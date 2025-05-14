<?php

namespace App\Http\Controllers\Doctor; // Note the Doctor namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered; // Optional: if you want to fire this event
use Illuminate\Support\Facades\Auth;
use App\Models\Consultation;
use App\Models\Prescription;

class PatientManagementController extends Controller
{
    /**
     * Store a newly created patient by a doctor from a modal form.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeFromModal(Request $request)
    {
        // Define validation rules specifically for this modal form
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'min:8', 'confirmed'], // 'confirmed' needs 'password_confirmation' field in form
            'phone' => ['nullable', 'string', 'max:20'],
            // Add any other fields from your 'add-patient-modal' form
        ]);

        // If validation fails, redirect back with errors and input,
        // and a session variable to tell JS to reopen the 'add-patient-modal'.
        if ($validator->fails()) {
            return redirect()->route('dashboard') // Or the specific route that displays the doctor dashboard
                ->withErrors($validator, 'addPatientModal') // Use a specific error bag for this modal
                ->withInput()
                ->with('open_modal_on_load', 'add-patient-modal'); // Signal to JS
        }

        $patientRole = Role::where('name', 'patient')->first();

        if (!$patientRole) {
            // This is a server configuration error if the 'patient' role doesn't exist
            return redirect()->route('dashboard')
                ->with('error', 'Erreur critique: Le rôle "patient" n\'est pas configuré dans le système.')
                ->with('open_modal_on_load', 'add-patient-modal'); // Keep modal open if possible
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $patientRole->id,
                // 'phone_number' => $request->phone, // If your User model has a phone_number field and it's in $fillable
            ]);

            // Optionally, if you have a separate Patient model/profile linked to User:
            // if ($user && method_exists($user, 'patientProfile')) {
            //    $user->patientProfile()->create([
            //        'phone' => $request->phone,
            //        // other patient-specific details
            //    ]);
            // }

            // event(new Registered($user)); // Fire event if new user registration notifications etc. are needed

            // Redirect back to the main dashboard. The patient list in the
            // "Create Appointment" modal should be re-populated on this page load (because the main dashboard fetches all patients).
            return redirect()->route('dashboard')
                   ->with('success', 'Patient "'. $user->name .'" ajouté avec succès!')
                   ->with('open_modal_on_load', 'doctor-create-appointment-modal'); // Signal to JS to reopen the *appointment* modal

        } catch (\Exception $e) {
            // Log the error for debugging
            \Illuminate\Support\Facades\Log::error('Error creating patient from modal: '. $e->getMessage());
            return redirect()->route('dashboard')
                ->with('error', 'Une erreur est survenue lors de la création du patient. Veuillez réessayer.')
                ->with('open_modal_on_load', 'add-patient-modal'); // Keep add patient modal open
        }
    }

    public function showDossier(User $patient)
    {
        // Ensure the passed user is actually a patient
        if (!$patient->role || $patient->role->name !== 'patient') {
            return response()->json(['error' => 'L\'utilisateur spécifié n\'est pas un patient.'], 404);
        }

        $doctorId = Auth::id();

        // Authorization: Check if the current doctor has had any interaction with this patient.
        // This prevents a doctor from viewing the dossier of a patient they've never seen.
        $hasConsultation = Consultation::where('doctor_id', $doctorId)
                                       ->where('patient_id', $patient->id)
                                       ->exists();

        $hasPrescription = Prescription::where('doctor_id', $doctorId)
                                       ->where('patient_id', $patient->id)
                                       ->exists();

        if (!$hasConsultation && !$hasPrescription) {
            // If you have a direct patient-doctor assignment model, you could check that too.
            // For now, if no consultations or prescriptions, deny access to this specific doctor.
            return response()->json(['error' => 'Accès non autorisé à ce dossier patient.'], 403);
        }

        // Eager load details for this patient specifically related to this doctor
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
                      ->with('appointment') // Optional: if you want to show linked appointment info
                      ->latest('consultation_date'); // Order by most recent
            },
            'receivedPrescriptions' => function ($query) use ($doctorId) {
                $query->where('doctor_id', $doctorId)
                      ->with(['items', 'consultation']) // Load items and linked consultation for each prescription
                      ->latest('prescription_date'); // Order by most recent
            }
            // Add other patient-specific profile relationships if you have them, e.g., 'patientProfile'
            // 'patientProfile'
        ]);

        // Add any other general patient details you want to return from the User model itself
        // (e.g., date_of_birth, phone if they are directly on the User model)
        // $patient->date_of_birth = $patient->date_of_birth; // Example if it exists

        return response()->json($patient);
    }
}
