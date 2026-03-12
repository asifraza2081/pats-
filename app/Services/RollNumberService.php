<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Batch;
use App\Models\ExamRollno;
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
            // 1. Find eligible candidates:
            // - Fee Paid
            // - No existing exam_rollnos record
            // - Desired test city matches batch center's city
            // - Same Project
            // - Selected Jobs
            $query = Application::where('project_id', $batch->project_id)
                ->where('status', 'fee_paid')
                ->where('desired_test_city_id', $batch->center->city_id)
                ->whereDoesntHave('examRollno');
                
            if (!empty($jobIds)) {
                $query->whereIn('job_id', $jobIds);
            }

            $candidates = $query->lockForUpdate()->limit($count)->get();

            if ($candidates->isEmpty()) {
                return 0;
            }

            $allocated = 0;
            $generator = new BarcodeGeneratorPNG();

            foreach ($candidates as $app) {
                // Get the last serial for this project/job/city/center to increment
                $lastSerial = ExamRollno::where('project_id', $app->project_id)
                    ->where('job_id', $app->job_id)
                    ->where('city_id', $app->desired_test_city_id)
                    ->where('center_id', $batch->center_id)
                    ->count();

                $serial = $lastSerial + 1;
                
                // Roll No Format: [ProjID][JobID][CityID][CenterTCID][Serial] (NUMBERS ONLY)
                // Example: 110230010001
                $rollNo = sprintf(
                    '%d%d%02d%s%04d',
                    $app->project_id % 10, // Single digit for project
                    $app->job->job_code % 100, // Up to 2 digits for job
                    $app->desired_test_city_id % 100, // 2 digits for city
                    $batch->center->tcid, // Use TCID string (e.g. 3001)
                    $serial
                );

                // Create Roll Number Record
                ExamRollno::create([
                    'application_id' => $app->id,
                    'project_id'     => $app->project_id,
                    'job_id'         => $app->job_id,
                    'city_id'        => $app->desired_test_city_id,
                    'center_id'      => $batch->center_id,
                    'batch_id'       => $batch->id,
                    'roll_no'        => $rollNo,
                    'barcode'        => base64_encode($generator->getBarcode($rollNo, $generator::TYPE_CODE_128, 2, 50)), 
                    'batch_no'       => (string)$batch->batch_number,
                    'test_date'      => $batch->test_date,
                    'reporting_time' => $batch->reporting_time,
                    'start_time'     => $batch->start_time,
                    'slip_ready'     => false, // Admin publishes later
                ]);

                // Update application with batch_id (redundant but helpful for quick query)
                $app->update(['batch_id' => $batch->id]);
                
                $allocated++;
            }

            // Update batch booked seats
            $batch->increment('booked_seats', $allocated);

            return $allocated;
        });
    }

    /**
     * Mark all roll numbers in a batch as ready for download.
     */
    public function markBatchReady(Batch $batch): int
    {
        return ExamRollno::where('batch_id', $batch->id)
            ->update(['slip_ready' => true]);
    }

    /**
     * Get a summary of allocations in a batch by Job Post.
     */
    public function batchSummary(Batch $batch)
    {
        return ExamRollno::where('batch_id', $batch->id)
            ->join('pats_jobs', 'exam_rollnos.job_id', '=', 'pats_jobs.id')
            ->selectRaw('pats_jobs.title as job_title, count(*) as allocated')
            ->groupBy('pats_jobs.title')
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
