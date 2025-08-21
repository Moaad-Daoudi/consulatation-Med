<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime', 'password' => 'hashed'];

    // --- RELATIONSHIPS ---

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }
    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }
    public function doctorAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }
    public function patientAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }
    public function givenConsultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }
    public function receivedConsultations(): HasMany
    {
        return $this->hasMany(Consultation::class, 'patient_id');
    }
    public function writtenPrescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'doctor_id');
    }
    public function receivedPrescriptions(): HasMany
    {
        return $this->hasMany(Prescription::class, 'patient_id');
    }

    // --- ACCESSORS ---

    public function getInitialsAttribute(): string
    {
        $nameParts = explode(' ', $this->name, 2);
        $initials = strtoupper(substr($nameParts[0], 0, 1));
        if (isset($nameParts[1])) {
            $initials .= strtoupper(substr($nameParts[1], 0, 1));
        } elseif (strlen($nameParts[0]) > 1) {
            $initials = strtoupper(substr($nameParts[0], 0, 2));
        }
        return $initials;
    }

    public function getPhotoUrlAttribute(): string
    {
        $path = null;

        if ($this->doctor && $this->doctor->photo_path) {
            $path = $this->doctor->photo_path;
        }
        if ($path && Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        return asset('dashboard/patients.png');
    }
}
