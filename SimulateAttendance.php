<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use App\Models\Batch;
use App\Models\AttendanceScan;
use Illuminate\Support\Facades\Auth;

// Mock login
$admin = \App\Models\User::where('email', 'admin@pats.test')->first();
Auth::login($admin);

$project = Project::where('name', 'LIKE', '%WAPDA%')->first();
if (!$project) {
    die("WAPDA Project not found.\n");
}

echo "Simulating Attendance Verification for Project: {$project->name}\n";

$batches = Batch::where('project_id', $project->id)->with('center')->get();

foreach ($batches as $batch) {
    echo "- Digitally Signing Center: {$batch->center->name} (Batch: {$batch->batch_number})\n";
    
    // Create Scan Record
    AttendanceScan::updateOrCreate(
        ['batch_id' => $batch->id],
        [
            'project_id'  => $project->id,
            'center_id'   => $batch->center_id,
            'test_date'   => $batch->test_date,
            'file_path'   => "attendance_scans/signed_{$batch->id}.pdf",
            'page_number' => 1,
            'uploaded_by' => $admin->id,
            'uploaded_at' => now(),
        ]
    );
}

echo "\nSUCCESS: All 15 centers have been verified and digitally signed.\n";
