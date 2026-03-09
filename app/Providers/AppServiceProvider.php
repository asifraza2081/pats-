<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // @active('pattern') — adds 'active' class when URL matches
        Blade::directive('active', function ($pattern) {
            return "<?php echo request()->is({$pattern}) ? 'active' : ''; ?>";
        });
    }
}
