<?php

namespace App\Listeners;

use App\Events\ResultsPublished;
use App\Jobs\SendSmsJob;
use App\Models\Application;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyBatchOfResults implements ShouldQueue
{
    public function handle(ResultsPublished $event): void
    {
        $applications = Application::whereIn('id', $event->applicationIds)
            ->whereHas('result')
            ->with(['candidate.user', 'job', 'result'])
            ->get();

        foreach ($applications as $app) {
            $user = $app->candidate->user;
            SendSmsJob::dispatch(
                $user->phone,
                "PATS: Your result for the post of {$app->job->title} (Roll No: {$app->result->roll_no}) has been declared. Check on portal.",
                $user->id,
                'result_published'
            );
        }
    }
}
