<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('education_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            // 1=Matric, 2=Inter, 3=Bachelor(14yr), 4=Master(16yr), 5=MPhil, 6=PhD
            $table->tinyInteger('degree_level')->unsigned();
            $table->string('degree_name', 100);   // e.g. Matric, BA, BCS, MBA
            $table->string('subject_major', 100)->nullable();
            $table->string('institution', 150)->nullable();
            $table->year('passing_year')->nullable();
            $table->enum('marks_type', ['Marks', 'CGPA'])->default('Marks');
            $table->decimal('obtained_marks', 7, 2)->nullable();
            $table->decimal('total_marks', 7, 2)->nullable();
            $table->string('certificate_path')->nullable();
            $table->timestamps();
        });

        Schema::create('work_experience', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates')->cascadeOnDelete();
            $table->enum('job_type', ['Public', 'Private']);
            $table->string('organization_name', 150);
            $table->string('designation', 120);
            $table->date('from_date');
            $table->date('to_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_experience');
        Schema::dropIfExists('education_history');
    }
};
