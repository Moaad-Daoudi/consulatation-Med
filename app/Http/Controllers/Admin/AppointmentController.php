<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Support\Carbon;

class AppointmentController extends Controller
{
    /**
     * Display a list of all appointments with filtering.
     */
    public function index(Request $request)
    {
        $query = Appointment::with(['patient.patient', 'doctor.doctor']); // Eager load relationships

        // Apply filters
        if ($request->filled('patient_search')) {
            $query->whereHas('patient', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->patient_search . '%');
            });
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        if ($request->filled('appointment_date')) {
            $query->whereDate('appointment_datetime', Carbon::parse($request->appointment_date));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appointments = $query->latest('appointment_datetime')->paginate(15);

        // Data for filter dropdowns and modals
        $doctors = User::whereHas('role', fn($q) => $q->where('role', 'doctor'))->orderBy('name')->get();
        $patients = User::whereHas('role', fn($q) => $q->where('role', 'patient'))->orderBy('name')->get();

        return view('admin.manage_appointments', compact('appointments', 'doctors', 'patients'));
    }

    /**
     * Store a newly created appointment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'status' => 'required|in:scheduled,completed,cancelled,missed',
        ]);

        // ===================================================================
        // NEW VALIDATION LOGIC
        // ===================================================================
        $appointmentDate = Carbon::parse($validated['appointment_date']);

        $existingAppointment = Appointment::where('patient_id', $validated['patient_id'])
            ->where('doctor_id', $validated['doctor_id'])
            ->whereDate('appointment_datetime', $appointmentDate)
            ->where('status', '!=', 'cancelled') // Ignore cancelled appointments
            ->exists();

        if ($existingAppointment) {
            // Redirect back with an error message.
            return redirect()->back()
                ->withErrors(['appointment_date' => 'Ce patient a déjà un rendez-vous avec ce médecin pour cette journée.'])
                ->withInput();
        }
        // ===================================================================

        Appointment::create([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'status' => $validated['status'],
            'appointment_datetime' => $appointmentDate->setTimeFromTimeString($validated['appointment_time']),
        ]);

        return redirect()->route('admin.appointments.index')->with('success', 'Appointment created successfully.');
    }
    /**
     * Update the specified appointment.
     */
    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'status' => 'required|in:scheduled,completed,cancelled,missed',
        ]);

        $appointment->update([
            'patient_id' => $validated['patient_id'],
            'doctor_id' => $validated['doctor_id'],
            'status' => $validated['status'],
            'appointment_datetime' => Carbon::parse($validated['appointment_date'] . ' ' . $validated['appointment_time']),
        ]);
        
        return redirect()->route('admin.appointments.index')->with('success', 'Appointment updated successfully.');
    }

    /**
     * Remove the specified appointment.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();
        return redirect()->route('admin.appointments.index')->with('success', 'Appointment deleted successfully.');
    }
}