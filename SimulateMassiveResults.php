<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use App\Models\Application;
use App\Models\Result;
use App\Models\ExamRollno;
use App\Events\ResultsPublished;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

// Mock login
$admin = \App\Models\User::where('email', 'admin@pats.test')->first();
Auth::login($admin);

$project = Project::where('name', 'LIKE', '%WAPDA%')->first();
if (!$project) {
    die("WAPDA Project not found.\n");
}

echo "Starting Massive Results Processing for Project: {$project->name}\n";

// Fetch all roll numbers in this project
$rollRecords = ExamRollno::where('project_id', $project->id)->get();
$count = $rollRecords->count();
echo "Found {$count} allocated candidates. Processing scores...\n";

$resultsData = [];
$jobAppeared = [];
$now = now();

foreach ($rollRecords as $rn) {
    $score = rand(25, 95);
    $totalMarks = 100;
    $percentage = round(($score / $totalMarks) * 100, 2);
    $status = ($percentage >= 50) ? 'pass' : 'fail';
    
    // Simulate some absences (5%)
    if (rand(1, 100) <= 5) {
        $status = 'absent';
        $score = 0;
        $percentage = 0;
    }

    $resultsData[] = [
        'application_id'    => $rn->application_id,
        'roll_no'           => $rn->roll_no,
        'score'             => $score,
        'total_marks'       => $totalMarks,
        'percentage'        => $percentage,
        'result_status'     => $status,
        'uploaded_by'       => $admin->id,
        'published_at'      => $now,
        'percentile'        => 0, // Calculated later
        'created_at'        => $now,
        'updated_at'        => $now,
    ];

    $jobAppeared[$rn->job_id][] = ['app_id' => $rn->application_id, 'pct' => $percentage];
}

// 1. Bulk Upsert Results
echo "Inserting 5,000 results into database...\n";
foreach (array_chunk($resultsData, 500) as $chunk) {
    Result::upsert($chunk, ['application_id'], [
        'roll_no', 'score', 'total_marks', 'percentage', 'result_status', 
        'uploaded_by', 'published_at', 'percentile'
    ]);
}

// 2. Global Percentile Computation
echo "Calculating percentiles per job...\n";
foreach ($jobAppeared as $jobId => $entries) {
    $allScores = Result::join('applications', 'results.application_id', '=', 'applications.id')
        ->where('applications.job_id', $jobId)
        ->select('results.*')
        ->get();

    if ($allScores->isNotEmpty()) {
        $sorted = $allScores->sortByDesc('percentage')->values();
        $total = $sorted->count();
        $upsertData = [];

        foreach ($allScores as $entry) {
            $rank = $sorted->search(fn($s) => $s->application_id == $entry->application_id) + 1;
            $percentile = round((($total - $rank) / $total) * 100, 2);
            
            $upsertData[] = [
                'application_id' => $entry->application_id,
                'percentile'     => $percentile,
                'roll_no'        => $entry->roll_no,
                'score'          => $entry->score,
                'total_marks'    => $entry->total_marks,
                'percentage'     => $entry->percentage,
                'result_status'  => $entry->result_status,
                'uploaded_by'    => $entry->uploaded_by,
                'published_at'   => $entry->published_at,
            ];
        }

        foreach (array_chunk($upsertData, 500) as $chunk) {
            Result::upsert($chunk, ['application_id'], ['percentile']);
        }
    }
}

// 3. Mark Applications & Project Status
echo "Updating application statuses...\n";
Application::whereIn('id', array_column($resultsData, 'application_id'))
    ->update(['status' => 'result_declared']);

$project->update(['status' => 'result_declared']);

// 4. Create Announcement for Ticker
\App\Models\Announcement::create([
    'text' => "Official Results for {$project->name} have been declared. Candidates can now check their scores.",
    'type' => 'result',
    'priority' => 10,
]);

// 5. Notify Candidates (Dispatch only first 100 for simulation performance)
echo "Dispatching notifications (Limited to first 100 for safety)...\n";
ResultsPublished::dispatch(array_slice(array_column($resultsData, 'application_id'), 0, 100));

echo "\nSUCCESS: Massive results processed and published for 5,000 candidates.\n";
