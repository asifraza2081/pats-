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
            $table->text('barcode')->change(); // Use TEXT to be safe for base64 strings
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_rollnos', function (Blueprint $table) {
            $table->string('barcode', 30)->unique()->change();
        });
    }
};
