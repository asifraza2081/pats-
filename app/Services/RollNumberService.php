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
        
        // 1. Prepare data, generate barcodes, and insert EVERYTHING in ONE transaction
        return DB::transaction(function () use ($batch, $count, $jobIds) {
            $closeDate = $batch->project->close_date
                ? Carbon::parse($batch->project->close_date)->toDateString()
                : Carbon::now()->toDateString();
            
            $closeYear = $batch->project->close_date
                ? Carbon::parse($batch->project->close_date)->year
                : Carbon::now()->year;

            $testDateStr = Carbon::parse($batch->test_date)->toDateString();

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
                ->where(function($q) use ($closeYear) {
                    $q->whereRaw('(pats_jobs.min_degree_level IS NULL OR pats_jobs.min_degree_level <= (
                        SELECT MAX(degree_level) FROM education_history 
                        WHERE candidate_id = candidates.id 
                        AND passing_year <= ?
                    ))', [$closeYear]);
                })
                ->whereRaw('(pats_jobs.age_min IS NULL OR (TIMESTAMPDIFF(YEAR, candidates.dob, ?) >= pats_jobs.age_min AND (pats_jobs.age_max IS NULL OR TIMESTAMPDIFF(YEAR, candidates.dob, ?) <= pats_jobs.age_max)))', [$closeDate, $closeDate])
                ->select('applications.*');

            if (!empty($jobIds)) {
                $query->whereIn('applications.job_id', $jobIds);
            }

            $candidates = $query->with('job')->lockForUpdate()->limit($count)->get();
            if ($candidates->isEmpty()) return 0;

            $uniqueJobs = $candidates->pluck('job_id')->unique();
            $jobCounts = [];
            foreach ($uniqueJobs as $jobId) {
                $jobCounts[$jobId] = ExamRollno::where('project_id', $batch->project_id)
                    ->where('city_id', $batch->center->city_id)
                    ->where('center_id', $batch->center_id)
                    ->where('job_id', $jobId)
                    ->lockForUpdate()
                    ->count();
            }

            $examRecords = [];
            $appIds = [];

            foreach ($candidates as $app) {
                $jobId = $app->job_id;
                $serial = ($jobCounts[$jobId] ?? 0) + 1;
                $jobCounts[$jobId] = $serial;

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
                    'barcode'        => $rollNo, // Using roll_no as barcode for integrity and uniqueness
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
                'batch_id' => $batch->id,
                'status'   => 'scheduled'
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
            ->selectRaw('job_id, MIN(CAST(roll_no AS UNSIGNED)) as roll_from, MAX(CAST(roll_no AS UNSIGNED)) as roll_to, COUNT(*) as allocated')
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
