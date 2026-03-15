<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class WipeTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pats:wash-out {--force : Skip confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate application test data (results, payments, etc.) but keep core setup.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (app()->environment('production')) {
            $this->error('This command CANNOT be run in production.');
            return 1;
        }

        if (!$this->option('force') && !$this->confirm('This will DELETE all application data. Are you sure?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $tables = [
            'results',
            'attendance_scans',
            'exam_rollnos',
            'payments',
            'applications',
            'batches',
            'activity_logs',
            'examiner_assignments',
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        try {
            foreach ($tables as $table) {
                if (Schema::hasTable($table)) {
                    $this->line("Truncating {$table}...");
                    DB::table($table)->truncate();
                }
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        // Unlock candidate profiles so they can apply again for fresh testing
        DB::table('candidates')->update(['profile_locked' => false]);

        $this->info('System data washed out successfully.');
        return 0;
    }
}
