<?php

namespace App\Console\Commands\Tools;

use App\Models\Project;
use App\Services\AllocationStatsService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class DebugBatchStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pats:debug-stats {project_id? : The ID of the project to audit}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit batch allocation statistics for a project.';

    /**
     * Execute the console command.
     */
    public function handle(AllocationStatsService $statsService)
    {
        $projectId = $this->argument('project_id');

        if (!$projectId) {
            $project = Project::first();
            if (!$project) {
                $this->error('No project found.');
                return 1;
            }
            $projectId = $project->id;
        }

        $project = Project::with('jobs')->findOrFail($projectId);
        $this->info("Auditing stats for Project ID: {$projectId}");

        try {
            $data = $statsService->getStats($project);

            $this->table(['Metric', 'Value'], [
                ['Project ID', $projectId],
                ['Project Total (Fee Paid)', $data['project_total']],
                ['Project Unallocated', $data['project_unallocated']],
            ]);

            if (!$data['stats']->isEmpty()) {
                $this->info("\nCity/Job Breakdown:");
                $this->table(['City', 'Job', 'Pending Count'], $data['stats']->map(function($s) {
                    return [$s->city_name, $s->job_title, $s->pending_count];
                })->toArray());
            }

        } catch (\Exception $e) {
            $this->error('ERROR: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
