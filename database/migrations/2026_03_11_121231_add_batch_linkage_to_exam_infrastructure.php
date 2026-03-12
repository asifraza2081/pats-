<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('job_id')->constrained('batches')->nullOnDelete();
        });

        Schema::table('exam_rollnos', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('center_id')->constrained('batches')->nullOnDelete();
        });

        Schema::table('attendance_scans', function (Blueprint $table) {
            $table->foreignId('batch_id')->nullable()->after('center_id')->constrained('batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendance_scans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('batch_id');
        });

        Schema::table('exam_rollnos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('batch_id');
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('batch_id');
        });
    }
};
