<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE applications
                MODIFY COLUMN status ENUM(
                    'submitted',
                    'fee_paid',
                    'scheduled',
                    'appeared',
                    'absent',
                    'rejected',
                    'shortlisted',
                    'not_shortlisted',
                    'result_declared'
                ) NOT NULL DEFAULT 'submitted'
            ");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement("
                ALTER TABLE applications
                MODIFY COLUMN status ENUM(
                    'submitted',
                    'fee_paid',
                    'appeared',
                    'absent',
                    'result_declared'
                ) NOT NULL DEFAULT 'submitted'
            ");
        }
    }
};
