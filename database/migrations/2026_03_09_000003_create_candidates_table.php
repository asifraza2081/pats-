<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            // Personal
            $table->string('father_name', 120)->nullable();
            $table->date('dob')->nullable();
            $table->enum('gender', ['Male', 'Female'])->nullable();
            $table->enum('marital_status', ['Single', 'Married', 'Divorced', 'Widowed'])->nullable();
            $table->enum('religion', ['Islam', 'Christianity', 'Hinduism', 'Sikhism', 'Other'])->nullable();
            $table->string('blood_group', 5)->nullable();
            $table->string('current_occupation', 120)->nullable();
            // Disability
            $table->boolean('disability')->default(false);
            $table->string('disability_type', 120)->nullable();
            // Domicile & Address
            $table->string('province_of_domicile', 80)->nullable();
            $table->string('district_of_domicile', 80)->nullable();
            $table->text('permanent_address')->nullable();
            $table->text('postal_address')->nullable();
            $table->boolean('same_postal_address')->default(false);
            $table->string('alternate_phone', 15)->nullable();
            // Documents
            $table->string('photo_path')->nullable();
            $table->string('cnic_front_path')->nullable();
            // Lock
            $table->boolean('profile_locked')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
