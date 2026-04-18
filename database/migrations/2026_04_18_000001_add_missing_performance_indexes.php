<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // batches: speed up publish/draft listing (index page) and groupShow query
        Schema::table('batches', function (Blueprint $table) {
            $table->index(['project_id', 'is_ready'], 'idx_batches_project_ready');
            $table->index(['test_date', 'batch_number'], 'idx_batches_date_number');
        });

        // exam_rollnos: speed up sticker range query and bulk markReady update
        Schema::table('exam_rollnos', function (Blueprint $table) {
            $table->index('roll_no', 'idx_rollno_number');
            $table->index('slip_ready', 'idx_rollno_slip_ready');
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropIndex('idx_batches_project_ready');
            $table->dropIndex('idx_batches_date_number');
        });

        Schema::table('exam_rollnos', function (Blueprint $table) {
            $table->dropIndex('idx_rollno_number');
            $table->dropIndex('idx_rollno_slip_ready');
        });
    }
};
