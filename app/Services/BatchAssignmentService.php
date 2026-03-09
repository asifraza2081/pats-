<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Batch;

class BatchAssignmentService
{
    /**
     * Find the best batch for a candidate applying for a job.
     * 1. Try to find batch in preferred city (priority 1, then 2)
     * 2. Fall back to any batch with capacity
     * 3. Return null if all batches are full
     */
    public function findBatch(int $projectId, ?string $city1, ?string $city2): ?Batch
    {
        $cities = array_filter([$city1, $city2]);

        // Try preferred cities first
        foreach ($cities as $city) {
            $batch = Batch::where('project_id', $projectId)
                ->whereHas('center', fn($q) => $q->where('city', 'like', "%{$city}%"))
                ->whereRaw('booked_seats < total_seats')
                ->orderBy('test_date')
                ->orderBy('batch_number')
                ->first();
            if ($batch) return $batch;
        }

        // Any available batch in the project
        return Batch::where('project_id', $projectId)
            ->whereRaw('booked_seats < total_seats')
            ->orderBy('test_date')
            ->orderBy('batch_number')
            ->first();
    }

    /**
     * Book a seat in the batch (atomic increment).
     */
    public function bookSeat(Batch $batch): void
    {
        $batch->increment('booked_seats');
    }

    /**
     * Release a seat (on application cancellation or rejection).
     */
    public function releaseSeat(Batch $batch): void
    {
        if ($batch->booked_seats > 0) {
            $batch->decrement('booked_seats');
        }
    }
}
