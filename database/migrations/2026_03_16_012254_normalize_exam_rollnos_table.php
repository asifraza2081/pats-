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
        Schema::table('exam_rollnos', function (Blueprint $table) {
            $table->dropColumn(['test_date', 'reporting_time', 'start_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_rollnos', function (Blueprint $table) {
            $table->date('test_date')->nullable();
            $table->time('reporting_time')->nullable();
            $table->time('start_time')->nullable();
        });
    }
};
