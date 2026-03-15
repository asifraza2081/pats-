<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

Artisan::command('inspire', function () {
    $this->comment(Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::call(function () {
    $files = Storage::disk('public')->allFiles('temp_scans');
    foreach ($files as $file) {
        if (Storage::disk('public')->lastModified($file) < now()->subDays(7)->getTimestamp()) {
            Storage::disk('public')->delete($file);
        }
    }
})->daily()->name('cleanup-temp-scans');
