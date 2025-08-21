<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Prescription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class PrescriptionController extends Controller
{
    /**
     * Display a list of the patient's prescriptions.
     */
    public function index()
    {
        $patientId = Auth::id();
        $now = Carbon::now();

        $allPrescriptions = Prescription::where('patient_id', $patientId)
            ->with('doctor')
            ->latest('prescription_date')
            ->get();

        $activePrescriptions = $allPrescriptions->filter(function ($prescription) use ($now) {
            return $prescription->prescription_date->isAfter($now->copy()->subDays(30));
        });

        $pastPrescriptions = $allPrescriptions->filter(function ($prescription) use ($now) {
            return $prescription->prescription_date->isBefore($now->copy()->subDays(30));
        });

        return view('patient.prescriptions', compact('activePrescriptions', 'pastPrescriptions'));
    }

    /**
     * Fetch and return the details of a single prescription as JSON for the view modal.
     */
    public function show(Prescription $prescription)
    {
        if ($prescription->patient_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $prescription->load(['doctor', 'items']);

        return response()->json($prescription);
    }
}