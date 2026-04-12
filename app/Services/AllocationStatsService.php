<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Project;
use Carbon\Carbon;

class AllocationStatsService
{
    /**
     * Get pending allocation statistics for a project.
     *
     * @param Project $project
     * @param string|null $testDate Optional date for collision prevention
     * @return array
     */
    public function getStats(Project $project, ?string $testDate = null): array
    {


        if ($testDate !== null && $testDate !== '') {
            try {
                $parsed = Carbon::parse($testDate);
                $testDate = ($parsed->year >= 1000 && $parsed->year <= 9999)
                    ? $parsed->toDateString()
                    : null;
            } catch (\Exception $e) {
                $testDate = null;
            }
        } else {
            $testDate = null;
        }

        // 1. Base query for eligible candidates
        $eligibleQuery = Application::where('applications.project_id', $project->id)
            ->feePaid()
            ->eligible($project);

        // 2. Unallocated candidates
        $baseQuery = (clone $eligibleQuery)->unallocated();

        // 3. Collision Prevention
        // Removed dynamic testDate collision queries as they conflict with deep Eloquent relations and reset UI state accidentally

        // 4. Breakdown by City/Job
        // We start a fresh query on the base table to avoid 'applications.*' from eligible scope
        $breakdown = $project->applications()
            ->feePaid()
            ->eligible($project)
            ->unallocated();

        $breakdown = $breakdown
            ->join('cities', 'applications.desired_test_city_id', '=', 'cities.id')
            ->selectRaw('cities.id as city_id, cities.name as city_name')
            ->selectRaw('pats_jobs.id as job_id, pats_jobs.title as job_title')
            ->selectRaw('count(*) as pending_count')
            ->groupByRaw('cities.id, cities.name, pats_jobs.id, pats_jobs.title')
            ->get();

        // 5. Job-specific totals (Optimized: Single query instead of per-job loop)
        $jobCounts = (clone $baseQuery)
            ->selectRaw('applications.job_id, count(*) as total')
            ->groupBy('applications.job_id')
            ->pluck('total', 'job_id');

        $jobStats = $project->jobs->map(function($j) use ($jobCounts) {
            return [
                'id' => $j->id,
                'pending' => $jobCounts[$j->id] ?? 0
            ];
        });

        return [
            'stats'               => $breakdown,
            'job_stats'           => $jobStats,
            'project_total'       => (clone $eligibleQuery)->count(),
            'project_unallocated' => (clone $baseQuery)->count(),
        ];
    }
}
