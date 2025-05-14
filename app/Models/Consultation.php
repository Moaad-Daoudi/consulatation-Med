<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'appointment_id',
        'consultation_date',
        'reason_for_visit',  // Added (short summary)
        'symptoms',         // Detailed description
        'notes',            // Doctor's observations
        'diagnosis',        // Preliminary or final diagnosis
        // Removed 'prescription' (will be added later)
        // Removed 'follow_up_date' (can be added in a separate step)
    ];

    // Cast consultation_date as datetime
    protected $casts = [
        'consultation_date' => 'datetime',
    ];

    // Relationships
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id'); // Assuming doctors are Users
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id'); // Assuming patients are Users
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

}
