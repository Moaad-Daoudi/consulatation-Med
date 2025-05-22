<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Consultation;
use App\Models\Prescription;

class PatientManagementController extends Controller
{

    public function storeFromModal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('dashboard')
                ->withErrors($validator, 'addPatientModal')
                ->withInput()
                ->with('open_modal_on_load', 'add-patient-modal');
        }

        $patientRole = Role::where('name', 'patient')->first();

        if (!$patientRole) {
            return redirect()->route('dashboard')
                ->with('error', 'Erreur critique: Le rôle "patient" n\'est pas configuré dans le système.')
                ->with('open_modal_on_load', 'add-patient-modal');
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $patientRole->id,
            ]);

            return redirect()->route('dashboard')
                   ->with('success', 'Patient "'. $user->name .'" ajouté avec succès!')
                   ->with('open_modal_on_load', 'doctor-create-appointment-modal');

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating patient from modal: '. $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Une erreur est survenue lors de la création du patient. Veuillez réessayer.')
                ->with('open_modal_on_load', 'add-patient-modal');
        }
    }

    public function showDossier(User $patient)
    {
        if (!$patient->role || $patient->role->name !== 'patient') {
            return response()->json(['error' => 'L\'utilisateur spécifié n\'est pas un patient.'], 404);
        }

        $doctorId = Auth::id();

        $hasConsultation = Consultation::where('doctor_id', $doctorId)
                                       ->where('patient_id', $patient->id)
                                       ->exists();

        $hasPrescription = Prescription::where('doctor_id', $doctorId)
                                       ->where('patient_id', $patient->id)
                                       ->exists();

        if (!$hasConsultation && !$hasPrescription) {
            return response()->json(['error' => 'Accès non autorisé à ce dossier patient.'], 403);
        }

        $patient->loadCount([
            'patientConsultations as consultations_count_with_this_doctor' => function ($query) use ($doctorId) {
                $query->where('doctor_id', $doctorId);
            },
            'receivedPrescriptions as prescriptions_count_from_this_doctor' => function ($query) use ($doctorId) {
                $query->where('doctor_id', $doctorId);
            }
        ]);

        $patient->load([
            'patientConsultations' => function ($query) use ($doctorId) {
                $query->where('doctor_id', $doctorId)
                      ->with('appointment')
                      ->latest('consultation_date');
            },
            'receivedPrescriptions' => function ($query) use ($doctorId) {
                $query->where('doctor_id', $doctorId)
                      ->with(['items', 'consultation'])
                      ->latest('prescription_date');
            }
        ]);

        return response()->json($patient);
    }
}
