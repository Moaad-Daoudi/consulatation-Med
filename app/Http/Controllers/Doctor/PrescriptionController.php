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
    public function index()
    {
        $prescriptions = Prescription::where('doctor_id', Auth::id())
            ->with(['patient', 'items'])
            ->latest('prescription_date')
            ->paginate(10);
        return view('doctor.prescriptions.index_spa_partial', compact('prescriptions'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|exists:users,id',
            'prescription_date' => 'required|date',
            'general_notes' => 'nullable|string',
            'consultation_id' => 'nullable|exists:consultations,id',
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

        $errorBagName = 'prescriptionCreate';
        $sectionToReopen = 'ordonnances';

        if ($validator->fails()) {
            return redirect()->route('dashboard')
                ->withErrors($validator, $errorBagName)
                ->withInput()
                ->with('active_section_on_load', $sectionToReopen);
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

    public function show(Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            abort(403);
        }
        $prescription->load(['patient', 'items', 'consultation']);
        return response()->json($prescription);
    }

    public function edit(Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            abort(403);
        }
        $prescription->load(['patient', 'items']);
        $patientsForModal = User::whereHas('role', fn($q) => $q->where('name', 'patient'))->orderBy('name')->get(['id', 'name']);

        return redirect()->route('dashboard')
            ->with('editing_prescription', $prescription->toArray())
            ->with('patientsForModal', $patientsForModal)
            ->with('active_section_on_load', 'ordonnances')
            ->with('open_modal_on_load', 'editPrescriptionModal');
    }


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
                ->with('open_modal_on_load', 'editPrescriptionModal')
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


    public function destroy(Prescription $prescription)
    {
        if ($prescription->doctor_id !== Auth::id()) {
            return redirect()->route('dashboard')
                ->with('error', 'Action non autorisée.')
                ->with('active_section_on_load', 'ordonnances');
        }

        try {
            $prescription->delete();
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
        if ($patient->role->name !== 'patient') {
            return response()->json(['error' => 'Invalid user type provided for patient.'], 400);
        }

        $query = Consultation::where('patient_id', $patient->id)
            ->where('doctor_id', Auth::id());

        $query->orderBy('consultation_date', 'desc');

        $consultations = $query->limit(30)
            ->get(['id', 'consultation_date', 'reason_for_visit']);

        return response()->json($consultations);
    }
}
