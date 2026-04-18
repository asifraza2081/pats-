<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::define('view-session', function ($user, \App\Models\Batch $batch) {
            if ($user->hasAnyRole(['admin', 'super_admin', 'data_entry'])) return true;
            if ($user->hasRole('examiner')) {
                return $user->assignedCenters()->where('test_centers.id', $batch->center_id)->exists();
            }
            return false;
        });

        Gate::define('publish results', function ($user) {
            return $user->hasAnyRole(['admin', 'super_admin']);
        });
    }
}
