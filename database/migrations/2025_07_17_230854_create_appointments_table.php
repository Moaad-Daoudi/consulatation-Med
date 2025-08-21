<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            // CORRECT: Links to the 'id' on your 'users' table.
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();

            // CORRECT: Also links to the 'id' on your 'users' table.
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();

            $table->dateTime('appointment_datetime');
            $table->text('notes')->nullable();
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};