<?php

namespace App\Http\Controllers\Doctor;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class AppointmentController extends DoctorBaseController
{
    /**
     * Display a paginated and filterable list of the doctor's appointments.
     */
    public function index(Request $request)
    {
        $appointmentsQuery = Appointment::where('doctor_id', Auth::id())
                                        ->with('patient');

        if ($request->filled('filter_date')) {
            $appointmentsQuery->whereDate('appointment_datetime', Carbon::parse($request->input('filter_date')));
        } elseif ($request->filled('filter_period')) {
            $period = $request->input('filter_period');
            if ($period === 'today') {
                $appointmentsQuery->whereDate('appointment_datetime', Carbon::today());
            } elseif ($period === 'this_week') {
                $appointmentsQuery->whereBetween('appointment_datetime', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            } elseif ($period === 'this_month') {
                $appointmentsQuery->whereMonth('appointment_datetime', Carbon::now()->month)->whereYear('appointment_datetime', Carbon::now()->year);
            }
        } else {
            $appointmentsQuery->where('appointment_datetime', '>=', Carbon::today()->startOfDay());
        }

        $appointments = $appointmentsQuery->orderBy('appointment_datetime', 'asc')
                                          ->paginate(15); 
        return view('doctor.appointments', compact('appointments'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|regex:/^\d{2}:\d{2}$/',
            'notes' => 'nullable|string|max:1000',
            'form_source' => 'required|string', 
        ]);

        $patientId = $validatedData['patient_id'];
        $appointmentDate = Carbon::parse($validatedData['appointment_date']);

        $existingAppointment = Appointment::where('patient_id', $patientId)
            ->whereDate('appointment_datetime', $appointmentDate) 
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($existingAppointment) {
            return redirect()->back()
                ->with('error', 'Ce patient a déjà un rendez-vous prévu pour ce jour.')
                ->withInput(); 
        }

        $appointmentDatetime = Carbon::parse($validatedData['appointment_date'] . ' ' . $validatedData['appointment_time']);

        Appointment::create([
            'doctor_id' => Auth::id(),
            'patient_id' => $validatedData['patient_id'],
            'appointment_datetime' => $appointmentDatetime,
            'notes' => $validatedData['notes'],
            'status' => 'scheduled',
        ]);

        return redirect()->route('doctor.appointments')->with('success', 'Rendez-vous créé avec succès !');
    }

    /**
     * Mark a specific appointment as 'completed'.
     */
    public function markAsCompleted(Appointment $appointment)
    {
        if ($appointment->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->update(['status' => 'completed']);

        return redirect()->route('doctor.appointments')->with('success', 'Rendez-vous marqué comme terminé.');
    }

    /**
     * Delete an appointment.
     */
    public function destroy(Appointment $appointment)
    {
        if ($appointment->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $appointment->delete();

        return redirect()->route('doctor.appointments')->with('success', 'Rendez-vous supprimé avec succès.');
    }
}