<?php

use App\Models\Project;
use App\Services\AllocationStatsService;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->boot();

$project = Project::first();
if (!$project) {
    echo "No project found.\n";
    exit;
}

$service = new AllocationStatsService();

$dates = [
    '2026-03-15',
    '15-03-2026',
    '03-15-2026',
    '15032026',
    '03152026',
];

foreach ($dates as $date) {
    echo "Testing date: {$date}\n";
    try {
        DB::enableQueryLog();
        $service->getStats($project, $date);
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        echo "Success.\n";
        // echo "SQL: " . $queries[count($queries)-1]['query'] . "\n";
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
    echo "-------------------\n";
}
