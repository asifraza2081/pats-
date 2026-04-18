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
            ->with(['candidate.user', 'job.project', 'result'])
            ->get();

        foreach ($applications as $app) {
            $user = $app->candidate->user;
            
            // SMS Notification
            SendSmsJob::dispatch(
                $user->phone,
                "PATS: Your result for the post of {$app->job->title} (Roll No: {$app->result->roll_no}) has been declared. Check on portal.",
                $user->id,
                'result_published'
            );
            
            // Real-time Dashboard Notification
            $user->notify(new \App\Notifications\SystemAlert(
                "Your result for {$app->job->title} has been declared. Click to check your score.",
                'info',
                route('candidate.applications.show', $app->id)
            ));

            // Email Notification
            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ResultPublishedMail($app));
        }
    }
}
