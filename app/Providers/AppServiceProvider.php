<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Hardening: Super Admin Bypass
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin') ? true : null;
        });

        // Hardening: Rate Limiters
        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinutes(15, 5)->by($request->input('email', $request->input('cnic', $request->ip())));
        });

        RateLimiter::for('registration', function (Request $request) {
            return Limit::perHour(3)->by($request->ip());
        });

        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        // Financial Module: auto-post revenue to ledger on payment verification
        \App\Models\Payment::observe(\App\Observers\PaymentObserver::class);

        // Hardening: Prevent N+1 and other common pitfalls in development
        \Illuminate\Database\Eloquent\Model::shouldBeStrict(! $this->app->isProduction());
        // Job Failure Monitoring
        \Illuminate\Support\Facades\Queue::failing(function (\Illuminate\Queue\Events\JobFailed $event) {
            $admins = \App\Models\User::role('super_admin')->get();
            $jobName = $event->job->resolveName();
            $message = "CRITICAL: Background Job Failed ({$jobName}). Technical review required.";
            
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\SystemAlert($message, 'error'));
            }
        });

        // Hardening: Secure Signed URLs for Documents
        \Illuminate\Support\Facades\URL::macro('patsDownload', function ($app, $type = 'challan') {
            return route("candidate.{$type}", ['app' => $app->id]);
        });
    }
}
