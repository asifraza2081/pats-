<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('test_centers', function (Blueprint $table) {
            $table->id();
            $table->string('tcid', 10)->unique()->comment('Admin-assigned, e.g. 3001');
            $table->string('name', 150);
            $table->foreignId('city_id')->constrained('cities')->restrictOnDelete();
            $table->text('address')->nullable();
            $table->string('map_url')->nullable();
            $table->smallInteger('seating_capacity')->unsigned()->comment('Max candidates per batch');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('project_centers', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('center_id')->constrained('test_centers')->cascadeOnDelete();
            $table->primary(['project_id', 'center_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_centers');
        Schema::dropIfExists('test_centers');
    }
};
