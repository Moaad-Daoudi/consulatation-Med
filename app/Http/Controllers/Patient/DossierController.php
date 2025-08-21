<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DossierController extends Controller
{
    public function index()
    {
        $patientUser = User::findOrFail(Auth::id());

        $patientUser->load([
            'patient', 
            'receivedConsultations' => function ($query) {
                $query->with('doctor')->latest('consultation_date');
            },
            'receivedPrescriptions' => function ($query) {
                $query->with(['doctor', 'items'])->latest('prescription_date');
            }
        ]);

        return view('patient.dossier_medical', compact('patientUser'));
    }
}