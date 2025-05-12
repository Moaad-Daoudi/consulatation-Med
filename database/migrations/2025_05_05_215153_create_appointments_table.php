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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->dateTime('appointment_datetime'); // Ensure this is your chosen datetime column name
            $table->enum('status', ['pending', 'confirmed', 'scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable(); // This is for appointment details/reason
            $table->timestamps();

            // Ensure unique constraint uses the correct datetime column name
            $table->unique(['doctor_id', 'appointment_datetime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
