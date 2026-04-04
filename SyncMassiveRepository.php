<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use App\Models\Application;
use App\Models\Result;
use App\Models\Batch;
use App\Models\ExamRollno;
use App\Services\DigitalRepositoryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;

// Technical Guards for Massive IO
ini_set('memory_limit', '2G');
set_time_limit(0); 

// Disable lazy loading prevention for the duration of this massive sync
Model::preventLazyLoading(false);
Model::preventAccessingMissingAttributes(false);

$admin = \App\Models\User::where('email', 'admin@pats.test')->first();
Auth::login($admin);

$repo = new DigitalRepositoryService();

$project = Project::where('name', 'LIKE', '%WAPDA%')->first();
if (!$project) {
    die("WAPDA Project not found.\n");
}

echo "Starting Massive Repository Sync for Project: {$project->name}\n";

$rollService = new \App\Services\RollNumberService();

// 1. Process Attendance Sheets (15 centers x 2 shifts = 30-34 batches)
echo "--- Syncing Attendance Sheets ---\n";
$batches = Batch::where('project_id', $project->id)->with(['project', 'center.city'])->get();
foreach ($batches as $batch) {
    echo "Saving Attendance: {$batch->center->name} (B{$batch->id})\n";
    $roster = $rollService->batchRoster($batch); 
    $pdf = Pdf::loadView('pdf.attendance', compact('batch', 'roster'))->setPaper('a4', 'portrait');
    $repo->saveAttendanceSheet($batch, $pdf->output());
}

// 2. Process Individual Slips & Results (5,000 Candidates)
echo "--- Syncing Candidate Folders (Roll Number Slips & Result Cards) ---\n";
echo "Note: Processing 5,000 candidates in chunks...\n";

Result::whereHas('application', fn($q) => $q->where('project_id', $project->id))
    ->with([
        'application.candidate.user', 
        'application.job.project', 
        'application.examRollno.city', 
        'application.examRollno.center', 
        'application.examRollno.batch'
    ])
    ->chunk(50, function ($results) use ($repo) {
        foreach ($results as $result) {
            $app = $result->application;
            $examRollno = $app->examRollno;
            $candidate = $app->candidate;
            
            // Explicitly set relations to prevent LazyLoadingViolation in repo service
            $examRollno->setRelation('application', $app);
            $result->setRelation('application', $app);

            // Generate & Save Roll No Slip
            $slipPdf = Pdf::loadView('pdf.slip', [
                'app' => $app, 
                'examRollno' => $examRollno, 
                'candidate' => $candidate
            ])->setPaper('a4', 'portrait');
            $repo->saveRollNumberSlip($examRollno, $slipPdf->output());

            // Generate & Save Result Card
            $resultPdf = Pdf::loadView('pdf.result-card', [
                'app' => $app, 
                'result' => $result
            ])->setPaper('a4', 'portrait');
            $repo->saveResultCard($result, $resultPdf->output());
            
            // Periodically clear memory
            unset($slipPdf, $resultPdf);
        }
        echo "."; // Progress indicator
    });

echo "\n--- Syncing Summaries ---\n";
foreach ($project->jobs as $job) {
    echo "Saving Summary for: {$job->title}\n";
    // Mock summary for stress test
    $summaryPdf = Pdf::loadHTML("<h1>Result Summary - {$job->title}</h1><p>Total Candidates: 5000 (Aggregate)</p>");
    $repo->saveSummary($job, 'Result', $summaryPdf->output());
}

echo "\nSUCCESS: Hierarchical Repository Synchronized for 5,000+ records.\n";
echo "Check path: storage/app/public/projects/" . $project->name . "...\n";
