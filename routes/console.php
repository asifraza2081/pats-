<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $paths = ['temp_scans', 'exports'];
    foreach ($paths as $path) {
        $files = Storage::disk('public')->allFiles($path);
        foreach ($files as $file) {
            if (Storage::disk('public')->lastModified($file) < now()->subDays(7)->getTimestamp()) {
                Storage::disk('public')->delete($file);
            }
        }
    }
})->weekly()->name('cleanup-temp-files');
