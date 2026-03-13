<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\Batch;
use App\Models\AttendanceScan;
use App\Models\Project;
use App\Models\TestCenter;
use App\Services\RollNumberService;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BatchController extends Controller
{
    public function __construct(
        private RollNumberService $rollNumbers,
        private SmsService        $sms,
    ) {}

    public function index()
    {
        $batches = Batch::with(['project', 'center.city'])->latest()->paginate(25);
        return view('admin.batches.index', compact('batches'));
    }

    public function create()
    {
        $projects = Project::with('jobs')->where('status', 'open')->get();
        $centers  = TestCenter::where('is_active', true)->get();
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
            'test_date'      => 'required|date',
            'reporting_time' => 'required',
            'start_time'     => 'required|after:reporting_time',
            'total_seats'    => 'required|integer|min:1',
            'count_to_allocate' => 'required|integer|min:1|max:'.$request->total_seats,
            'envelope_size'  => 'required|integer|min:10|max:100',
        ]);

        $totalAllocated = 0;
        $batchIds = [];

        try {
            DB::beginTransaction();

            foreach ($data['center_ids'] as $centerId) {
                $center = TestCenter::findOrFail($centerId);
                
                // 1. Physical Capacity Check
                if ($data['total_seats'] > $center->seating_capacity) {
                    throw new \Exception("Center '{$center->name}' only has {$center->seating_capacity} seats, but {$data['total_seats']} were requested.");
                }

                // 2. Conflict Detection (Same center, same date, overlapping time)
                // New logic: Check if (start < current_end AND end > current_start)
                // We use reporting_time to start_time + 4 hours (estimated) for overlap-safety
                $startTime = $data['start_time'];
                $endTime   = date('H:i:s', strtotime($startTime . ' + 4 hours'));

                $conflict = Batch::where('center_id', $centerId)
                    ->where('test_date', $data['test_date'])
                    ->where(function($q) use ($startTime, $endTime) {
                        $q->where(function($sq) use ($startTime, $endTime) {
                             $sq->where('start_time', '<', $endTime)
                                ->where('start_time', '>=', $startTime);
                        })
                        ->orWhere(function($sq) use ($startTime, $endTime) {
                             // This is a rough window check, ideally we'd have a fixed duration or end_time in DB
                             $sq->where('reporting_time', '<', $endTime)
                                ->where('reporting_time', '>=', $startTime);
                        });
                    })
                    ->exists();

                if ($conflict) {
                    throw new \Exception("A session is already scheduled at '{$center->name}' on this date and time.");
                }

                $batchData = $request->except(['job_ids', 'count_to_allocate', 'center_ids']);
                $batchData['center_id'] = $centerId;
                $batchData['created_by'] = Auth::id();
                
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

                $allocCount = min($data['count_to_allocate'], $eligibleCount);
                $allocated = $this->rollNumbers->allocateBatch($batch, $allocCount, $data['job_ids']);
                $totalAllocated += $allocated;

                \App\Models\ActivityLog::log('allocate_seats', $batch, [
                    'count' => $allocated,
                    'job_ids' => $data['job_ids']
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage())->withInput();
        }

        if (empty($batchIds)) {
            return back()->with('error', 'No eligible candidates matching the criteria were found for the selected centers. No batches created.')->withInput();
        }

        return redirect()->route('admin.batches.index')
            ->with('success', "Scheduled " . count($batchIds) . " sessions. Successfully allocated {$totalAllocated} candidates across selected centers.");
    }

    /** AJAX endpoint to get real-time pending candidate counts */
    public function stats(Request $request)
    {
        $projectId = $request->project_id;
        $testDate = $request->test_date; // Optional: for CCP
        if (!$projectId) return response()->json([]);

        $project = Project::with('jobs')->findOrFail($projectId);

        // Base Query with AEE (Eligibility) Hardening
        $baseQuery = Application::where('applications.project_id', $projectId)
            ->where('applications.status', 'fee_paid')
            ->whereDoesntHave('examRollno')
            ->join('candidates', 'applications.candidate_id', '=', 'candidates.id')
            ->join('pats_jobs', 'applications.job_id', '=', 'pats_jobs.id')
            // [AEE] Eligibility enforcement
            ->whereRaw('pats_jobs.min_degree_level <= (
                SELECT MAX(degree_level) FROM education_histories 
                WHERE candidate_id = candidates.id 
                AND passing_year <= YEAR(CURDATE())
            )')
            ->whereRaw('TIMESTAMPDIFF(YEAR, candidates.dob, CURDATE()) BETWEEN pats_jobs.age_min AND pats_jobs.age_max');

        // [CCP] Collision Prevention (if date provided)
        if ($testDate) {
            $baseQuery->whereDoesntHave('candidate.applications.examRollno', function($q) use ($testDate) {
                $q->where('test_date', $testDate);
            });
        }

        // Clone for breakdown
        $stats = (clone $baseQuery)
            ->join('cities', 'applications.desired_test_city_id', '=', 'cities.id')
            ->selectRaw('cities.id as city_id, cities.name as city_name, pats_jobs.id as job_id, pats_jobs.title as job_title, count(*) as pending_count')
            ->groupBy('cities.id', 'cities.name', 'pats_jobs.id', 'pats_jobs.title')
            ->get();

        return response()->json([
            'stats' => $stats,
            'job_stats' => $project->jobs->map(function($j) use ($baseQuery) {
                return [
                    'id' => $j->id,
                    'pending' => (clone $baseQuery)->where('applications.job_id', $j->id)->count()
                ];
            }),
            'project_total' => (clone $baseQuery)->count(),
            'project_unallocated' => (clone $baseQuery)->count(), 
        ]);
    }

    public function show(Batch $batch)
    {
        $batch->load(['project', 'center.city', 'examRollnos.application.candidate.user', 'examRollnos.job']);
        $summary = $this->rollNumbers->batchSummary($batch);
        return view('admin.batches.show', compact('batch', 'summary'));
    }

    public function edit(Batch $batch)
    {
        $projects = Project::where('status', 'open')->get();
        $centers  = TestCenter::where('is_active', true)->get();
        return view('admin.batches.edit', compact('batch', 'projects', 'centers'));
    }

    public function update(Request $request, Batch $batch)
    {
        $data = $request->validate([
            'reporting_time' => 'required',
            'start_time'     => 'required',
            'total_seats'    => 'required|integer|min:1',
            'envelope_size'  => 'required|integer|min:10|max:100',
        ]);
        $batch->update($data);
        return back()->with('success', 'Test Session updated.');
    }

    public function destroy(Batch $batch) { $batch->delete(); return redirect()->route('admin.batches.index')->with('success', 'Test Session deleted.'); }

    /** Mark all roll numbers in batch as slip_ready and SMS all candidates */
    public function markReady(Batch $batch)
    {
        $count = $this->rollNumbers->markBatchReady($batch);

        \App\Models\ActivityLog::log('publish_slips', $batch, ['count' => $count]);

        // Queue SMS notifications
        $applications = $batch->applications()->with(['candidate.user', 'examRollno'])->get();
        foreach ($applications as $app) {
            if ($app->examRollno?->slip_ready) {
                $user = $app->candidate->user;
                $this->sms->send(
                    $user->phone,
                    "PATS: Your Roll Number Slip for {$app->job->title} is ready. Log in to download: " . url('/candidate/dashboard'),
                    $user->id
                );
            }
        }

        return back()->with('success', "{$count} roll number slips marked ready. Candidates notified.");
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
        $batch->load(['project', 'center.city']);
        // Flatten roster for attendance sheet
        $roster = $this->rollNumbers->batchRoster($batch);
        $pdf = Pdf::loadView('pdf.attendance', compact('batch', 'roster'))->setPaper('a4', 'portrait');
        return $pdf->stream("Attendance_{$batch->center->tcid}_{$batch->test_date->format('Ymd')}.pdf");
    }

    /** NTS-style printable Answer Sheets for all candidates in batch */
    public function answerSheets(Batch $batch)
    {
        if ($batch->booked_seats == 0) {
            return back()->with('error', 'Cannot generate Answer Sheets for an empty batch.');
        }
        $batch->load(['project.jobs', 'center.city']);
        // Get all roll numbers in one flat list for bulk PDF
        $roster = ExamRollno::where('batch_id', $batch->id)
            ->with(['application.candidate.user', 'job'])
            ->orderBy('roll_no')
            ->get();

        $pdf = Pdf::loadView('pdf.answer-sheet', compact('batch', 'roster'))->setPaper('a4', 'portrait');
        return $pdf->stream("AnswerSheets_{$batch->center->tcid}.pdf");
    }

    /** Show attendance management (upload scans + mark appeared/absent) */
    public function attendance(Batch $batch)
    {
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
            $path = $file->store("attendance_scans/batch_{$batch->id}", 'public');
            AttendanceScan::create([
                'batch_id'    => $batch->id,
                'file_path'   => $path,
                'page_number' => $i + 1,
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
            ]);
            $uploaded++;
        }

        return back()->with('success', "{$uploaded} scan(s) uploaded.");
    }

    /** Mark candidates as appeared or absent */
    public function markAttendance(Request $request, Batch $batch)
    {
        if ($batch->results_published) {
            return back()->with('error', 'Operation denied. Results for this session have already been published.');
        }
        $data = $request->validate([
            'attendance'   => 'required|array',
            'attendance.*' => 'required|in:appeared,absent',
        ]);

        foreach ($data['attendance'] as $appId => $status) {
            Application::where('id', $appId)
                ->where('batch_id', $batch->id)
                ->update(['status' => $status]);
        }

        return back()->with('success', 'Attendance tracking completed.');
    }

    public function toggleResults(Batch $batch)
    {
        $batch->update(['results_published' => !$batch->results_published]);
        $status = $batch->results_published ? 'published' : 'unpublished';
        return back()->with('success', "Results for this session are now {$status}.");
    }
}
