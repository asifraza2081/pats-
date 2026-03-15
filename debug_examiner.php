<?php
use App\Models\User;
use App\Models\Batch;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$email = 'examiner@pats.test';
$user = User::where('email', $email)->first();

if (!$user) {
    echo "User not found\n";
    exit;
}

echo "User: " . $user->full_name . " (ID: " . $user->id . ")\n";

$centers = $user->assignedCenters;
echo "Assigned Centers Count: " . $centers->count() . "\n";
foreach ($centers as $center) {
    echo " - Center: " . $center->name . " (ID: " . $center->id . ") [Project ID: " . $center->pivot->project_id . "]\n";
}

$assignedCenterIds = $centers->pluck('id')->toArray();
echo "Center IDs: " . implode(', ', $assignedCenterIds) . "\n";

$sessions = Batch::whereIn('center_id', $assignedCenterIds)
    ->where('test_date', '>=', now()->toDateString())
    ->get();

echo "Sessions visible to Examiner: " . $sessions->count() . "\n";
foreach ($sessions as $s) {
    echo " - Session ID: " . $s->id . " | Project: " . $s->project->name . " | Center: " . $s->center->name . " | Date: " . $s->test_date . "\n";
}

// Check if there are ANY sessions in the system
$allSessions = Batch::all();
echo "Total Sessions in system: " . $allSessions->count() . "\n";
foreach ($allSessions as $s) {
    echo " - System Session ID: " . $s->id . " | Center ID: " . $s->center_id . " | Date: " . $s->test_date . "\n";
}
