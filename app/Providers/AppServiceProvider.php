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
        // @active('pattern' or 'route.name') — adds 'active' class when URL or Route name matches
        Blade::directive('active', function ($expression) {
            return "<?php echo (request()->is($expression) || request()->routeIs($expression)) ? 'active' : ''; ?>";
        });
    }
}
