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

        foreach ($data['center_ids'] as $centerId) {
            // Create a distinct batch for each center
            $batchData = $request->except(['job_ids', 'count_to_allocate', 'center_ids']);
            $batchData['center_id'] = $centerId;
            $batchData['created_by'] = Auth::id();
            
            $batch = Batch::create($batchData);
            $batchIds[] = $batch->id;

            // Run seat allocation for this specific center
            // Note: We allocate the *same* count per center or we could divide. 
            // User likely wants 'total seats' per center.
            $allocated = $this->rollNumbers->allocateBatch($batch, $data['count_to_allocate'], $data['job_ids']);
            $totalAllocated += $allocated;

            \App\Models\ActivityLog::log('allocate_seats', $batch, [
                'count' => $allocated,
                'job_ids' => $data['job_ids']
            ]);
        }

        return redirect()->route('admin.batches.index')
            ->with('success', "Scheduled " . count($batchIds) . " sessions. Successfully allocated {$totalAllocated} candidates across selected centers.");
    }

    /** AJAX endpoint to get real-time pending candidate counts */
    public function stats(Request $request)
    {
        $projectId = $request->project_id;
        if (!$projectId) return response()->json([]);

        // Get unassigned, paid candidates grouped by job and city
        $stats = Application::where('applications.project_id', $projectId)
            ->where('applications.status', 'fee_paid')
            ->whereDoesntHave('examRollno')
            ->join('pats_jobs', 'applications.job_id', '=', 'pats_jobs.id')
            ->join('cities', 'applications.desired_test_city_id', '=', 'cities.id')
            ->selectRaw('cities.id as city_id, cities.name as city_name, pats_jobs.id as job_id, pats_jobs.title as job_title, count(*) as pending_count')
            ->groupBy('cities.id', 'cities.name', 'pats_jobs.id', 'pats_jobs.title')
            ->get();

        return response()->json(['stats' => $stats]);
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
        $batch->load(['project', 'center.city']);
        // Flatten roster for attendance sheet
        $roster = $this->rollNumbers->batchRoster($batch);
        $pdf = Pdf::loadView('pdf.attendance', compact('batch', 'roster'))->setPaper('a4', 'portrait');
        return $pdf->stream("Attendance_{$batch->center->tcid}_{$batch->test_date->format('Ymd')}.pdf");
    }

    /** NTS-style printable Answer Sheets for all candidates in batch */
    public function answerSheets(Batch $batch)
    {
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
}
