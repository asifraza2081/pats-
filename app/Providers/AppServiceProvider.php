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
            if ($user->hasAnyRole(['admin', 'super_admin'])) return true;
            if ($user->hasRole('examiner')) {
                return $user->assignedCenters()->where('test_centers.id', $batch->center_id)->exists();
            }
            return false;
        });
        // @active('pattern') — adds 'active' class when URL matches
        Blade::directive('active', function ($pattern) {
            return "<?php echo request()->is({$pattern}) ? 'active' : ''; ?>";
        });
    }
}
