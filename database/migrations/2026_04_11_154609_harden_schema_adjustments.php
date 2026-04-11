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
        Schema::table('batches', function (Blueprint $table) {
            $table->unsignedSmallInteger('duration_minutes')->default(240)->after('start_time')->comment('Estimated session length');
        });

        Schema::table('pats_jobs', function (Blueprint $table) {
            $table->unsignedInteger('job_code')->change();
        });
    }

    public function down(): void
    {
        Schema::table('pats_jobs', function (Blueprint $table) {
            $table->unsignedTinyInteger('job_code')->change();
        });

        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('duration_minutes');
        });
    }
};
