<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('org_name', 200);
            $table->string('logo_path')->nullable();
            $table->longText('description')->nullable();
            $table->date('open_date')->nullable();
            $table->date('close_date')->nullable();
            $table->date('test_date')->nullable();
            $table->enum('status', ['draft', 'open', 'closed', 'result_declared'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('pats_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->unsignedTinyInteger('job_code')->comment('Sequential per project, used in roll number');
            // Basic info
            $table->string('title', 150);
            $table->string('department', 120)->nullable();
            $table->string('bps_grade', 20)->nullable();
            $table->smallInteger('total_seats')->unsigned();
            $table->text('quota_notes')->nullable();
            $table->decimal('fee', 8, 2);
            // Eligibility
            $table->tinyInteger('min_degree_level')->unsigned()->nullable();
            $table->string('min_qualification_name', 100)->nullable();
            $table->string('required_subject', 100)->nullable();
            $table->decimal('min_experience_years', 4, 1)->default(0);
            $table->enum('experience_sector', ['Any', 'Public', 'Private'])->default('Any');
            $table->tinyInteger('age_min')->unsigned()->nullable();
            $table->tinyInteger('age_max')->unsigned()->nullable();
            $table->string('domicile_required', 80)->nullable();
            $table->timestamps();

            $table->unique(['project_id', 'job_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pats_jobs');
        Schema::dropIfExists('projects');
    }
};
