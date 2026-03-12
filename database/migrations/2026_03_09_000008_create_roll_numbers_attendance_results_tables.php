<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exam_rollnos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('applications')->cascadeOnDelete();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('job_id')->constrained('pats_jobs')->cascadeOnDelete();
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->foreignId('center_id')->constrained('test_centers')->restrictOnDelete();
            
            // Format: [ProjID][JobID][CityID][CenterID]-[Serial] = 01010203-0001
            $table->string('roll_no', 30)->unique();
            $table->string('barcode', 30)->unique();
            $table->string('batch_no', 20)->comment('e.g 1, 2, Morning, Evening');
            $table->date('test_date');
            $table->time('reporting_time');
            $table->time('start_time');
            
            $table->boolean('slip_ready')->default(false);
            $table->timestamps();
            
            // Indexes for fast slip generation and lookup
            $table->index(['project_id', 'job_id', 'city_id']);
        });

        Schema::create('attendance_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('center_id')->constrained('test_centers')->cascadeOnDelete();
            $table->date('test_date');
            $table->string('file_path');
            $table->unsignedTinyInteger('page_number')->default(1);
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamp('uploaded_at')->useCurrent();
        });

        Schema::create('results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('applications')->cascadeOnDelete();
            $table->string('roll_no', 30)->index();
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
        Schema::dropIfExists('exam_rollnos');
    }
};
