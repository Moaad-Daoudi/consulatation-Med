<?php

namespace App\Http\Controllers\Doctor;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Consultation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PrescriptionController extends DoctorBaseController
{
    /**
     * Display the prescription management page.
     */
    public function index()
    {
        $prescriptions = Prescription::where('doctor_id', Auth::id())
            ->with('patient')
            ->withCount('items')
            ->latest('prescription_date')
            ->paginate(10);

        return view('doctor.prescriptions', compact('prescriptions'));
    }

    public function show(Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized Action');
        }

        $prescription->load(['patient', 'items', 'consultation']);
        
        return response()->json($prescription);
    }

    public function edit(Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized Action');
        }

        $prescription->load('items');
        
        return response()->json($prescription);
    }

    /**
     * Update the specified prescription in storage.
     */
    public function update(Request $request, Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized Action');
        }

        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'prescription_date' => 'required|date',
            'consultation_id' => 'nullable|exists:consultations,id',
            'general_notes' => 'nullable|string',
            'medications' => 'required|array|min:1',
            'medications.*.id' => 'nullable|exists:prescription_items,id,prescription_id,'.$prescription->id, 
            'medications.*.medication_name' => 'required|string|max:255',
            'medications.*.dosage' => 'nullable|string|max:255',
            'medications.*.frequency' => 'nullable|string|max:255',
            'medications.*.duration' => 'nullable|string|max:255',
            'medications.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $prescription->update([
                'patient_id' => $validated['patient_id'],
                'prescription_date' => $validated['prescription_date'],
                'consultation_id' => $validated['consultation_id'],
                'general_notes' => $validated['general_notes'],
            ]);

            $submittedItemIds = [];

            foreach ($validated['medications'] as $medData) {
                if (!empty($medData['id'])) {
                    $item = \App\Models\PrescriptionItem::find($medData['id']);
                    $item->update($medData);
                    $submittedItemIds[] = $item->id;
                } else {
                    $newItem = $prescription->items()->create($medData);
                    $submittedItemIds[] = $newItem->id;
                }
            }

            $itemsToDelete = $prescription->items()->whereNotIn('id', $submittedItemIds);
            $itemsToDelete->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error updating prescription {$prescription->id}: " . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.');
        }

        return redirect()->route('doctor.prescriptions.index')
                         ->with('success', 'Ordonnance mise à jour avec succès.');
    }

    /**
     * Store a newly created prescription.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'prescription_date' => 'required|date',
            'consultation_id' => 'nullable|exists:consultations,id',
            'general_notes' => 'nullable|string',
            'medications' => 'required|array|min:1',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'nullable|string|max:255',
            'medications.*.frequency' => 'nullable|string|max:255',
            'medications.*.duration' => 'nullable|string|max:255',
            'medications.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $prescription = Prescription::create([
                'doctor_id' => Auth::id(),
                'patient_id' => $validated['patient_id'],
                'prescription_date' => $validated['prescription_date'],
                'consultation_id' => $validated['consultation_id'],
                'general_notes' => $validated['general_notes'],
            ]);

            foreach ($validated['medications'] as $medData) {
                $prescription->items()->create([
                    'medication_name' => $medData['name'],
                    'dosage' => $medData['dosage'] ?? null,
                    'frequency' => $medData['frequency'] ?? null,
                    'duration' => $medData['duration'] ?? null,
                    'notes' => $medData['notes'] ?? null,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creating prescription: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la création de l\'ordonnance.')
                ->withInput();
        }

        return redirect()->route('doctor.prescriptions.index')
                         ->with('success', 'Ordonnance créée avec succès.');
    }


    /**
     * Fetch consultations for a patient to be linked in the prescription form.
     */
    public function getPatientConsultationsForLinking(User $patient)
    {
        $consultations = Consultation::where('patient_id', $patient->id)
            ->where('doctor_id', Auth::id())
            ->latest('consultation_date')
            ->limit(20)
            ->get(['id', 'consultation_date', 'reason_for_visit']);

        return response()->json($consultations);
    }

    public function destroy(Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            abort(403, 'Unauthorized Action');
        }

        $prescription->delete();

        return redirect()->route('doctor.prescriptions.index')
                         ->with('success', 'Ordonnance supprimée avec succès.');
    }
}