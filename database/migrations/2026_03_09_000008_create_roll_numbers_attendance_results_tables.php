<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roll_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('applications')->cascadeOnDelete();
            // Format: TCID_prefix(3) + job_code(2) + serial(4) = 9 chars, e.g. 301020061
            $table->string('roll_number', 20)->unique();
            // Admin bulk-sets when slips are released for download
            $table->boolean('slip_ready')->default(false);
            $table->timestamp('assigned_at')->useCurrent();
        });

        Schema::create('attendance_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('batches')->cascadeOnDelete();
            $table->string('file_path');
            $table->unsignedTinyInteger('page_number')->default(1);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('uploaded_at')->useCurrent();
        });

        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('applications')->cascadeOnDelete();
            $table->string('roll_number', 20)->index();
            $table->decimal('score', 6, 2)->nullable();
            $table->decimal('total_marks', 6, 2)->nullable();
            $table->decimal('percentage', 5, 2)->nullable();
            $table->decimal('percentile', 5, 2)->nullable();
            $table->enum('result_status', ['pass', 'fail', 'absent', 'withheld'])->default('fail');
            $table->string('remarks', 255)->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('results');
        Schema::dropIfExists('attendance_scans');
        Schema::dropIfExists('roll_numbers');
    }
};
