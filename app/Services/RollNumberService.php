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
            // 1. Find eligible candidates
            $query = Application::where('project_id', $batch->project_id)
                ->where('status', 'fee_paid')
                ->where('desired_test_city_id', $batch->center->city_id)
                ->whereDoesntHave('examRollno');
                
            if (!empty($jobIds)) {
                $query->whereIn('job_id', $jobIds);
            }

            // Lock for update to prevent double allocation in high concurrency
            $candidates = $query->with('job')->lockForUpdate()->limit($count)->get();

            if ($candidates->isEmpty()) {
                return 0;
            }

            // 2. Pre-fetch last serials for each job to avoid N+1 queries in the loop
            // We use lockForUpdate() on a dummy select or on the existing records to prevent race conditions
            $jobCounts = ExamRollno::where('project_id', $batch->project_id)
                ->where('city_id', $batch->center->city_id)
                ->where('center_id', $batch->center_id)
                ->whereIn('job_id', $candidates->pluck('job_id')->unique())
                ->lockForUpdate() // LOCK these rows to prevent concurrent serial calculation
                ->selectRaw('job_id, count(*) as count')
                ->groupBy('job_id')
                ->pluck('count', 'job_id')
                ->toArray();

            $allocated = 0;
            $generator = new BarcodeGeneratorPNG();
            $examRecords = [];
            $appIdsForUpdate = [];

            $numericCenterTcid = preg_replace('/[^0-9]/', '', $batch->center->tcid);

            foreach ($candidates as $app) {
                $jobId = $app->job_id;
                $serial = ($jobCounts[$jobId] ?? 0) + 1;
                $jobCounts[$jobId] = $serial; // Increment for next candidate in loop

                // Roll No Format: [ProjID][JobID][CityID][CenterTCID][Serial]
                $rollNo = sprintf(
                    '%d%02s%02d%s%04d',
                    $app->project_id % 10,
                    str_pad($app->job->job_code % 100, 2, '0', STR_PAD_LEFT),
                    $app->desired_test_city_id % 100,
                    $numericCenterTcid,
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
                    'barcode'        => base64_encode($generator->getBarcode($rollNo, $generator::TYPE_CODE_128, 2, 50)),
                    'batch_no'       => (string)$batch->batch_number,
                    'test_date'      => $batch->test_date->format('Y-m-d'),
                    'reporting_time' => $batch->reporting_time,
                    'start_time'     => $batch->start_time,
                    'slip_ready'     => 0,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];

                $appIdsForUpdate[] = $app->id;
                $allocated++;
            }

            // 3. Bulk Insert Exam Records
            if (!empty($examRecords)) {
                // Chunking to avoid large packet issues
                foreach (array_chunk($examRecords, 100) as $chunk) {
                    ExamRollno::insert($chunk);
                }
            }

            // 4. Bulk Update Applications
            if (!empty($appIdsForUpdate)) {
                Application::whereIn('id', $appIdsForUpdate)->update([
                    'batch_id' => $batch->id
                ]);
            }

            // 5. Update batch booked seats
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
