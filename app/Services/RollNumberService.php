<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Batch;
use App\Models\ExamRollno;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;

class RollNumberService
{
    /**
     * Allocate candidates to a specific batch and generate roll numbers.
     * 
     * @param Batch $batch
     * @param int $count Number of candidates to allocate in this pass
     * @param array $jobIds List of job IDs to restrict the allocation to
     * @return int Number of successfully allocated candidates
     */
    public function allocateBatch(Batch $batch, int $count, array $jobIds = []): int
    {
        return DB::transaction(function () use ($batch, $count, $jobIds) {
            $closeDate = $batch->project->close_date ?? now();
            $closeYear = $closeDate->year;
            $testDateStr = $batch->test_date->toDateString();

            // Core allocation query
            $query = Application::where('applications.project_id', $batch->project_id)
                ->where('applications.status', 'fee_paid')
                ->where('applications.desired_test_city_id', $batch->center->city_id)
                ->whereDoesntHave('examRollno')
                ->whereDoesntHave('candidate.applications.examRollno', function($q) use ($testDateStr) {
                    $q->whereHas('batch', function($q2) use ($testDateStr) {
                        $q2->where('test_date', $testDateStr);
                    });
                })
                ->join('candidates', 'applications.candidate_id', '=', 'candidates.id')
                ->join('pats_jobs', 'applications.job_id', '=', 'pats_jobs.id')
                // Eligibility Logic (Hardened SQL)
                ->where(function($q) use ($closeYear) {
                    $q->whereRaw('(pats_jobs.min_degree_level IS NULL OR pats_jobs.min_degree_level <= (
                        SELECT MAX(degree_level) FROM education_history 
                        WHERE candidate_id = candidates.id 
                        AND passing_year <= ?
                    ))', [$closeYear]);
                })
                ->whereRaw('(pats_jobs.age_min IS NULL OR ((YEAR(?) - YEAR(candidates.dob)) - (DATE_FORMAT(?, "%m%d") < DATE_FORMAT(candidates.dob, "%m%d")) >= pats_jobs.age_min))', [$closeDate->toDateString(), $closeDate->toDateString()])
                ->whereRaw('(pats_jobs.age_max IS NULL OR ((YEAR(?) - YEAR(candidates.dob)) - (DATE_FORMAT(?, "%m%d") < DATE_FORMAT(candidates.dob, "%m%d")) <= pats_jobs.age_max))', [$closeDate->toDateString(), $closeDate->toDateString()])
                ->select('applications.*');

            if (!empty($jobIds)) {
                $query->whereIn('applications.job_id', $jobIds);
            }

            $candidates = $query->with('job')->lockForUpdate()->limit($count)->get();
            if ($candidates->isEmpty()) return 0;

            // Roll Number Generation Logic
            $examRecords = [];
            $appIds = [];
            
            // Get current max serials for each job in this project/city/center to prevent collisions
            $uniqueJobs = $candidates->pluck('job_id')->unique();
            $jobSerials = [];
            foreach ($uniqueJobs as $jobId) {
                // Get the current max serial from the last 4 digits of roll_no for this specific job/center
                $lastRoll = ExamRollno::where('project_id', $batch->project_id)
                    ->where('center_id', $batch->center_id)
                    ->where('job_id', $jobId)
                    ->orderByDesc('roll_no')
                    ->lockForUpdate()
                    ->first();
                
                $jobSerials[$jobId] = $lastRoll ? (int) substr($lastRoll->roll_no, -4) : 0;
            }

            foreach ($candidates as $app) {
                $jobId = $app->job_id;
                $serial = ++$jobSerials[$jobId];

                // Robust Format: [ProjID(2)][JobID(2)][CityID(2)][CenterID(2)][Serial(4)]
                $rollNo = sprintf(
                    '%02d%02d%02d%02d%04d',
                    $app->project_id % 100,
                    $app->job->job_code % 100,
                    $app->desired_test_city_id % 100,
                    ((int) preg_replace('/[^0-9]/', '', $batch->center->tcid) % 100),
                    $serial
                );

                $examRecords[] = [
                    'application_id' => $app->id,
                    'project_id'     => $app->project_id,
                    'job_id'         => $app->job_id,
                    'city_id'        => $app->desired_test_city_id,
                    'center_id'      => $batch->center_id,
                    'batch_id'       => $batch->id,
                    'roll_no'        => $rollNo,
                    'barcode'        => $rollNo, 
                    'batch_no'       => (string)$batch->batch_number,
                    'slip_ready'     => 0,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
                $appIds[] = $app->id;
            }

            foreach (array_chunk($examRecords, 100) as $chunk) {
                ExamRollno::insert($chunk);
            }

            Application::whereIn('id', $appIds)->update([
                'status'   => 'scheduled',
                'batch_id' => $batch->id
            ]);
            
            $batch->increment('booked_seats', count($appIds));

            return count($appIds);
        });
    }

    /**
     * Mark all roll numbers in a batch as ready for download.
     */
    public function markBatchReady(Batch $batch): int
    {
        $batch->update(['is_ready' => true]);
        return ExamRollno::where('batch_id', $batch->id)
            ->update(['slip_ready' => true]);
    }

    /**
     * Get a summary of allocations in a batch by Job Post.
     */
    public function batchSummary(Batch $batch)
    {
        return ExamRollno::where('batch_id', $batch->id)
            ->with('job')
            ->selectRaw('job_id, MIN(roll_no) as roll_from, MAX(roll_no) as roll_to, COUNT(*) as allocated')
            ->groupBy('job_id')
            ->get();
    }
    /**
     * Get full list of candidates in a batch grouped by Job Post.
     */
    public function batchRoster(Batch $batch)
    {
        return ExamRollno::where('batch_id', $batch->id)
            ->with(['application.candidate.user', 'job'])
            ->orderBy('job_id')
            ->orderBy('roll_no')
            ->get()
            ->groupBy('job_id');
    }
}
