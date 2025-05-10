<?php

namespace App\Http\Controllers\Doctor; // Note the Doctor namespace

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Registered; // Optional: if you want to fire this event

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
}
