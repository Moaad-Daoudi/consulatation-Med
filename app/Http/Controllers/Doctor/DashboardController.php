<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Appointment;
use App\Models\Consultation;
use App\Models\Prescription;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DashboardController extends DoctorBaseController
{
    public function index(Request $request)
    {
        $doctorId = Auth::id();

        $appointmentsTodayCount = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_datetime', Carbon::today())
            ->where('status', '!=', 'cancelled')
            ->count();

        $totalUniquePatientsCount = Consultation::where('doctor_id', $doctorId)
            ->distinct('patient_id')
            ->count('patient_id');

        $prescriptionsThisMonthCount = Prescription::where('doctor_id', $doctorId)
            ->whereMonth('prescription_date', Carbon::now()->month)
            ->whereYear('prescription_date', Carbon::now()->year)
            ->count();

        $recentActivities = $this->getRecentActivities($doctorId);

        return view('doctor.dashboard', [
            'appointmentsTodayCount' => $appointmentsTodayCount,
            'totalUniquePatientsCount' => $totalUniquePatientsCount,
            'prescriptionsThisMonthCount' => $prescriptionsThisMonthCount,
            'recentActivities' => $recentActivities,
        ]);
    }

    private function getRecentActivities(int $doctorId)
    {
        $recentAppointments = Appointment::where('doctor_id', $doctorId)
            ->with('patient') 
            ->latest('appointment_datetime')
            ->limit(3)->get();

        $recentConsultations = Consultation::where('doctor_id', $doctorId)
            ->with('patient')
            ->latest('consultation_date')
            ->limit(3)->get();
        
        $recentPrescriptions = Prescription::where('doctor_id', $doctorId)
            ->with('patient')
            ->withCount('items') 
            ->latest('prescription_date')
            ->limit(3)->get();

        $activities = collect(); 
        foreach ($recentAppointments as $appt) {
            $activities->push([
                'type' => 'Rendez-vous',
                'date' => $appt->appointment_datetime,
                'patient_name' => $appt->patient->name ?? 'Patient Inconnu',
                'description' => 'RDV pour: ' . Str::limit($appt->notes, 50),
                'status' => ucfirst($appt->status),
            ]);
        }
        
        foreach ($recentConsultations as $consult) {
            $activities->push([
                'type' => 'Consultation',
                'date' => $consult->consultation_date,
                'patient_name' => $consult->patient->name ?? 'Patient Inconnu',
                'description' => 'Motif: ' . Str::limit($consult->reason_for_visit, 50),
                'status' => 'Terminé',
            ]);
        }

        foreach ($recentPrescriptions as $presc) {
             $activities->push([
                'type' => 'Ordonnance',
                'date' => $presc->prescription_date,
                'patient_name' => $presc->patient->name ?? 'Patient Inconnu',
                'description' => $presc->items_count . ' médicament(s) prescrit(s)',
                'status' => 'Délivrée',
            ]);
        }
        
        return $activities->sortByDesc('date')->take(5);
    }
}