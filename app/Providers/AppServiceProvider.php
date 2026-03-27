<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::define('view-session', function ($user, \App\Models\Batch $batch) {
            if ($user->hasAnyRole(['admin', 'super_admin', 'data_entry'])) return true;
            if ($user->hasRole('examiner')) {
                return $user->assignedCenters()->where('test_centers.id', $batch->center_id)->exists();
            }
            return false;
        });

        \Illuminate\Support\Facades\Gate::define('publish results', function ($user) {
            return $user->hasAnyRole(['admin', 'super_admin', 'data_entry']);
        });
        // Job Failure Monitoring
        \Illuminate\Support\Facades\Queue::failing(function (\Illuminate\Queue\Events\JobFailed $event) {
            $admins = \App\Models\User::role('super_admin')->get();
            $jobName = $event->job->resolveName();
            $message = "CRITICAL: Background Job Failed ({$jobName}). Technical review required.";
            
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\SystemAlert($message, 'error'));
            }
        });
    }
}
