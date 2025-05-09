<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $patient = Auth::user()->patient;
        $appointments = $patient->appointments()
            ->with('doctor.user')
            ->orderBy('scheduled_at', 'desc')
            ->paginate(10);
            
        return view('patient.appointments.index', compact('appointments'));
    }
    
    public function create()
    {
        $doctors = Doctor::with('user')->get();
        return view('patient.appointments.create', compact('doctors'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'scheduled_at_date' => 'required|date|after_or_equal:today',
            'scheduled_at_time' => 'required|date_format:H:i',
            'reason' => 'required|string|max:255',
        ]);
        
        // Combine date and time into a single datetime
        $scheduledAt = Carbon::parse(
            $request->scheduled_at_date . ' ' . $request->scheduled_at_time
        );
        
        $patient = Auth::user()->patient;
        
        Appointment::create([
            'patient_id' => $patient->id,
            'doctor_id' => $request->doctor_id,
            'scheduled_at' => $scheduledAt,
            'duration' => 30, // Default 30 minutes
            'status' => 'pending',
            'reason' => $request->reason,
        ]);
        
        return redirect()->route('patient.appointments.index')
            ->with('status', 'appointment-created');
    }
    
    public function show(Appointment $appointment)
    {
        // Ensure the appointment belongs to the logged-in patient
        if ($appointment->patient_id !== Auth::user()->patient->id) {
            abort(403);
        }
        
        $appointment->load('doctor.user');
        
        return view('patient.appointments.show', compact('appointment'));
    }
    
    public function cancel(Appointment $appointment)
    {
        // Ensure the appointment belongs to the logged-in patient
        if ($appointment->patient_id !== Auth::user()->patient->id) {
            abort(403);
        }
        
        // Only allow cancellation of upcoming appointments that aren't already cancelled
        if ($appointment->scheduled_at > Carbon::now() && $appointment->status != 'cancelled') {
            $appointment->update(['status' => 'cancelled']);
            return redirect()->route('patient.appointments.index')
                ->with('status', 'appointment-cancelled');
        }
        
        return redirect()->route('patient.appointments.index')
            ->with('error', 'Unable to cancel this appointment');
    }
}