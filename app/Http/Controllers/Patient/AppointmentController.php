<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a list of the patient's upcoming and past appointments.
     */
    public function index()
    {
        $patientId = Auth::id();
        $now = Carbon::now();

        $upcomingAppointments = Appointment::where('patient_id', $patientId)
            ->where('appointment_datetime', '>=', $now)
            ->with('doctor') 
            ->orderBy('appointment_datetime', 'asc')
            ->get();

        $pastAppointments = Appointment::where('patient_id', $patientId)
            ->where('appointment_datetime', '<', $now)
            ->with('doctor')
            ->orderBy('appointment_datetime', 'desc')
            ->paginate(10);

        $doctors = User::whereHas('role', fn($q) => $q->where('role', 'doctor'))
                         ->orderBy('name')->get(['id', 'name']);

        return view('patient.appointments', compact(
            'upcomingAppointments',
            'pastAppointments',
            'doctors'
        ));
    }

    /**
     * Store a new appointment booked by the patient.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|regex:/^\d{2}:\d{2}$/',
            'notes' => 'nullable|string|max:1000',
        ]);

        Appointment::create([
            'patient_id' => Auth::id(),
            'doctor_id' => $validatedData['doctor_id'],
            'appointment_datetime' => $validatedData['appointment_date'] . ' ' . $validatedData['appointment_time'],
            'status' => 'scheduled',
            'notes' => $validatedData['notes'],
        ]);

        return redirect()->route('patient.appointments.index')
                         ->with('success', 'Rendez-vous pris avec succès !');
    }

    /**
     * Cancel an upcoming appointment.
     */
    public function destroy(Appointment $appointment)
    {
        if ($appointment->patient_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        if ($appointment->status !== 'scheduled' || $appointment->appointment_datetime->isPast()) {
            return redirect()->route('patient.appointments.index')
                             ->with('error', 'Ce rendez-vous ne peut plus être annulé.');
        }

        $appointment->update(['status' => 'cancelled']);

        return redirect()->route('patient.appointments.index')
                         ->with('success', 'Rendez-vous annulé avec succès.');
    }
}