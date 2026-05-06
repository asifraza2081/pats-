<?php

namespace App\Http\Controllers\Admin;

use App\Events\ResultsPublished;
use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\PatsJob;
use App\Models\Result;
use App\Services\SmsService;
use App\Enums\ProjectStatus;
use App\Enums\ApplicationStatus;
use App\Enums\ResultStatus;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ResultController extends Controller
{
    public function __construct(
        private SmsService $sms,
        private \App\Services\DigitalRepositoryService $repository,
    ) {}

    public function index()
    {
        $results = Result::with(['application.candidate.user', 'application.job.project'])
            ->latest()->paginate(25);
        return view('admin.results.index', compact('results'));
    }

    public function showUpload()
    {
        $projects = Project::where('status', ProjectStatus::OPEN)
            ->orWhere('status', ProjectStatus::CLOSED)
            ->latest()->get();
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

        $rolls = array_map(fn($row) => trim($row['roll_number'] ?? $row[0] ?? ''), $rows);
        $rolls = array_filter($rolls);

        // Fetch ALL matching applications in ONE query to eliminate N+1 choke
        $applications = Application::whereHas('examRollno', fn($q) => $q->whereIn('roll_no', $rolls))
            ->where('project_id', $projectId)
            ->with(['candidate.user', 'job', 'examRollno'])
            ->get()
            ->keyBy(fn($app) => $app->examRollno->roll_no);

        $preview  = [];
        $warnings = [];

        foreach ($rows as $i => $row) {
            $roll = trim($row['roll_number'] ?? $row[0] ?? '');
            if (!$roll) continue;

            $application = $applications->get($roll);

            if (!$application) {
                $warnings[] = "Row " . ($i + 2) . ": Roll No {$roll} not found in project.";
                continue;
            }

            $preview[] = [
                'application_id' => $application->id,
                'roll_number'    => $roll,
                'candidate_name' => $application->candidate->user->full_name,
                'cnic'           => $application->candidate->user->cnic,
                'job_title'      => $application->job->title,
                'score'         => floatval($row['score'] ?? $row[1] ?? 0),
                'total_marks'   => floatval($row['total_marks'] ?? $row[2] ?? 100),
                'result_status' => trim(strtolower($row['result_status'] ?? $row['status'] ?? $row[3] ?? 'fail')),
                'scan_path'     => $scanPaths[$roll] ?? null,
            ];
        }

        $previewKey = 'temp/result_preview_' . Auth::id() . '.json';
        Storage::disk('local')->put($previewKey, json_encode($preview));
        session(['result_project_id' => $projectId]);

        return view('admin.results.preview', compact('preview', 'warnings', 'projectId'));
    }

    public function publish(Request $request, Project $project)
    {
        $previewKey = 'temp/result_preview_' . Auth::id() . '.json';
        $previewData = Storage::disk('local')->get($previewKey);
        $preview = $previewData ? json_decode($previewData, true) : [];
        
        $sessionProjectId = session('result_project_id');
        if (empty($preview) || $sessionProjectId != $project->id) {
            return back()->with('error', 'Result data mismatch. Please upload the file for this specific project again.');
        }

        DB::transaction(function () use ($preview, $project) {
            $appIds = array_column($preview, 'application_id');
            // Fetch jobs for apps in one query to avoid N+1 inside the loop
            $appsMap = Application::whereIn('id', $appIds)->pluck('job_id', 'id');
            
            $resultsData = [];
            $jobAppeared = [];
            $now = now();
            $authId = Auth::id();

            foreach ($preview as $row) {
                $appId = $row['application_id'];
                $jobId = $appsMap[$appId] ?? null;
                if (!$jobId) continue;

                $score        = $row['score'];
                $totalMarks   = $row['total_marks'];
                
                $resultStatus = ResultStatus::tryFrom($row['result_status']) ?? ResultStatus::FAIL;
                $percentage   = $totalMarks > 0 ? round(($score / $totalMarks) * 100, 2) : 0;

                $resultsData[] = [
                    'application_id'    => $appId,
                    'roll_no'           => $row['roll_number'],
                    'score'             => $score,
                    'total_marks'       => $totalMarks,
                    'percentage'        => $percentage,
                    'result_status'     => $resultStatus->value,
                    'uploaded_by'       => $authId,
                    'published_at'      => $now,
                    'scanned_sheet_path'=> $row['scan_path'],
                    'percentile'        => 0, // Placeholder
                ];

                $jobAppeared[$jobId][] = ['app_id' => $appId, 'pct' => $percentage];
            }

            // 1. Bulk Upsert Results (Include all requisite columns to avoid default value errors)
            if (!empty($resultsData)) {
                Result::upsert($resultsData, ['application_id'], [
                    'roll_no', 'score', 'total_marks', 'percentage', 'result_status', 
                    'uploaded_by', 'published_at', 'scanned_sheet_path'
                ]);
            }

            // 2. Bulk Update Application Status
            Application::whereIn('id', $appIds)->update(['status' => ApplicationStatus::RESULT_DECLARED]);

            // 3. Compute percentiles GLOBALLY per job category and update
            foreach ($jobAppeared as $jobId => $newEntries) {
                // Fetch only IDs and percentages to minimize memory usage
                $allScores = DB::table('results')
                    ->join('applications', 'results.application_id', '=', 'applications.id')
                    ->where('applications.job_id', $jobId)
                    ->whereNotNull('published_at')
                    ->select('results.application_id', 'results.percentage')
                    ->orderByDesc('results.percentage')
                    ->get();

                if ($allScores->isNotEmpty()) {
                    $total = $allScores->count();
                    $upsertData = [];

                    foreach ($allScores as $index => $entry) {
                        $rank = $index + 1;
                        $percentile = round((($total - $rank) / $total) * 100, 2);
                        
                        $upsertData[] = [
                            'application_id' => $entry->application_id,
                            'percentile'     => $percentile
                        ];

                        // Chunk the upsert to avoid huge SQL strings
                        if (count($upsertData) >= 500) {
                            Result::upsert($upsertData, ['application_id'], ['percentile']);
                            $upsertData = [];
                        }
                    }

                    if (!empty($upsertData)) {
                        Result::upsert($upsertData, ['application_id'], ['percentile']);
                    }
                }
            }

            $project->update(['status' => ProjectStatus::RESULT_DECLARED]);

            // 4. Dispatch Event for Notifications
            ResultsPublished::dispatch($appIds);

            \App\Models\ActivityLog::log('publish_results', $project, [
                'count' => count($preview),
                'job_ids' => array_keys($jobAppeared)
            ]);
        });

        // Cleanup
        $previewKey = 'temp/result_preview_' . Auth::id() . '.json';
        Storage::disk('local')->delete($previewKey);
        session()->forget('result_project_id');
        
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
            if (($handle = fopen($file->getPathname(), 'r')) !== false) {
                // Handle BOM if present
                $bom = fread($handle, 3);
                if ($bom !== "\xEF\xBB\xBF") {
                    rewind($handle);
                }

                $headers = fgetcsv($handle);
                if ($headers) {
                    $headers = array_map('strtolower', array_map('trim', $headers));
                    while (($row = fgetcsv($handle)) !== false) {
                        if (empty(array_filter($row))) continue; // Skip empty rows
                        $rows[] = array_combine($headers, array_slice(array_pad($row, count($headers), ''), 0, count($headers)));
                        
                        // Limit rows to 15,000 for safety in this synchronous method
                        if (count($rows) > 15000) break;
                    }
                }
                fclose($handle);
            }
            return $rows;
        }
        // Excel (still uses PhpSpreadsheet, which is heavy)
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);
        $headers = array_map('strtolower', array_map('trim', array_shift($sheet)));
        return array_map(fn($r) => array_combine($headers, array_slice(array_pad($r, count($headers), ''), 0, count($headers))), $sheet);
    }
}
