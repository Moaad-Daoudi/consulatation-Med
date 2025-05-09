<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ensure patient profile exists
        $patient = Auth::user()->patient;
        if (!$patient) {
            return redirect()->route('patient.profile.create');
        }
        
        // Get upcoming appointments
        $upcomingAppointments = $patient->appointments()
            ->where('scheduled_at', '>=', Carbon::now())
            ->where('status', '!=', 'cancelled')
            ->orderBy('scheduled_at')
            ->with('doctor.user')
            ->get();
        
        // Get past appointments
        $pastAppointments = $patient->appointments()
            ->where('scheduled_at', '<', Carbon::now())
            ->orWhere('status', 'completed')
            ->orderBy('scheduled_at', 'desc')
            ->with('doctor.user')
            ->take(5)
            ->get();
        
        // Get doctors list for new appointments
        $doctors = Doctor::with('user')->get();
        
        // Count statistics
        $totalAppointments = $patient->appointments()->count();
        $completedAppointments = $patient->appointments()->where('status', 'completed')->count();
        $upcomingCount = $upcomingAppointments->count();
        
        return view('patient.dashboard', compact(
            'upcomingAppointments',
            'pastAppointments',
            'doctors',
            'totalAppointments',
            'completedAppointments',
            'upcomingCount',
            'patient'
        ));
    }
}