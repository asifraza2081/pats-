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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BatchController extends Controller
{
    public function __construct(
        private RollNumberService      $rollNumbers,
        private SmsService             $sms,
        private AllocationStatsService $statsService,
    ) {}

    public function index()
    {
        $batches = Batch::with(['project', 'center.city'])->latest()->paginate(25);
        $projects = Project::whereHas('batches')->orderBy('name')->get();
        return view('admin.batches.index', compact('batches', 'projects'));
    }

    public function create()
    {
        $projects = Project::with('jobs')->where('status', 'open')->get();
        $centers  = TestCenter::with('city')->where('is_active', true)->get();
        return view('admin.batches.create', compact('projects', 'centers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'     => 'required|exists:projects,id',
            'job_ids'        => 'required|array|min:1',
            'job_ids.*'      => 'required|exists:pats_jobs,id',
            'center_ids'     => 'required|array|min:1',
            'center_ids.*'   => 'required|exists:test_centers,id',
            'batch_number'   => 'required|integer|min:1',
            'test_date'      => 'required|date_format:Y-m-d|after_or_equal:today|before:2100-01-01',
            'reporting_time' => 'required',
            'start_time'     => 'required|after:reporting_time',
            'count_to_allocate' => 'required|integer|min:1',
            'envelope_size'  => 'required|integer|min:10|max:100',
        ]);

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

        DB::beginTransaction();
        try {
            foreach ($centerIds as $centerId) {
                if ($unallocatedTarget <= 0) {
                    break; // All targets have been distributed across centers
                }

                $center = TestCenter::findOrFail($centerId);

                // 2. Conflict Detection (Same center, same date, overlapping time)
                // New logic: Check if (start < current_end AND end > current_start)
                // We use reporting_time to start_time + 4 hours (estimated) for overlap-safety
                $startTime = $data['start_time'];
                $endTime   = Carbon::parse($startTime)->addHours(4)->format('H:i:s');

                $conflict = Batch::where('center_id', $centerId)
                    ->where('test_date', $testDateNormalized)
                    ->where(function($q) use ($startTime, $endTime) {
                         // Improved overlap detection [M5]
                         // Logic: (StartA < EndB) AND (EndA > StartB)
                         // NOTE: We assume a 4-hour window per batch as duration is not stored in DB.
                         $q->where('start_time', '<', $endTime)
                           ->whereRaw('DATE_ADD(start_time, INTERVAL 4 HOUR) > ?', [$startTime]);
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
                    ->where('status', 'fee_paid')
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
        $batches = Batch::with(['center.city', 'examRollnos.job', 'examRollnos.application.candidate.user'])
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

    /** Printable batch summary sheet (Image 1 equivalent) */
    public function summary(Batch $batch)
    {
        $batch->load(['project', 'center.city']);
        $summary = $this->rollNumbers->batchSummary($batch);
        return view('admin.batches.summary-print', compact('batch', 'summary'));
    }

    /**
     * Internal helper to save PDF to a structured hierarchical repository
     */
    private function savePdfToHierarchy($pdf, $batch, $type)
    {
        $date = $batch->test_date->toDateString();
        $projectSlug = Str::slug($batch->project->name);
        $centerSlug = Str::slug($batch->center->name);
        
        $directory = "exports/{$date}/{$projectSlug}/{$centerSlug}";
        $filename = "{$type}_B{$batch->id}_" . now()->format('His') . ".pdf";
        $path = "{$directory}/{$filename}";
        
        Storage::disk('public')->put($path, $pdf->output());
        return $path;
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
            
            // Save to hierarchy
            $this->savePdfToHierarchy($pdf, $batch, 'attendance');
            
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
            
            // Save to hierarchy
            $this->savePdfToHierarchy($pdf, $batch, 'answer_sheets');
            
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
            
            // Save to hierarchy
            $this->savePdfToHierarchy($pdf, $batch, 'bulk_slips');
            
            return $pdf->stream("RollNoSlips_{$batch->center->tcid}.pdf");
        } catch (\Exception $e) {
            \Log::error("Bulk Slips Generation Failed: " . $e->getMessage());
            return back()->with('error', 'Bulk slip generation failed: ' . $e->getMessage());
        }
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

        // Use bulk updates — scope via examRollno since applications has no batch_id column
        if (!empty($appearedIds)) {
            Application::whereIn('id', $appearedIds)
                ->whereHas('examRollno', fn($q) => $q->where('batch_id', $batch->id))
                ->whereIn('status', ['scheduled', 'absent'])
                ->update(['status' => 'appeared']);
        }

        if (!empty($absentIds)) {
            Application::whereIn('id', $absentIds)
                ->whereHas('examRollno', fn($q) => $q->where('batch_id', $batch->id))
                ->whereIn('status', ['scheduled', 'appeared'])
                ->update(['status' => 'absent']);
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

        $data = $centers->map(function ($center) {
            return [
                'id' => $center->id,
                'name' => $center->name,
                'city' => $center->city->name,
                'batches' => $center->batches->map(function ($batch) {
                    return [
                        'id' => $batch->id,
                        'batch_number' => $batch->batch_number,
                        'booked_seats' => $batch->booked_seats,
                        'slips_url' => route('admin.batches.bulk-slips', $batch),
                        'attendance_url' => route('admin.batches.attendance-sheet', $batch),
                        'omr_url' => route('admin.batches.answer-sheets', $batch),
                    ];
                })
            ];
        });

        return response()->json($data);
    }
}
