<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('project_centers', function (Blueprint $table) {
            $table->foreignId('examiner_id')->nullable()->after('center_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('project_centers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('examiner_id');
        });
    }
};
