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
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('trainingID')->unique();
            $table->string('training_title');
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime');
            $table->string('duration'); // Combine duration & unit here
            $table->string('status')->default('Upcoming'); // Add status column with default value
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trainings');
    }
};
