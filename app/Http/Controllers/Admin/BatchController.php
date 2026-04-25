<?php

namespace App\Http\Controllers\Admin;

use App\Events\SlipsPublished;
use App\Http\Resources\BatchStatResource;
use App\Http\Requests\UpdateBatchRequest;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Batch;
use App\Models\AttendanceScan;
use App\Models\Project;
use App\Models\TestCenter;
use App\Services\RollNumberService;
use App\Services\SmsService;
use App\Services\AllocationStatsService;
use App\Models\ExamRollno;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Enums\ApplicationStatus;

class BatchController extends Controller
{
    public function __construct(
        private RollNumberService      $rollNumbers,
        private SmsService             $sms,
        private AllocationStatsService $statsService,
        private \App\Services\DigitalRepositoryService $repository,
    ) {}

    public function index()
    {
        $batchesPaginator = Batch::with(['project', 'center.city'])
            ->orderBy('is_ready', 'asc')
            ->orderBy('test_date', 'desc')
            ->paginate(50);
            
        $groupedBatches = collect($batchesPaginator->items())
            ->groupBy(fn ($b) => $b->is_ready ? 'published' : 'draft')
            ->map(fn ($group) => $group->groupBy(fn ($b) => $b->center->name));

        $projects = Project::whereHas('batches')->orderBy('name')->get();
        
        return view('admin.batches.index', compact('groupedBatches', 'batchesPaginator', 'projects'));
    }

    public function create()
    {
        $projects = Project::with('jobs')->where('status', 'open')->get();
        $centers  = TestCenter::with('city')->where('is_active', true)->get();
        return view('admin.batches.create', compact('projects', 'centers'));
    }

