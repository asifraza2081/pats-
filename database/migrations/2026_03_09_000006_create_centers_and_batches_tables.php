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
            $table->string('city', 80);
            $table->string('province', 80);
            $table->text('address')->nullable();
            $table->string('map_url')->nullable();
            $table->smallInteger('total_capacity')->unsigned()->comment('Max candidates per batch');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('project_centers', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('center_id')->constrained('test_centers')->cascadeOnDelete();
            $table->primary(['project_id', 'center_id']);
        });

        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('center_id')->constrained('test_centers');
            $table->unsignedTinyInteger('batch_number')->default(1)->comment('e.g. 1=BATCH-1');
            $table->date('test_date');
            $table->time('reporting_time')->comment('Printed on slip');
            $table->time('start_time')->comment('Slip download disabled at this time');
            $table->smallInteger('total_seats')->unsigned();
            $table->smallInteger('booked_seats')->unsigned()->default(0);
            $table->unsignedTinyInteger('envelope_size')->default(30)->comment('Candidates per paper envelope');
            $table->timestamps();

            $table->index(['project_id', 'center_id', 'test_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
        Schema::dropIfExists('project_centers');
        Schema::dropIfExists('test_centers');
    }
};
