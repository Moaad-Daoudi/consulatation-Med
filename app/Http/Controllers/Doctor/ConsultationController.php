<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request; // Standard Request
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator; // Make sure this is imported
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class ConsultationController extends Controller
{
    /**
     * Store a newly created consultation in storage.
     */
    public function store(Request $request) // Using Illuminate\Http\Request
    {
        $validator = Validator::make($request->all(), [ // Create the validator instance
            'patient_id' => 'required|exists:users,id',
            'consultation_date_time' => 'required|date',
            'reason_for_visit' => 'required|string|max:500',
            'symptoms' => 'required|string',
            'notes' => 'nullable|string',
            'diagnosis' => 'nullable|string',
        ], [
            'patient_id.required' => 'Veuillez sélectionner un patient pour la consultation.',
            'consultation_date_time.required' => 'La date et l\'heure de la consultation sont requises.',
            'reason_for_visit.required' => 'Le motif de la visite est requis.',
            'reason_for_visit.max' => 'Le motif ne peut pas dépasser 500 caractères.',
            'symptoms.required' => 'La description des symptômes est requise.',
        ]);

        $errorBagName = 'consultationCreate';
        $modalToReopenOnFail = 'createConsultationModal';
        $sectionToReopen = 'consultations';

        if ($validator->fails()) {
            return redirect()->route('dashboard')
                ->withErrors($validator, $errorBagName)
                ->withInput()
                ->with('active_section_on_load', $sectionToReopen)
                ->with('open_modal_on_load', $modalToReopenOnFail);
        }

        $validatedData = $validator->validated(); // Get validated data from the validator

        try {
            Consultation::create([
                'patient_id' => $validatedData['patient_id'],
                'doctor_id' => Auth::user()->id,
                'consultation_date' => $validatedData['consultation_date_time'],
                'reason_for_visit' => $validatedData['reason_for_visit'],
                'symptoms' => $validatedData['symptoms'],
                'notes' => $validatedData['notes'] ?? null,
                'diagnosis' => $validatedData['diagnosis'] ?? null,
            ]);
        } catch (\Exception $e) {
            Log::error("Error creating consultation for doctor " . Auth::id() . ": " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('dashboard')
                ->with('error', 'Erreur lors de l\'enregistrement de la consultation.')
                ->with('active_section_on_load', $sectionToReopen)
                ->with('open_modal_on_load', $modalToReopenOnFail)
                ->withInput();
        }

        return redirect()->route('dashboard')
               ->with('success', 'Consultation enregistrée avec succès.')
               ->with('active_section_on_load', $sectionToReopen);
    }

    /**
     * Show the form for editing the specified consultation.
     * In SPA context, this might not be directly hit if modal is populated by JS.
     * However, the PUT route still needs it for route model binding.
     * We can use it to signal JS to open the edit modal with old input if validation failed on update.
     */
    public function edit(Request $request, Consultation $consultation)
    {
        if ($consultation->doctor_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        // This method is mainly for route model binding and redirecting with errors.
        // The actual modal population is handled by JS.
        return redirect()->route('dashboard')
            ->with('active_section_on_load', 'consultations')
            ->with('open_modal_on_load', 'editConsultationModal')
            ->with('consultation_id_for_error_bag', $consultation->id);
    }

    /**
     * Update the specified consultation in storage.
     */
    public function update(Request $request, Consultation $consultation) // Using Illuminate\Http\Request
    {
        if ($consultation->doctor_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $validator = Validator::make($request->all(), [ // Create the validator instance
            'consultation_date_time' => 'required|date',
            'reason_for_visit' => 'required|string|max:500',
            'symptoms' => 'nullable|string',
            'notes' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            // 'appointment_id' is not typically changed during an edit of consultation from this form
        ]);

        $errorBagName = 'consultationEdit_' . $consultation->id;
        $modalToReopenOnFail = 'editConsultationModal';
        $sectionToReopen = 'consultations';

        if ($validator->fails()) {
            return redirect()->route('dashboard')
                ->withErrors($validator, $errorBagName)
                ->withInput()
                ->with('active_section_on_load', $sectionToReopen)
                ->with('open_modal_on_load', $modalToReopenOnFail)
                ->with('consultation_id_for_error_bag', $consultation->id);
        }

        $validatedData = $validator->validated(); // Get validated data from the validator

        try {
            $consultation->update([
                'consultation_date' => Carbon::parse($validatedData['consultation_date_time']),
                'reason_for_visit' => $validatedData['reason_for_visit'],
                'symptoms' => $validatedData['symptoms'] ?? $consultation->symptoms, // Keep old value if not provided
                'notes' => $validatedData['notes'] ?? $consultation->notes,
                'diagnosis' => $validatedData['diagnosis'] ?? $consultation->diagnosis,
                // 'appointment_id' and 'patient_id' are usually not updated here
            ]);
        } catch (\Exception $e) {
            Log::error("Error updating consultation {$consultation->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('dashboard')
                ->with('error', 'Erreur lors de la mise à jour de la consultation.')
                ->with('active_section_on_load', $sectionToReopen)
                ->with('open_modal_on_load', $modalToReopenOnFail)
                ->with('consultation_id_for_error_bag', $consultation->id)
                ->withInput();
        }

        return redirect()->route('dashboard')
               ->with('success', 'Consultation mise à jour avec succès.')
               ->with('active_section_on_load', $sectionToReopen);
    }

    /**
     * Remove the specified consultation from storage.
     */
    public function destroy(Consultation $consultation)
    {
        if ($consultation->doctor_id !== Auth::id()) {
            return redirect()->route('dashboard')
                ->with('error', 'Action non autorisée.')
                ->with('active_section_on_load', 'consultations');
        }

        try {
            $consultation->delete();
        } catch (\Exception $e) {
            Log::error("Error deleting consultation {$consultation->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('dashboard')
                ->with('error', 'Erreur lors de la suppression de la consultation.')
                ->with('active_section_on_load', 'consultations');
        }

        return redirect()->route('dashboard')
            ->with('success', 'Consultation supprimée avec succès.')
            ->with('active_section_on_load', 'consultations');
    }
}
