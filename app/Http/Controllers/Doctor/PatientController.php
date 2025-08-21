<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Consultation;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;

class PatientController extends DoctorBaseController
{
    public function index()
    {
        $doctorId = Auth::id();

        $patientIdsFromConsultations = Consultation::where('doctor_id', $doctorId)->distinct()->pluck('patient_id');
        $patientIdsFromPrescriptions = Prescription::where('doctor_id', $doctorId)->distinct()->pluck('patient_id');

        $distinctPatientIds = $patientIdsFromConsultations->merge($patientIdsFromPrescriptions)->unique();

        $doctorPatients = User::whereIn('id', $distinctPatientIds)
            ->whereHas('role', fn($q) => $q->where('role', 'patient')) 
            ->withCount([
                'receivedConsultations as consultations_with_doctor' => function ($query) use ($doctorId) {
                    $query->where('doctor_id', $doctorId);
                },
                'receivedPrescriptions as prescriptions_from_doctor' => function ($query) use ($doctorId) {
                    $query->where('doctor_id', $doctorId);
                }
            ])
            ->orderBy('name')
            ->paginate(12); 

        return view('doctor.patients', compact('doctorPatients'));
    }

    public function showDossier(User $patient)
    {
        $doctorId = Auth::id();

        $hasInteraction = Consultation::where('doctor_id', $doctorId)->where('patient_id', $patient->id)->exists()
                         || Prescription::where('doctor_id', $doctorId)->where('patient_id', $patient->id)->exists();

        if (!$hasInteraction) {
            return response()->json(['error' => 'Access to this patient dossier is unauthorized.'], 403);
        }

        $patient->load([
            'patientProfile', 
            'receivedConsultations' => function ($query) use ($doctorId) {
                $query->where('doctor_id', $doctorId)
                      ->latest('consultation_date');
            },
            'receivedPrescriptions' => function ($query) use ($doctorId) {
                $query->where('doctor_id', $doctorId)
                      ->with('items') 
                      ->latest('prescription_date');
            }
        ]);

        return response()->json($patient);
    }
}