    public function store(\App\Http\Requests\StoreBatchRequest $request)
    {
        $data = $request->validated();

        $testDateNormalized = Carbon::parse($data['test_date'])->toDateString();
        $data['test_date']  = $testDateNormalized;

        $centerIds = array_unique($data['center_ids'] ?? []);
        // Enforce physical priority order locally configured by admins for cascaded fill rates
        $centerIds = TestCenter::whereIn('id', $centerIds)
            ->orderBy('city_id')
            ->orderBy('priority_order')
            ->pluck('id')
            ->toArray();

        $totalAllocated = 0;
        $batchIds = [];

        $unallocatedTarget = (int) $data['count_to_allocate'];

        // Preload centers to eliminate N+1 query overhead (Â§4.1)
        $centers = TestCenter::whereIn('id', $centerIds)->get()->keyBy('id');

        DB::beginTransaction();
        try {
            foreach ($centerIds as $centerId) {
                if ($unallocatedTarget <= 0) {
                    break;
                }

                $center = $centers->get($centerId);
                if (!$center) continue;

                // 2. Conflict Detection (Same center, same date, overlapping time)
                // New logic: Check if (start < current_end AND end > current_start)
                $startTime = $data['start_time'];
                $duration  = (int) ($data['duration_minutes'] ?? 240); 
                $endTime   = Carbon::parse($startTime)->addMinutes($duration)->format('H:i:s');

                $conflict = Batch::where('center_id', $centerId)
                    ->where('test_date', $testDateNormalized)
                    ->where(function($q) use ($startTime, $endTime) {
                         // Improved overlap detection [M5]
                         // Logic: (StartA < EndB) AND (EndA > StartB)
                         $q->where('start_time', '<', $endTime)
                           // PostgreSQL/MySQL COALESCE to handle legacy rows where duration_minutes is NULL
                           ->whereRaw('DATE_ADD(start_time, INTERVAL COALESCE(duration_minutes, 240) MINUTE) > ?', [$startTime]);
                    })
                    ->exists();

                if ($conflict) {
                    throw new \Exception("A session is already scheduled at '{$center->name}' on this date and time.");
                }

                $batchData = array_diff_key($data, array_flip(['job_ids', 'count_to_allocate', 'center_ids']));
                $batchData['center_id'] = $centerId;
                $batchData['created_by'] = Auth::id();
                $batchData['total_seats'] = $center->seating_capacity;
                
                $cityId = $center->city_id;
                $eligibleCount = Application::where('project_id', $batchData['project_id'])
                    ->where('status', \App\Enums\ApplicationStatus::FEE_PAID)
                    ->where('desired_test_city_id', $cityId)
                    ->whereDoesntHave('examRollno')
                    ->whereIn('job_id', $data['job_ids'])
                    ->count();

                if ($eligibleCount === 0) {
                    continue; 
                }

                $batch = Batch::create($batchData);
                $batchIds[] = $batch->id;

                // Cascade constraint: Never allocate more than remaining target, center's physical capacity, or available eligible candidates
                $allocCount = min($unallocatedTarget, $center->seating_capacity, $eligibleCount);
                $allocated = $this->rollNumbers->allocateBatch($batch, $allocCount, $data['job_ids']);
                
                $totalAllocated += $allocated;
                $unallocatedTarget -= $allocated; // Deduct from the remaining pool

                \App\Models\ActivityLog::log('allocate_seats', $batch, [
                    'count' => $allocated,
                    'job_ids' => $data['job_ids']
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('BatchController@store exception', [
                'message'    => $e->getMessage(),
                'class'      => get_class($e),
                'file'       => $e->getFile(),
                'line'       => $e->getLine(),
                'test_date'  => $data['test_date'] ?? 'not set',
                'project_id' => $data['project_id'] ?? 'not set',
                'center_ids' => $data['center_ids'] ?? [],
            ]);
            return back()->with('error', $e->getMessage())->withInput();
        }

        if (empty($batchIds)) {
            $msg = "No eligible candidates found. Check if candidates have 'Paid' status and if their 'Desired Test City' matches the selected centers.";
            return back()->with('error', $msg)->withInput();
        }

        return redirect()->route('admin.batches.index')
            ->with('success', "Scheduled " . count($batchIds) . " sessions. Successfully allocated {$totalAllocated} candidates across selected centers.");
    }

    /** AJAX endpoint to get real-time pending candidate counts */
    public function stats(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'test_date'  => 'nullable|date_format:Y-m-d|after:2000-01-01|before:2100-01-01',
        ]);

        $testDate = isset($data['test_date']) && $data['test_date'] !== ''
            ? Carbon::parse($data['test_date'])->toDateString()
            : null;

        $project = Project::with('jobs')->findOrFail($data['project_id']);
        $result  = $this->statsService->getStats($project, $testDate);

        return response()->json([
            'stats'               => BatchStatResource::collection($result['stats']),
            'job_stats'           => $result['job_stats'],
            'project_total'       => $result['project_total'],
            'project_unallocated' => $result['project_unallocated'], 
        ]);
    }

    public function show(Batch $batch)
    {
        $batch->load(['project', 'center.city', 'examRollnos.application.candidate.user', 'examRollnos.job']);
        $summary = $this->rollNumbers->batchSummary($batch);
        return view('admin.batches.show', compact('batch', 'summary'));
    }

    /** View Mega Session across all test centers within a city for a given date/time slot */
    public function groupShow($projectId, $testDate, $batchNumber)
    {
        $batches = Batch::with(['project', 'center.city', 'examRollnos.job', 'examRollnos.application.candidate.user'])
            ->where('project_id', $projectId)
            ->where('test_date', $testDate)
            ->where('batch_number', $batchNumber)
            ->get();

        if ($batches->isEmpty()) {
            abort(404, 'Mega session group not found');
        }

        // Aggregate statistics and candidate lists
        $project = $batches->first()->project;
        
        $totalSeats = $batches->sum('total_seats');
        $bookedSeats = $batches->sum('booked_seats');

        // Group roll numbers by Center and then by Job
        $groupedCandidates = collect();
        foreach ($batches as $batch) {
            $centerName = $batch->center->name . ' (' . $batch->center->city->name . ')';
            $groupedCandidates[$centerName] = $batch->examRollnos->groupBy(function($roll) {
                return $roll->job->title;
            });
        }

        return view('admin.batches.group_show', compact(
            'batches', 'project', 'testDate', 'batchNumber', 'totalSeats', 'bookedSeats', 'groupedCandidates'
        ));
    }

    public function edit(Batch $batch)
    {
        $batch->load(['project', 'center.city']);
        $projects = Project::where('status', 'open')->get();
        $centers  = TestCenter::with('city')->where('is_active', true)->get();
        return view('admin.batches.edit', compact('batch', 'projects', 'centers'));

    }

    public function update(UpdateBatchRequest $request, Batch $batch)
    {
        abort_if($batch->results_published, 403, 'Cannot update a test session after results have been published.');
        $data = $request->validated();
        $batch->update($data);
        return back()->with('success', 'Test Session updated.');
    }

    public function destroy(Batch $batch) 
    { 
        if ($batch->booked_seats > 0) {
            return back()->with('error', 'Cannot delete a test session that already has candidates allocated. Please unallocate or move candidates first.');
        }
        $batch->delete(); 
        return redirect()->route('admin.batches.index')->with('success', 'Test Session deleted.'); 
    }

    /** Mark all roll numbers in batch as slip_ready and SMS all candidates */
    public function markReady(Batch $batch)
    {
        abort_if($batch->results_published, 403, 'Cannot publish slips for a session that already has results declared.');
        $count = $this->rollNumbers->markBatchReady($batch);

        \App\Models\ActivityLog::log('publish_slips', $batch, ['count' => $count]);

        // Dispatch event for notifications
        event(new SlipsPublished($batch));

        return back()->with('success', "{$count} roll number slips marked ready. Candidates notified.");
    }

    /** Bulk mark all sessions in a group as ready */
    public function bulkPublish(Request $request)
    {
        $request->validate([
            'batch_ids' => 'required|array',
            'batch_ids.*' => 'exists:batches,id'
        ]);

        $count = 0;
        $allRollsCount = 0;

        DB::transaction(function() use ($request, &$count, &$allRollsCount) {
            $batches = Batch::whereIn('id', $request->batch_ids)
                ->where('is_ready', false)
                ->where('results_published', false)
                ->get();

            foreach ($batches as $batch) {
                $allocated = $this->rollNumbers->markBatchReady($batch);
                $allRollsCount += $allocated;
                
                \App\Models\ActivityLog::log('publish_slips', $batch, ['count' => $allocated]);
                event(new SlipsPublished($batch));
                $count++;
            }
        });

        if ($count === 0) {
            return back()->with('error', 'No eligible sessions found to publish.');
        }

        return back()->with('success', "Successfully published {$count} sessions and notified {$allRollsCount} candidates.");
    }

    /** Generic handler for bulk printing multiple batches (Slips, Sheets, or OMR) */
    public function bulkPrintGroup(Request $request)
    {
        $request->validate([
            'batch_ids' => 'required|array',
            'batch_ids.*' => 'exists:batches,id',
            'type' => 'required|in:slips,attendance,omr'
        ]);

        ini_set('memory_limit', '2G');
        set_time_limit(600);

        $batches = Batch::whereIn('id', $request->batch_ids)->orderBy('id')->get();
        if ($batches->isEmpty()) return back()->with('error', 'No sessions selected.');

        // Use the first batch for basic context (Project/Center)
        $batch = $batches->first();
        $batch->load(['project', 'center.city']);

        // Collection of all roll numbers across all requested batches
        $roster = ExamRollno::whereIn('batch_id', $request->batch_ids)
            ->with(['application.candidate.user', 'job.project', 'center.city', 'batch'])
            ->orderBy('batch_id')
            ->orderBy('roll_no')
            ->get();

        if ($roster->isEmpty()) return back()->with('error', 'No candidates found in selected sessions.');

        switch ($request->type) {
            case 'slips':
                $view = 'pdf.bulk-slips';
                $name = "Group_Slips_" . now()->format('His');
                break;
            case 'attendance':
                $view = 'pdf.attendance-sheet';
                $name = "Group_Attendance_" . now()->format('His');
                break;
            case 'omr':
                $view = 'pdf.answer-sheets';
                $name = "Group_OMR_" . now()->format('His');
                break;
        }

        $pdf = Pdf::loadView($view, compact('batch', 'roster'))->setPaper('a4', 'portrait');
        return $pdf->stream("{$name}.pdf");
    }

    /** Printable batch summary sheet (Image 1 equivalent) */
    public function summary(Batch $batch)
    {
        $batch->load(['project', 'center.city']);
        $summary = $this->rollNumbers->batchSummary($batch);
        return view('admin.batches.summary-print', compact('batch', 'summary'));
    }


    /** Printable attendance sheet for this batch (Image 2 equivalent) */
    public function attendanceSheet(Batch $batch)
    {
        if ($batch->booked_seats == 0) {
            return back()->with('error', 'Cannot generate Attendance Sheet for an empty batch.');
        }

        // Technical Guards for Bulk Generation
        ini_set('memory_limit', '1G');
        set_time_limit(300);

        try {
            $batch->load(['project', 'center.city']);
            $roster = $this->rollNumbers->batchRoster($batch);
            
            $pdf = Pdf::loadView('pdf.attendance', compact('batch', 'roster'))->setPaper('a4', 'portrait');
            
            // Save to new hierarchical repository
            $this->repository->saveAttendanceSheet($batch, $pdf->output());
            
            return $pdf->stream("Attendance_{$batch->center->tcid}_{$batch->test_date->format('Ymd')}.pdf");
        } catch (\Exception $e) {
            \Log::error("Attendance Sheet Generation Failed: " . $e->getMessage());
            return back()->with('error', 'Document generation failed: ' . $e->getMessage());
        }
    }

    /** Professional recruitment-style printable Answer Sheets for all candidates in batch */
    public function answerSheets(Batch $batch)
    {
        if ($batch->booked_seats > 750) {
            return back()->with('error', 'Batch too large for bulk generation (Max 750). Please download in smaller sessions.');
        }

        // Technical Guards for Bulk Generation
        ini_set('memory_limit', '1G');
        set_time_limit(300);

        try {
            $batch->load(['project.jobs', 'center.city']);
            // Get all roll numbers in one flat list for bulk PDF
            $roster = ExamRollno::where('batch_id', $batch->id)
                ->with(['application.candidate.user', 'job'])
                ->orderBy('roll_no')
                ->get();

            $pdf = Pdf::loadView('pdf.answer-sheet', compact('batch', 'roster'))->setPaper('a4', 'portrait');
            
            // Save to new hierarchical repository
            $this->repository->saveSummary($batch, 'AnswerSheets', $pdf->output());
            
            return $pdf->stream("AnswerSheets_{$batch->center->tcid}.pdf");
        } catch (\Exception $e) {
            \Log::error("Answer Sheet Generation Failed: " . $e->getMessage());
            return back()->with('error', 'Bulk answer sheet generation failed: ' . $e->getMessage());
        }
    }

    /** Bulk Print Roll Number Slips for the entire batch */
    public function bulkSlips(Batch $batch)
    {
        if ($batch->booked_seats > 750) {
            return back()->with('error', 'Batch too large for bulk generation (Max 750). Please download in smaller sessions.');
        }

        // Technical Guards
        ini_set('memory_limit', '1G');
        set_time_limit(300);

        try {
            $batch->load(['project', 'center.city']);
            $roster = ExamRollno::where('batch_id', $batch->id)
                ->with(['application.candidate.user', 'job.project', 'center.city', 'batch'])
                ->orderBy('roll_no')
                ->get();

            $pdf = Pdf::loadView('pdf.bulk-slips', compact('batch', 'roster'))->setPaper('a4', 'portrait');
            
            // Save to new hierarchical repository
            $this->repository->saveSummary($batch, 'BulkSlips', $pdf->output());
            
            return $pdf->stream("RollNoSlips_{$batch->center->tcid}.pdf");
        } catch (\Exception $e) {
            \Log::error("Bulk Slips Generation Failed: " . $e->getMessage());
            return back()->with('error', 'Bulk slip generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Print sticker labels for a roll number range.
     * Accepts: roll_from, roll_to, batch_id (optional, for context)
     * Each sticker shows Roll No + Post Name, 3 per row Ã— 10 per page.
     */
    public function printStickers(Request $request)
    {
        $request->validate([
            'roll_from' => 'required|string',
            'roll_to'   => 'required|string',
            'batch_id'  => 'nullable|exists:batches,id',
        ]);

        ini_set('memory_limit', '1G');
        set_time_limit(300);

        $query = ExamRollno::with(['job', 'batch.center.city', 'batch.project'])
            ->whereBetween('roll_no', [$request->roll_from, $request->roll_to])
            ->orderBy('roll_no');

        if ($request->batch_id) {
            $query->where('batch_id', $request->batch_id);
        }

        $roster = $query->get();

        if ($roster->isEmpty()) {
            return redirect()->route('admin.batches.index')->with('error', 'No roll numbers found in the given range.');
        }

        // Use context from first record
        $firstBatch   = $roster->first()->batch;
        $centerName   = $firstBatch->center->name ?? 'Unknown Center';
        $centerCity   = $firstBatch->center->city->name ?? '';
        $batchNumber  = $firstBatch->batch_number ?? 'â€”';
        $projectName  = $firstBatch->project->name ?? '';

        $pdf = Pdf::loadView('pdf.stickers', compact(
            'roster', 'centerName', 'centerCity', 'batchNumber', 'projectName'
        ))->setPaper('a4', 'portrait');

        $safeName = "Stickers_{$request->roll_from}-{$request->roll_to}";
        return $pdf->stream("{$safeName}.pdf");
    }

    /**
     * AJAX endpoint to check if roll numbers exist in range
     */
    public function checkStickers(Request $request)
    {
        $count = ExamRollno::whereBetween('roll_no', [$request->roll_from, $request->roll_to])
            ->when($request->batch_id, fn($q) => $q->where('batch_id', $request->batch_id))
            ->count();

        return response()->json(['count' => $count]);
    }


    /** Show attendance management (upload scans + mark appeared/absent) */
    public function attendance(Batch $batch)
    {
        if ($batch->results_published) {
            return redirect()->route('admin.batches.show', $batch)->with('error', 'Attendance and marks are locked for this session as results have been published.');
        }
        $batch->load(['center.city', 'project', 'scans', 'examRollnos.application.candidate.user', 'examRollnos.job']);
        return view('admin.batches.attendance', compact('batch'));
    }

    /** Upload scanned attendance sheet image */
    public function uploadScan(Request $request, Batch $batch)
    {
        if ($batch->results_published) {
            return back()->with('error', 'Operation denied. Results for this session have already been published.');
        }
        $request->validate([
            'scans.*'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'scans'     => 'required|array|min:1',
        ]);

        $uploaded = 0;
        foreach ($request->file('scans') as $i => $file) {
            // New Hierarchical Structure: attendance_scans/YYYY-MM-DD/project_ID/batch_ID/
            $dateFolder = $batch->test_date->toDateString();
            $targetPath = "attendance_scans/{$dateFolder}/project_{$batch->project_id}/batch_{$batch->id}";
            
            $path = $file->store($targetPath, 'public');
            
            AttendanceScan::create([
                'batch_id'    => $batch->id,
                'project_id'  => $batch->project_id,
                'center_id'   => $batch->center_id,
                'test_date'   => $batch->test_date->toDateString(),
                'file_path'   => $path,
                'page_number' => $i + 1,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
            ]);
            $uploaded++;
        }

        return back()->with('success', "{$uploaded} scan(s) uploaded.");
    }

    /** Mark candidates as appeared or absent with bulk update and validation */
    public function markAttendance(Request $request, Batch $batch)
    {
        if ($batch->results_published) {
            return back()->with('error', 'Operation denied. Results for this session have already been published.');
        }
        $data = $request->validate([
            'attendance'   => 'required|array',
            'attendance.*' => 'required|in:appeared,absent',
        ]);

        $appearedIds = [];
        $absentIds   = [];

        foreach ($data['attendance'] as $appId => $status) {
            if ($status === 'appeared') {
                $appearedIds[] = $appId;
            } else {
                $absentIds[] = $appId;
            }
        }

        // Use bulk updates â€” scope via examRollno since applications has no batch_id column
        if (!empty($appearedIds)) {
            Application::whereIn('id', $appearedIds)
                ->whereHas('examRollno', fn($q) => $q->where('batch_id', $batch->id))
                ->whereIn('status', [ApplicationStatus::SCHEDULED, ApplicationStatus::ABSENT])
                ->update(['status' => ApplicationStatus::APPEARED]);
        }

        if (!empty($absentIds)) {
            Application::whereIn('id', $absentIds)
                ->whereHas('examRollno', fn($q) => $q->where('batch_id', $batch->id))
                ->whereIn('status', [ApplicationStatus::SCHEDULED, ApplicationStatus::APPEARED])
                ->update(['status' => ApplicationStatus::ABSENT]);
        }

        return back()->with('success', 'Attendance tracking completed for validated candidates.');
    }

    public function toggleResults(Batch $batch)
    {
        $this->authorize('publish results');
        $batch->update(['results_published' => !$batch->results_published]);
        $status = $batch->results_published ? 'published' : 'unpublished';
        return back()->with('success', "Results for this session are now {$status}.");
    }

    /** AJAX: Get centers and batches for a specific project (Print Portal) */
    public function centersForProject(Project $project)
    {
        $centers = TestCenter::whereHas('batches', function ($q) use ($project) {
            $q->where('project_id', $project->id);
        })
        ->with(['city', 'batches' => function ($q) use ($project) {
            $q->where('project_id', $project->id)->orderBy('batch_number');
        }])
        ->get();

        $projectBounds = ExamRollno::whereHas('batch', function($q) use ($project) {
                $q->where('project_id', $project->id);
            })
            ->selectRaw('MIN(roll_no) as min_roll, MAX(roll_no) as max_roll')
            ->first();

        $data = $centers->map(function ($center) {
            return [
                'id' => $center->id,
                'name' => $center->name,
                'city' => $center->city?->name ?? 'N/A',
                'batches' => $center->batches->map(function ($batch) {
                    $batchBounds = ExamRollno::where('batch_id', $batch->id)
                        ->selectRaw('MIN(roll_no) as min_roll, MAX(roll_no) as max_roll')
                        ->first();
                        
                    return [
                        'id' => $batch->id,
                        'batch_number' => $batch->batch_number,
                        'booked_seats' => $batch->booked_seats,
                        'min_roll' => $batchBounds->min_roll ?? '',
                        'max_roll' => $batchBounds->max_roll ?? '',
                        'slips_url' => route('admin.batches.bulk-slips', $batch),
                        'attendance_url' => route('admin.batches.attendance-sheet', $batch),
                        'omr_url' => route('admin.batches.answer-sheets', $batch),
                    ];
                })
            ];
        });

        return response()->json([
            'centers' => $data,
            'min_roll' => $projectBounds->min_roll ?? '',
            'max_roll' => $projectBounds->max_roll ?? '',
        ]);
    }

    /** Export Master Candidate CSV (Point 1 of Client Requests) */
    public function export(Project $project)
    {
        $filename = "{$project->org_name}_Candidate_List_All_Centers_" . now()->format('Ymd_His') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // We use a stream callback for high-performance direct download with no memory bloat.
        $callback = function () use ($project) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for proper UTF-8 Excel support
            fputs($file, "\xEF\xBB\xBF");
            
            // Strict columns requested by client
            fputcsv($file, [
                'S#', 'Roll No', 'Name', 'FatherName', 'CNIC', 'Post_Name', 
                'TC ID', 'Test_Center', 'Test_City', 'Batch', 'Test_Date', 'Reporting_time', 
                'Test_time', 'Department'
            ]);

            // Query safely joining relationships
            $rollnos = ExamRollno::whereHas('batch', function($q) use ($project) {
                    $q->where('project_id', $project->id);
                })
                ->with([
                    'application.candidate.user', 
                    'application.candidate',
                    'job', 
                    'batch.center.city'
                ])
                ->orderBy('roll_no')
                ->cursor(); // Cursor prevents memory exhaust

            $counter = 1;

            foreach ($rollnos as $roll) {
                // Safety null checks
                $user = $roll->application->candidate->user ?? null;
                $candidate = $roll->application->candidate ?? null;
                $job = $roll->job ?? null;
                $batch = $roll->batch ?? null;
                $center = $batch->center ?? null;

                // Format times exactly as required (e.g. 8:00 AM)
                $repTime = Carbon::parse($batch->reporting_time)->format('g:i A');
                $testTime = Carbon::parse($batch->start_time)->format('g:i A');
                $testDate = Carbon::parse($batch->test_date)->format('jS F, Y');

                fputcsv($file, [
                    $counter++, // S#
                    $roll->roll_no,
                    $user->full_name ?? '',
                    $candidate->father_name ?? '',
                    $user->cnic ?? '',
                    $job->title ?? '',
                    $center->id ?? '',
                    $center->name ?? '',
                    $center->city->name ?? '',
                    'Batch-' . ($batch->batch_number ?? ''),
                    $testDate,
                    $repTime,
                    $testTime,
                    $job->department ?: $project->org_name
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}


