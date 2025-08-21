<?php

namespace App\Http\Controllers\Doctor;

use Illuminate\Http\Request;
use App\Models\Consultation;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ConsultationController extends DoctorBaseController
{
    /**
     * Display a list of the doctor's consultations.
     */
    public function index()
    {
        $consultations = Consultation::where('doctor_id', Auth::id())
            ->with('patient')
            ->latest('consultation_date')
            ->paginate(15);

        return view('doctor.consultations', compact('consultations'));
    }

    /**
     * Store a newly created consultation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:users,id',
            'consultation_date' => 'required|date',
            'reason_for_visit' => 'required|string|max:500',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Auth::user()->givenConsultations()->create($validated);

        return redirect()->route('doctor.consultations.index')
                         ->with('success', 'Consultation enregistrée avec succès.');
    }

    /**
     * Update the specified consultation in storage.
     */
    public function update(Request $request, Consultation $consultation)
    {
        if ($consultation->doctor_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'consultation_date' => 'required|date',
            'reason_for_visit' => 'required|string|max:500',
            'symptoms' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $consultation->update($validated);

        return redirect()->route('doctor.consultations.index')
                         ->with('success', 'Consultation mise à jour avec succès.');
    }

    /**
     * Remove the specified consultation from storage.
     */
    public function destroy(Consultation $consultation)
    {
        if ($consultation->doctor_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }
        
        $consultation->delete();

        return redirect()->route('doctor.consultations.index')
                         ->with('success', 'Consultation supprimée avec succès.');
    }
}