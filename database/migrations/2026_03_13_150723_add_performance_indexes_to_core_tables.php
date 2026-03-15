<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Speed up filtering by project, status, and job - common in dashboards and allocation
            $table->index(['project_id', 'status', 'job_id'], 'idx_app_project_status_job');
            // Speed up city-based allocation logic
            $table->index(['project_id', 'status', 'desired_test_city_id'], 'idx_app_project_status_city');
            // Speed up recent applications sorting
            $table->index('applied_at');
        });

        Schema::table('users', function (Blueprint $table) {
            // Speed up search by name and phone in CandidateController
            $table->index(['first_name', 'last_name'], 'idx_user_name');
            $table->index('phone');
        });

        Schema::table('exam_rollnos', function (Blueprint $table) {
            // Add center_id to existing composite to speed up serial counting if still needed
            $table->index(['project_id', 'job_id', 'city_id', 'center_id'], 'idx_roll_lookup');
        });

        Schema::table('batches', function (Blueprint $table) {
            // Speed up allocation stats collision check
            $table->index('test_date');
        });

        Schema::table('pats_jobs', function (Blueprint $table) {
            // Speed up project-wise job lookups
            $table->index('project_id');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropIndex('idx_app_project_status_job');
            $table->dropIndex('idx_app_project_status_city');
            $table->dropIndex(['applied_at']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_user_name');
            $table->dropIndex(['phone']);
        });

        Schema::table('exam_rollnos', function (Blueprint $table) {
            $table->dropIndex('idx_roll_lookup');
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropIndex(['test_date']);
        });

        Schema::table('pats_jobs', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
        });
    }
};
