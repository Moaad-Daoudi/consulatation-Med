<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $doctor = Auth::user()->doctor;
        $appointments = $doctor->appointments()
            ->with('patient.user')
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);
            
        return view('doctor.appointments.index', compact('appointments'));
    }
    
    public function show(Appointment $appointment)
    {
        // Ensure the appointment belongs to the logged-in doctor
        if ($appointment->doctor_id !== Auth::user()->doctor->id) {
            abort(403);
        }
        
        $appointment->load('patient.user');
        
        return view('doctor.appointments.show', compact('appointment'));
    }
    
    public function update(Request $request, Appointment $appointment)
    {
        // Ensure the appointment belongs to the logged-in doctor
        if ($appointment->doctor_id !== Auth::user()->doctor->id) {
            abort(403);
        }
        
        $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
            'notes' => 'nullable|string',
        ]);
        
        $appointment->update([
            'status' => $request->status,
            // Add notes field to appointments table if you want to store doctor's notes
        ]);
        
        return redirect()->route('doctor.appointments.show', $appointment)
            ->with('status', 'appointment-updated');
    }
    
    public function calendar()
    {
        $doctor = Auth::user()->doctor;
        $appointments = $doctor->appointments()
            ->with('patient.user')
            ->get();
            
        return view('doctor.appointments.calendar', compact('appointments'));
    }
    
    public function patients()
    {
        $doctor = Auth::user()->doctor;
        
        // Get unique patients who have appointments with this doctor
        $patientIds = $doctor->appointments()
            ->distinct('patient_id')
            ->pluck('patient_id');
            
        $patients = Patient::whereIn('id', $patientIds)
            ->with('user')
            ->get();
            
        return view('doctor.patients.index', compact('patients'));
    }
}