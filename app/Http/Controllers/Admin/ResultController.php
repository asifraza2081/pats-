<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\PatsJob;
use App\Models\Project;
use App\Models\Result;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ResultController extends Controller
{
    public function __construct(private SmsService $sms) {}

    public function index()
    {
        $results = Result::with(['application.candidate.user', 'application.job.project'])
            ->latest()->paginate(25);
        return view('admin.results.index', compact('results'));
    }

    public function showUpload()
    {
        $projects = Project::where('status', 'open')->orWhere('status', 'closed')->latest()->get();
        return view('admin.results.upload', compact('projects'));
    }

    public function upload(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'file'       => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'scans.*'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $projectId = $request->project_id;
        $rows = $this->parseFile($request->file('file'));

        // Store scans in temporary storage for preview matching
        $scanPaths = [];
        if ($request->hasFile('scans')) {
            foreach ($request->file('scans') as $scan) {
                // Filename should be roll_number.ext
                $name = pathinfo($scan->getClientOriginalName(), PATHINFO_FILENAME);
                $path = $scan->store('temp_scans', 'public');
                $scanPaths[$name] = $path;
            }
        }

        $preview  = [];
        $warnings = [];

        foreach ($rows as $i => $row) {
            $roll = trim($row['roll_number'] ?? $row[0] ?? '');
            if (!$roll) continue;

            $application = Application::whereHas('examRollno', fn($q) => $q->where('roll_no', $roll))
                ->where('project_id', $projectId)
                ->with(['candidate.user', 'job', 'examRollno'])
                ->first();

            if (!$application) {
                $warnings[] = "Row " . ($i + 2) . ": Roll No {$roll} not found in project.";
                continue;
            }

            $preview[] = [
                'application'   => $application,
                'roll_number'   => $roll,
                'score'         => floatval($row['score'] ?? $row[1] ?? 0),
                'total_marks'   => floatval($row['total_marks'] ?? $row[2] ?? 100),
                'result_status' => strtolower(trim($row['status'] ?? $row[3] ?? 'fail')),
                'scan_path'     => $scanPaths[$roll] ?? null,
            ];
        }

        session(['result_preview' => $preview, 'result_project_id' => $projectId]);
        return view('admin.results.preview', compact('preview', 'warnings', 'projectId'));
    }

    public function publish(Request $request, Project $project)
    {
        $preview = session('result_preview', []);
        if (empty($preview)) return back()->with('error', 'No result data in session. Please re-upload.');

        DB::transaction(function () use ($preview, $project) {
            // Get all appeared counts for percentile calc
            $jobAppeared = [];

            foreach ($preview as $row) {
                $application  = Application::findOrFail($row['application']['id']);
                $score        = $row['score'];
                $totalMarks   = $row['total_marks'];
                $resultStatus = in_array($row['result_status'], ['pass','fail','absent','withheld'])
                    ? $row['result_status'] : 'fail';
                $percentage   = $totalMarks > 0 ? round(($score / $totalMarks) * 100, 2) : 0;

                Result::updateOrCreate(
                    ['application_id' => $application->id],
                    [
                        'roll_number'         => $row['roll_number'],
                        'score'               => $score,
                        'total_marks'         => $totalMarks,
                        'percentage'          => $percentage,
                        'result_status'       => $resultStatus,
                        'uploaded_by'         => Auth::id(),
                        'published_at'        => now(),
                        'scanned_sheet_path'  => $row['scan_path'],
                    ]
                );

                $application->update(['status' => 'result_declared']);
                $jobAppeared[$application->job_id][] = ['app_id' => $application->id, 'pct' => $percentage];
            }

            // Compute percentiles per job category
            foreach ($jobAppeared as $jobId => $entries) {
                usort($entries, fn($a, $b) => $a['pct'] <=> $b['pct']);
                $total = count($entries);
                foreach ($entries as $rank => $entry) {
                    Result::where('application_id', $entry['app_id'])
                        ->update(['percentile' => round((($rank + 1) / $total) * 100, 2)]);
                }
            }

            $project->update(['status' => 'result_declared']);

            \App\Models\ActivityLog::log('publish_results', $project, [
                'count' => count($preview),
                'job_ids' => array_keys($jobAppeared)
            ]);
        });

        // Notify candidates
        $applications = Application::where('status', 'result_declared')
            ->where('project_id', $project->id)
            ->with(['candidate.user', 'job'])
            ->get();

        foreach ($applications as $app) {
            $user = $app->candidate->user;
            $this->sms->send($user->phone, "PATS: Results for {$app->job->title} are published. Log in to view.", $user->id);
        }

        session()->forget(['result_preview', 'result_project_id']);
        return redirect()->route('admin.results.index')->with('success', 'Results published and candidates notified.');
    }

    public function show(Application $app)
    {
        $result = $app->result;
        abort_if(!$result, 404);
        $app->load(['candidate.user', 'job.project', 'examRollno.center']);
        return view('admin.results.show', compact('app', 'result'));
    }

    private function parseFile($file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        if ($ext === 'csv') {
            $rows = [];
            $handle = fopen($file->getPathname(), 'r');
            $headers = fgetcsv($handle);
            $headers = array_map('strtolower', array_map('trim', $headers));
            while ($row = fgetcsv($handle)) {
                $rows[] = array_combine($headers, array_pad($row, count($headers), ''));
            }
            fclose($handle);
            return $rows;
        }
        // Excel
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        $headers = array_map('strtolower', array_map('trim', array_shift($sheet)));
        return array_map(fn($r) => array_combine($headers, array_pad($r, count($headers), '')), $sheet);
    }
}
