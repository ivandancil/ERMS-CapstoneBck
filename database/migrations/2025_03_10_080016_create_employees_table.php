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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('employeeID')->unique();
            $table->string('lastname');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->enum('sex', ['Male', 'Female', 'Other']);
            $table->date('dateOfBirth'); // Changed to date type
            $table->enum('civilStatus', ['Single', 'Married', 'Divorced', 'Widowed']);
            $table->string('phoneNumber');
            $table->string('email')->unique(); 
            $table->text('address');
            $table->string('jobPosition');
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
