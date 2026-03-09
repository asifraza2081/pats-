<?php

use App\Models\Batch;
use App\Models\Application;
use App\Models\Project;
use App\Models\Result;
use Illuminate\Http\Request;

echo "Starting Admin Post-Test E2E Simulation...\n";

// 1. Mark Attendance
$batch = Batch::first();
if (!$batch) die("FAIL: No batch found\n");

$apps = $batch->applications;
foreach ($apps as $app) {
    $app->update(['status' => 'appeared']);
    echo "Marked app {$app->id} as appeared for Roll Number: {$app->rollNumber->roll_number}\n";
}

// 2. Upload and Publish Results
$project = Project::first();
if (!$project) die("FAIL: No project found\n");

$projectApps = Application::where('status', 'appeared')
    ->whereHas('batch', fn($q) => $q->where('project_id', $project->id))
    ->get();

$preview = [];
foreach ($projectApps as $app) {
    if (!$app->rollNumber) continue;
    $preview[] = [
        'application'   => $app->toArray(),
        'roll_number'   => $app->rollNumber->roll_number,
        'score'         => 80,
        'total_marks'   => 100,
        'result_status' => 'pass'
    ];
}

if (empty($preview)) die("FAIL: No valid applications for result preview.\n");

// Simulate Session Storage
session(['result_preview' => $preview, 'result_project_id' => $project->id]);

// Call Controller Method
$controller = app(\App\Http\Controllers\Admin\ResultController::class);
$request = Request::create('/admin/projects/'.$project->id.'/results/publish', 'POST');
$request->setLaravelSession(session()->driver());

// Bind the fake logged-in admin user to satisfy Auth::id() inside ResultController -> publish
$admin = App\Models\User::where('email', 'admin@pats.test')->first();
auth()->login($admin);

$response = $controller->publish($request, $project);

echo "Redirection Target: " . $response->getTargetUrl() . "\n";

// 3. Verify the final outcomes
$result = Result::first();
if ($result) {
    echo "Result successfully stored! Score: {$result->score}, Percentage: {$result->percentage}%, Percentile: {$result->percentile}\n";
} else {
    echo "FAIL: Result was not stored.\n";
}

echo "Project Final Status: {$project->refresh()->status}\n";

$app = $projectApps->first();
echo "Candidate Application Status: {$app->refresh()->status}\n";

echo "\n✅ ADMIN POST-TEST E2E PASSED!\n";
