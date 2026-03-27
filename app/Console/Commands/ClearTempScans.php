<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearTempScans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scans:clear';
    protected $description = 'Purge temporary result scans older than 24 hours';

    public function handle(): void
    {
        $files = \Illuminate\Support\Facades\Storage::disk('public')->files('temp_scans');
        $now   = time();
        $count = 0;

        foreach ($files as $file) {
            $lastModified = \Illuminate\Support\Facades\Storage::disk('public')->lastModified($file);
            if ($now - $lastModified > 24 * 3600) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($file);
                $count++;
            }
        }

        $this->info("Successfully purged {$count} temporary scans.");
    }
}
