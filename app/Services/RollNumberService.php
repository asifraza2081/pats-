<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Batch;
use App\Models\RollNumber;
use Illuminate\Support\Facades\DB;

class RollNumberService
{
    /**
     * Assign a roll number for an application after payment is verified.
     * Format: TCID_prefix(3) + job_code(2, zero-padded) + serial(4, zero-padded)
     * e.g. TCID=3001 → prefix=301, job_code=2 → 02, serial=61 → 0061 → 301020061
     */
    public function assign(Application $application): RollNumber
    {
        return DB::transaction(function () use ($application) {
            if ($application->rollNumber) {
                return $application->rollNumber;
            }

            $batch    = $application->batch()->with('center')->first();
            $job      = $application->job;
            $prefix   = substr($batch->center->tcid, 0, 3);
            $jobCode  = str_pad((string) $job->job_code, 2, '0', STR_PAD_LEFT);

            // Serial = count of existing roll numbers for this project+job + 1
            $serial = RollNumber::whereHas('application', fn($q) =>
                    $q->where('job_id', $job->id)
                      ->whereHas('batch', fn($b) => $b->where('project_id', $batch->project_id))
                )->lockForUpdate()->count() + 1;

            $rollNumber = $prefix . $jobCode . str_pad((string) $serial, 4, '0', STR_PAD_LEFT);

            // Collision guard (extremely rare but safe)
            while (RollNumber::where('roll_number', $rollNumber)->exists()) {
                $serial++;
                $rollNumber = $prefix . $jobCode . str_pad((string) $serial, 4, '0', STR_PAD_LEFT);
            }

            return RollNumber::create([
                'application_id' => $application->id,
                'roll_number'    => $rollNumber,
                'slip_ready'     => false,
                'assigned_at'    => now(),
            ]);
        });
    }

    /**
     * Mark all roll numbers in a batch as slip_ready and return count.
     */
    public function markBatchReady(Batch $batch): int
    {
        return RollNumber::whereHas('application', fn($q) =>
            $q->where('batch_id', $batch->id)
        )->update(['slip_ready' => true]);
    }

    /**
     * Get all roll numbers grouped by job for a batch (for batch summary sheet).
     * Returns [job_id => ['job' => PatsJob, 'from' => roll, 'to' => roll, 'count' => n], ...]
     */
    public function batchSummary(Batch $batch): array
    {
        $rows = RollNumber::with(['application.job'])
            ->whereHas('application', fn($q) => $q->where('batch_id', $batch->id))
            ->orderBy('roll_number')
            ->get()
            ->groupBy(fn($rn) => $rn->application->job_id);

        $summary = [];
        foreach ($rows as $jobId => $group) {
            $summary[$jobId] = [
                'job'   => $group->first()->application->job,
                'from'  => $group->first()->roll_number,
                'to'    => $group->last()->roll_number,
                'count' => $group->count(),
                'items' => $group,
            ];
        }
        return $summary;
    }
}
