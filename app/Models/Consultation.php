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
        'reason_for_visit',
        'symptoms',
        'notes',
        'diagnosis',
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id'); 
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

}
