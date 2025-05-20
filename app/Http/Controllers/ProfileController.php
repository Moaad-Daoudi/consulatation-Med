<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Storage;
use App\Models\User; // Not strictly needed if always using $request->user()
use App\Models\Doctor; // Not strictly needed if using $user->doctor relationship

class ProfileController extends Controller
{
    /**
     * Update the user's profile information.
     * This method handles the POST/PATCH request from the profile form.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user(); // Get the authenticated user

        // --- Validate User Data ---
        $validatedUserData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^[+\/\s\-\(\)0-9]*$/'], // Basic phone regex
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'], // Max 2MB
        ]);

        // --- Handle Photo Upload ---
        if ($request->hasFile('photo')) {
            // Delete old photo if it exists
            if ($user->photo_path && Storage::disk('public')->exists($user->photo_path)) {
                Storage::disk('public')->delete($user->photo_path);
            }
            // Store new photo (e.g., in storage/app/public/profile_photos)
            $path = $request->file('photo')->store('profile_photos', 'public');
            $validatedUserData['photo_path'] = $path;
        }

        // --- Update User Model ---
        $user->fill($validatedUserData); // Fill with validated data (name, email, phone, photo_path if new)

        // If email was changed, mark for re-verification (if your app uses it)
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
        $user->save(); // Save user changes

        // --- Doctor-Specific Updates (only if the user is a doctor) ---
        if ($user->role && $user->role->name === 'doctor') {
            $validatedDoctorData = $request->validate([
                'specialty' => ['required', 'string', 'max:255'],
                'bio' => ['nullable', 'string', 'max:1000'],
                'practice_address' => ['nullable', 'string', 'max:500'],
            ]);

            // Get or create the doctor profile associated with this user
            $doctor = $user->doctor()->firstOrNew(['user_id' => $user->id]);

            // Fill doctor-specific data
            $doctor->specialty = $validatedDoctorData['specialty'];
            $doctor->bio = $validatedDoctorData['bio'];
            $doctor->practice_address = $validatedDoctorData['practice_address'];
            $doctor->save(); // Save doctor changes
        }

        // Redirect back to the dashboard, specifically to the 'parametres' (profile) section
        return Redirect::route('dashboard')
                       ->with('status', 'profile-updated') // For success message
                       ->with('active_section_on_load', 'parametres'); // To re-open the profile section
    }

    // If you have a dedicated page for profile editing (GET /profile), you might have an edit() method:
    // public function edit(Request $request): \Illuminate\View\View
    // {
    //     return view('profile.edit', [ // Assuming you have a profile.edit blade view
    //         'user' => $request->user(),
    //     ]);
    // }

    // destroy() method for account deletion if needed (as in Breeze/Jetstream)
    // public function destroy(Request $request): RedirectResponse
    // {
    //     $request->validateWithBag('userDeletion', [
    //         'password' => ['required', 'current_password'],
    //     ]);
    //     $user = $request->user();
    //     Auth::logout();
    //     $user->delete(); // This should cascade to delete doctor record if onDelete('cascade') is set
    //     $request->session()->invalidate();
    //     $request->session()->regenerateToken();
    //     return Redirect::to('/');
    // }
}
