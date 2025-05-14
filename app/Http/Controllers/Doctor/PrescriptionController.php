<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\Consultation;
use App\Models\Appointment;

class PrescriptionController extends Controller
{
    /**
     * Display a listing of the resource. (History of prescriptions for the doctor)
     * This will be loaded via the main dashboard route for the SPA section.
     */
    public function index()
    {
        // Data for the history list will be fetched by the main dashboard route
        // and passed to the view. This method isn't strictly needed for SPA display
        // but is good for API or traditional page.
        $prescriptions = Prescription::where('doctor_id', Auth::id())
            ->with(['patient', 'items'])
            ->latest('prescription_date')
            ->paginate(10);
        // In SPA, this data is usually fetched in the dashboard route.
        return view('doctor.prescriptions.index_spa_partial', compact('prescriptions')); // Or however you handle SPA sections
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:users,id',
            'prescription_date' => 'required|date',
            'general_notes' => 'nullable|string',
            'consultation_id' => 'nullable|exists:consultations,id', // Optional: link to consultation
            'medications' => 'required|array|min:1',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'nullable|string|max:255',
            'medications.*.frequency' => 'nullable|string|max:255',
            'medications.*.duration' => 'nullable|string|max:255',
            'medications.*.notes' => 'nullable|string',
        ], [
            'patient_id.required' => 'Veuillez sélectionner un patient.',
            'prescription_date.required' => 'La date de l\'ordonnance est requise.',
            'medications.required' => 'Veuillez ajouter au moins un médicament.',
            'medications.min' => 'Veuillez ajouter au moins un médicament.',
            'medications.*.name.required' => 'Le nom du médicament est requis.',
        ]);

        // For SPA, redirecting with errors needs specific handling to reopen modal/section
        $errorBagName = 'prescriptionCreate';
        $sectionToReopen = 'ordonnances'; // Your SPA section ID

        if ($validator->fails()) {
            return redirect()->route('dashboard')
                ->withErrors($validator, $errorBagName)
                ->withInput()
                ->with('active_section_on_load', $sectionToReopen);
                // ->with('open_modal_on_load', 'createPrescriptionModal'); // If you use a modal
        }

        DB::beginTransaction();
        try {
            $prescription = Prescription::create([
                'doctor_id' => Auth::id(),
                'patient_id' => $request->patient_id,
                'prescription_date' => $request->prescription_date,
                'general_notes' => $request->general_notes,
                'consultation_id' => $request->consultation_id,
            ]);

            foreach ($request->medications as $medData) {
                $prescription->items()->create([
                    'medication_name' => $medData['name'],
                    'dosage' => $medData['dosage'],
                    'frequency' => $medData['frequency'],
                    'duration' => $medData['duration'],
                    'notes' => $medData['notes'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('dashboard')
                ->with('success', 'Ordonnance créée avec succès.')
                ->with('active_section_on_load', $sectionToReopen);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating prescription: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('dashboard')
                ->with('error', 'Erreur lors de la création de l\'ordonnance.')
                ->with('active_section_on_load', $sectionToReopen)
                ->withInput();
        }
    }

    /**
     * Display the specified resource. (Show/View Prescription)
     * For SPA, this might populate a modal.
     */
    public function show(Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            abort(403);
        }
        $prescription->load(['patient', 'items', 'consultation']); // Eager load needed data
        // For SPA, you might return JSON or redirect to dashboard with data to open a modal
        return response()->json($prescription); // Example for AJAX modal
    }

    /**
     * Show the form for editing the specified resource.
     * For SPA, this data would populate an edit modal.
     */
    public function edit(Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            abort(403);
        }
        $prescription->load(['patient', 'items']);
        // For SPA, redirect to dashboard signaling to open edit modal with this data
        // This is just a placeholder for traditional forms.
        // The main dashboard route would provide patientsForModal
        $patientsForModal = User::whereHas('role', fn($q) => $q->where('name', 'patient'))->orderBy('name')->get(['id', 'name']);

        return redirect()->route('dashboard')
            ->with('editing_prescription', $prescription->toArray()) // Send all data needed
            ->with('patientsForModal', $patientsForModal) // If needed for patient dropdown in edit
            ->with('active_section_on_load', 'ordonnances')
            ->with('open_modal_on_load', 'editPrescriptionModal'); // Signal JS
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:users,id',
            'prescription_date' => 'required|date',
            'general_notes' => 'nullable|string',
            'consultation_id' => 'nullable|exists:consultations,id',
            'medications' => 'required|array|min:1',
            'medications.*.id' => 'nullable|sometimes|exists:prescription_items,id,prescription_id,'.$prescription->id, // For existing items
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'nullable|string|max:255',
            'medications.*.frequency' => 'nullable|string|max:255',
            'medications.*.duration' => 'nullable|string|max:255',
            'medications.*.notes' => 'nullable|string',
        ]);

        $errorBagName = 'prescriptionEdit_' . $prescription->id;
        $sectionToReopen = 'ordonnances';

        if ($validator->fails()) {
            return redirect()->route('dashboard')
                ->withErrors($validator, $errorBagName)
                ->withInput()
                ->with('active_section_on_load', $sectionToReopen)
                ->with('open_modal_on_load', 'editPrescriptionModal') // Signal JS
                ->with('prescription_id_for_error_bag', $prescription->id);
        }

        DB::beginTransaction();
        try {
            $prescription->update([
                'patient_id' => $request->patient_id,
                'prescription_date' => $request->prescription_date,
                'general_notes' => $request->general_notes,
                'consultation_id' => $request->consultation_id,
            ]);

            $existingItemIds = $prescription->items()->pluck('id')->toArray();
            $newItemIds = [];

            foreach ($request->medications as $medData) {
                if (!empty($medData['id']) && in_array($medData['id'], $existingItemIds)) {
                    // Update existing item
                    $item = PrescriptionItem::find($medData['id']);
                    if ($item) {
                        $item->update([
                            'medication_name' => $medData['name'],
                            'dosage' => $medData['dosage'],
                            'frequency' => $medData['frequency'],
                            'duration' => $medData['duration'],
                            'notes' => $medData['notes'] ?? null,
                        ]);
                        $newItemIds[] = $item->id;
                    }
                } else {
                    // Create new item
                    $item = $prescription->items()->create([
                        'medication_name' => $medData['name'],
                        'dosage' => $medData['dosage'],
                        'frequency' => $medData['frequency'],
                        'duration' => $medData['duration'],
                        'notes' => $medData['notes'] ?? null,
                    ]);
                    $newItemIds[] = $item->id;
                }
            }

            // Delete items that were removed from the form
            $itemsToDelete = array_diff($existingItemIds, $newItemIds);
            PrescriptionItem::destroy($itemsToDelete);

            DB::commit();
            return redirect()->route('dashboard')
                ->with('success', 'Ordonnance mise à jour avec succès.')
                ->with('active_section_on_load', $sectionToReopen);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating prescription {$prescription->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('dashboard')
                ->with('error', 'Erreur lors de la mise à jour de l\'ordonnance.')
                ->with('active_section_on_load', $sectionToReopen)
                ->withInput()
                ->with('open_modal_on_load', 'editPrescriptionModal')
                ->with('prescription_id_for_error_bag', $prescription->id);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            return redirect()->route('dashboard')
                ->with('error', 'Action non autorisée.')
                ->with('active_section_on_load', 'ordonnances');
        }

        try {
            $prescription->delete(); // Items will be deleted due to onDelete('cascade')
            return redirect()->route('dashboard')
                ->with('success', 'Ordonnance supprimée avec succès.')
                ->with('active_section_on_load', 'ordonnances');
        } catch (\Exception $e) {
            Log::error("Error deleting prescription {$prescription->id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('dashboard')
                ->with('error', 'Erreur lors de la suppression de l\'ordonnance.')
                ->with('active_section_on_load', 'ordonnances');
        }
    }

    public function getPatientConsultationsForLinking(Request $request, User $patient)
    {
        // Authorization: Ensure the logged-in doctor can access this patient's info.
        // This is a basic check; you might have more specific patient-doctor relationship logic.
        if (Auth::user()->role->name === 'doctor' && !Consultation::where('patient_id', $patient->id)->where('doctor_id', Auth::id())->exists() && !Appointment::where('patient_id', $patient->id)->where('doctor_id', Auth::id())->exists() ) {
            // A simple check: if the doctor has no consultations or appointments with this patient.
            // You might want a direct "is my patient" check.
            // For now, let's assume if the doctor can select the patient, they can see their consultations.
        }

        if ($patient->role->name !== 'patient') {
            return response()->json(['error' => 'Invalid user type provided for patient.'], 400);
        }

        // Fetch consultations for the patient that belong to the currently authenticated doctor
        $query = Consultation::where('patient_id', $patient->id)
            ->where('doctor_id', Auth::id()); // CRUCIAL: Only for the current doctor

        // Further considerations for filtering:
        // 1. Consultations not already linked to a prescription (if one-to-one).
        //    This would require 'prescriptions' table to have 'consultation_id' as unique.
        //    Or if 'consultations' table has a 'prescription_id' to mark it as "used by a prescription".
        //    For now, we'll fetch recent ones.
        //    $query->whereDoesntHave('prescription'); // If using the hasOne relationship from Consultation to Prescription

        // 2. Date range (e.g., recent)
        $query->orderBy('consultation_date', 'desc'); // Show most recent first

        $consultations = $query->limit(30) // Sensible limit
            ->get(['id', 'consultation_date', 'reason_for_visit']);

        return response()->json($consultations);
    }
}
