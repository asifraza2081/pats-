<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires a full MODIFY COLUMN to change an ENUM definition.
        // This adds the missing values: scheduled, rejected, shortlisted,
        // not_shortlisted — and keeps result_declared which is already in use.
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

    public function down(): void
    {
        // Revert to original 5-value ENUM.
        // WARNING: any rows with the new values will be truncated to ''.
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
};
