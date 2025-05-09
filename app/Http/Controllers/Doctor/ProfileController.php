<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function create()
    {
        // Check if doctor profile already exists
        if (Auth::user()->doctor) {
            return redirect()->route('doctor.dashboard');
        }
        
        return view('doctor.profile.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'specialty' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'license_numbre' => 'required|string|max:255',
        ]);

        Doctor::create([
            'user_id' => Auth::id(),
            'specialty' => $request->specialty,
            'bio' => $request->bio,
            'license_numbre' => $request->license_numbre,
        ]);

        return redirect()->route('doctor.dashboard');
    }

    public function edit()
    {
        $doctor = Auth::user()->doctor;
        
        if (!$doctor) {
            return redirect()->route('doctor.profile.create');
        }
        
        return view('doctor.profile.edit', compact('doctor'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'specialty' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'license_numbre' => 'required|string|max:255',
        ]);

        $doctor = Auth::user()->doctor;
        
        $doctor->update([
            'specialty' => $request->specialty,
            'bio' => $request->bio,
            'license_numbre' => $request->license_numbre,
        ]);

        return redirect()->route('doctor.profile.edit')->with('status', 'profile-updated');
    }
}