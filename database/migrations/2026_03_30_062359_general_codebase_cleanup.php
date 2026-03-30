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
        // Drop unused legacy table
        Schema::dropIfExists('audit_log');
    }

    public function down(): void
    {
        // No simple reverse for dropIfExists without knowing full schema
    }
};
