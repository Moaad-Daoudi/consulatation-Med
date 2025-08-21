<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Doctor\DoctorBaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\User; 

class ProfileController extends DoctorBaseController
{
    /**
     * Show the form for editing the doctor's profile.
     */
    public function edit()
    {
        $doctorUser = User::findOrFail(Auth::id());

        $doctorUser->load('doctor');

        return view('doctor.profile', compact('doctorUser'));
    }

    /**
     * Update the doctor's profile information.
     */
    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'specialisation' => ['required', 'string', 'max:255'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'biography' => ['nullable', 'string', 'max:5000'],
            'gender' => ['required', 'string', Rule::in(['male', 'female', 'other'])],
        ]);
        
        $doctorProfile = $user->doctor()->firstOrNew(['user_id' => $user->id]);

        if ($request->hasFile('photo')) {
            if ($doctorProfile->photo_path && Storage::disk('public')->exists($doctorProfile->photo_path)) {
                Storage::disk('public')->delete($doctorProfile->photo_path);
            }
            $path = $request->file('photo')->store('profile_photos', 'public');
            $doctorProfile->photo_path = $path;
        }

        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        $doctorProfile->specialisation = $validatedData['specialisation'];
        $doctorProfile->phone_number = $validatedData['phone_number'];
        $doctorProfile->biography = $validatedData['biography'];
        $doctorProfile->gender = $validatedData['gender'];
        $doctorProfile->save();


        return redirect()->route('doctor.profile.edit')
                         ->with('success', 'Profil mis à jour avec succès !');
    }
}