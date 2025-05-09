<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function create()
    {
        // Check if patient profile already exists
        if (Auth::user()->patient) {
            return redirect()->route('patient.dashboard');
        }
        
        return view('patient.profile.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
        ]);

        Patient::create([
            'user_id' => Auth::id(),
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'email' => Auth::user()->email,
            'emergency_contact' => $request->emergency_contact,
        ]);

        return redirect()->route('patient.dashboard');
    }

    public function edit()
    {
        $patient = Auth::user()->patient;
        
        if (!$patient) {
            return redirect()->route('patient.profile.create');
        }
        
        return view('patient.profile.edit', compact('patient'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|in:male,female,other',
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
        ]);

        $patient = Auth::user()->patient;
        
        $patient->update([
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'phone' => $request->phone,
            'emergency_contact' => $request->emergency_contact,
        ]);

        return redirect()->route('patient.profile.edit')->with('status', 'profile-updated');
    }
}