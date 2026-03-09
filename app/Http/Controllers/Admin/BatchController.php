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
        $batches = Batch::with(['project', 'center'])->latest()->paginate(20);
        return view('admin.batches.index', compact('batches'));
    }

    public function create()
    {
        $projects = Project::where('status', 'open')->get();
        $centers  = TestCenter::where('is_active', true)->get();
        return view('admin.batches.create', compact('projects', 'centers'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id'     => 'required|exists:projects,id',
            'center_id'      => 'required|exists:test_centers,id',
            'batch_number'   => 'required|integer|min:1',
            'test_date'      => 'required|date',
            'reporting_time' => 'required',
            'start_time'     => 'required|after:reporting_time',
            'total_seats'    => 'required|integer|min:1',
            'envelope_size'  => 'required|integer|min:10|max:100',
        ]);
        Batch::create($data);
        return redirect()->route('admin.batches.index')->with('success', 'Batch created.');
    }

    public function show(Batch $batch)
    {
        $batch->load(['project', 'center', 'applications.candidate.user', 'applications.job', 'applications.rollNumber']);
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
        return back()->with('success', 'Batch updated.');
    }

    public function destroy(Batch $batch) { $batch->delete(); return redirect()->route('admin.batches.index')->with('success', 'Batch deleted.'); }

    /** Mark all roll numbers in batch as slip_ready and SMS all candidates */
    public function markReady(Batch $batch)
    {
        $count = $this->rollNumbers->markBatchReady($batch);

        // Queue SMS notifications
        $applications = $batch->applications()->with(['candidate.user', 'rollNumber'])->get();
        foreach ($applications as $app) {
            if ($app->rollNumber?->slip_ready) {
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
        $batch->load(['project', 'center']);
        $summary = $this->rollNumbers->batchSummary($batch);
        return view('admin.batches.summary-print', compact('batch', 'summary'));
    }

    /** Printable attendance sheet for this batch (Image 2 equivalent) */
    public function attendanceSheet(Batch $batch)
    {
        $batch->load(['project', 'center']);
        $summary = $this->rollNumbers->batchSummary($batch);
        return view('admin.batches.attendance-print', compact('batch', 'summary'));
    }

    /** Show attendance management (upload scans + mark appeared/absent) */
    public function attendance(Batch $batch)
    {
        $batch->load(['center', 'project', 'scans', 'applications.candidate.user', 'applications.job', 'applications.rollNumber']);
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

    /** Mark candidates appeared or absent */
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

        return back()->with('success', 'Attendance marked successfully.');
    }
}
