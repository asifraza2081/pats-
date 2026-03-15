<?php

namespace App\Listeners;

use App\Events\SlipsPublished;
use App\Jobs\SendSmsJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyBatchOfSlips implements ShouldQueue
{
    public function handle(SlipsPublished $event): void
    {
        $batch = $event->batch;
        $applications = $batch->applications()->with(['candidate.user', 'examRollno', 'job'])->get();

        foreach ($applications as $app) {
            if ($app->examRollno?->slip_ready) {
                $user = $app->candidate->user;
                SendSmsJob::dispatch(
                    $user->phone,
                    "PATS: Your Roll Number Slip for {$app->job->title} is ready. Download from portal.",
                    $user->id,
                    'slip_ready'
                );
            }
        }
    }
}
