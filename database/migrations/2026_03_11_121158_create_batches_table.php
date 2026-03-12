<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->foreignId('center_id')->constrained('test_centers')->restrictOnDelete();
            
            $table->unsignedTinyInteger('batch_number')->default(1)->comment('e.g. 1, 2, 3');
            $table->date('test_date');
            $table->time('reporting_time');
            $table->time('start_time');
            
            $table->unsignedSmallInteger('total_seats')->comment('Allocated seats for this shift');
            $table->unsignedSmallInteger('booked_seats')->default(0);
            
            $table->unsignedTinyInteger('envelope_size')->default(30)->comment('Candidates per envelope');
            $table->boolean('is_ready')->default(false)->comment('Slips published');
            
            $table->timestamps();
            
            // Prevent duplicate batches in same center/date/shift
            $table->unique(['project_id', 'center_id', 'test_date', 'batch_number'], 'project_center_batch_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
