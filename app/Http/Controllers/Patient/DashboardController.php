<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Prescription;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard for the authenticated patient.
     */
    public function index()
    {
        $patientId = Auth::id();
        $now = Carbon::now();

        $upcomingAppointmentCount = Appointment::where('patient_id', $patientId)
            ->where('appointment_datetime', '>=', $now)
            ->where('status', 'scheduled')
            ->count();

        $activePrescriptionsCount = Prescription::where('patient_id', $patientId)
            ->where('prescription_date', '>=', $now->subDays(30))
            ->count();

        $nextAppointment = Appointment::where('patient_id', $patientId)
            ->where('appointment_datetime', '>=', $now)
            ->where('status', 'scheduled')
            ->with('doctor') 
            ->orderBy('appointment_datetime', 'asc')
            ->first();

        return view('patient.dashboard', compact(
            'upcomingAppointmentCount',
            'activePrescriptionsCount',
            'nextAppointment'
        ));
    }
}