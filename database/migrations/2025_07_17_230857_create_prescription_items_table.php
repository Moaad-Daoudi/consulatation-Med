<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->onDelete('cascade');
            $table->string('medication_name');
            $table->string('dosage')->nullable(); // e.g., "10mg", "1 tablet"
            $table->string('frequency')->nullable(); // e.g., "3 times a day", "Once daily"
            $table->string('duration')->nullable(); // e.g., "7 days", "Until finished"
            $table->text('notes')->nullable(); // Specific notes for this medication
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescription_items');
    }
};
