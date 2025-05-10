<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Import for type hinting

class Appointment extends Model
{
    use HasFactory; // Enables using factories for testing/seeding

    /**
     * The table associated with the model.
     *
     * Laravel usually infers this as 'appointments' (plural, snake_case version of model name),
     * but explicitly defining it is good practice if there's any ambiguity or if you
     * want to use a different table name.
     *
     * @var string
     */
    // protected $table = 'appointments'; // Usually not needed if naming convention is followed

    /**
     * The attributes that are mass assignable.
     *
     * These are the columns in your 'appointments' table that you allow
     * to be filled using `Appointment::create([...])` or `Appointment::update([...])`.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'appointment_datetime',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * This tells Eloquent to automatically convert these attributes
     * to the specified types when you access them on the model instance.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'appointment_datetime' => 'datetime', // Ensures this is treated as a Carbon datetime object
    ];

    /**
     * Get the patient that owns the appointment.
     * Defines a BelongsTo relationship with the User model (assuming patients are users).
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the doctor that is assigned to the appointment.
     * Defines a BelongsTo relationship with the User model (assuming doctors are users).
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    /**
     * (Optional) Accessor to get a formatted appointment date.
     * Example: $appointment->formatted_date
     */
    // public function getFormattedDateAttribute(): string
    // {
    //     return $this->appointment_datetime ? $this->appointment_datetime->format('d/m/Y') : 'N/A';
    // }

    /**
     * (Optional) Accessor to get a formatted appointment time.
     * Example: $appointment->formatted_time
     */
    // public function getFormattedTimeAttribute(): string
    // {
    //     return $this->appointment_datetime ? $this->appointment_datetime->format('H:i') : 'N/A';
    // }

    /**
     * (Optional) Scope to get upcoming appointments.
     * Example: Appointment::upcoming()->get();
     */
    // public function scopeUpcoming($query)
    // {
    //     return $query->where('appointment_datetime', '>=', now())->orderBy('appointment_datetime', 'asc');
    // }

    /**
     * (Optional) Scope to get past appointments.
     * Example: Appointment::past()->get();
     */
    // public function scopePast($query)
    // {
    //     return $query->where('appointment_datetime', '<', now())->orderBy('appointment_datetime', 'desc');
    // }
}
