<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('candidate_id')->constrained('candidates');
            $table->foreignId('job_id')->constrained('pats_jobs');
            $table->foreignId('project_id')->constrained('projects');
            // City preference submitted by candidate
            $table->foreignId('desired_test_city_id')->constrained('cities');
            // Age relaxation if claimed
            $table->string('age_relaxation_type', 80)->nullable();
            $table->unsignedTinyInteger('age_relaxation_years')->nullable();
            // Status lifecycle: submitted → fee_paid → appeared/absent → result_declared
            $table->enum('status', ['submitted', 'fee_paid', 'appeared', 'absent', 'result_declared'])
                  ->default('submitted');
            // Non-blocking eligibility warnings stored at apply time
            $table->json('eligibility_warnings')->nullable();
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();

            $table->unique(['candidate_id', 'job_id']);
            $table->index('status');
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->unique()->constrained('applications')->cascadeOnDelete();
            $table->string('challan_ref', 30)->unique()->comment('Generated challan reference');
            $table->decimal('amount', 8, 2);
            $table->string('bank_name', 80)->nullable();
            $table->string('branch_code', 20)->nullable();
            $table->string('transaction_id', 100)->nullable();
            $table->date('deposit_date')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->enum('status', ['pending', 'paid', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('applications');
    }
};
