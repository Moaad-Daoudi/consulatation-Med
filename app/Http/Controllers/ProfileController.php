<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validatedUserDataRules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^(06|07)\d{8}$/'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ];


        $roleSpecificRules = [];
        $redirect_section = 'dashboard';

        if ($user->role) {
            if ($user->role->name === 'doctor') {
                $roleSpecificRules = [
                    'specialty' => ['required', 'string', 'max:255'],
                    'bio' => ['nullable', 'string', 'max:1000'],
                    'practice_address' => ['nullable', 'string', 'max:500'],
                ];
                $redirect_section = 'parametres';
            } elseif ($user->role->name === 'patient') {
                $roleSpecificRules = [
                    'date_of_birth' => ['required', 'date', 'before_or_equal:' . now()->format('Y-m-d')],
                    'gender' => ['required', 'string', 'in:male,female,other'],
                    'emergency_contact' => ['nullable', 'string', 'max:20', 'regex:/^(06|07)\d{8}$/'],
                ];
                $redirect_section = 'patient_settings_content';
            }
        }

        $allRules = array_merge($validatedUserDataRules, $roleSpecificRules);
        $validatedData = $request->validate($allRules);

        if ($request->hasFile('photo')) {
            if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
                Storage::disk('public')->delete($user->photo_path);
            }

            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->photo_path = $path;
        }


        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->phone_number = $validatedData['phone_number'] ?? null;
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save();

        if ($user->role) {
            if ($user->role->name === 'doctor') {
                $doctor = $user->doctor()->firstOrNew(['user_id' => $user->id]);
                $doctor->specialty = $validatedData['specialty'];
                $doctor->bio = $validatedData['bio'] ?? null;
                $doctor->practice_address = $validatedData['practice_address'] ?? null;
                $doctor->save();
            } elseif ($user->role->name === 'patient') {
                $patient = $user->patient()->firstOrNew(['user_id' => $user->id]);
                $patient->date_of_birth = $validatedData['date_of_birth'];
                $patient->gender = $validatedData['gender'];
                $patient->emergency_contact = $validatedData['emergency_contact'] ?? null;
                $patient->save();
            }
        }


        return Redirect::route('dashboard')
                       ->with('status', 'profile-updated')
                       ->with('active_section_on_load', $redirect_section);
    }

}
