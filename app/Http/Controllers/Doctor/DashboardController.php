<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Ensure doctor profile exists
        $doctor = Auth::user()->doctor;
        if (!$doctor) {
            return redirect()->route('doctor.profile.create');
        }
        
        // Get today's appointments
        $today = Carbon::today();
        $todayAppointments = $doctor->appointments()
            ->whereDate('scheduled_at', $today)
            ->orderBy('scheduled_at')
            ->with('patient.user')
            ->get();
        
        // Get upcoming appointments (future appointments excluding today)
        $upcomingAppointments = $doctor->appointments()
            ->whereDate('scheduled_at', '>', $today)
            ->orderBy('scheduled_at')
            ->with('patient.user')
            ->take(5)
            ->get();
        
        // Get recent appointments (past appointments)
        $recentAppointments = $doctor->appointments()
            ->whereDate('scheduled_at', '<', $today)
            ->orderBy('scheduled_at', 'desc')
            ->with('patient.user')
            ->take(5)
            ->get();
        
        // Count statistics
        $totalAppointments = $doctor->appointments()->count();
        $pendingAppointments = $doctor->appointments()->where('status', 'pending')->count();
        $completedAppointments = $doctor->appointments()->where('status', 'completed')->count();
        
        // Get patient count (unique patients seen by the doctor)
        $patientCount = $doctor->appointments()
            ->distinct('patient_id')
            ->count('patient_id');
        
        return view('doctor.dashboard', compact(
            'todayAppointments',
            'upcomingAppointments',
            'recentAppointments',
            'totalAppointments',
            'pendingAppointments',
            'completedAppointments',
            'patientCount',
            'doctor'
        ));
    }
}