<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{

    public function index(Request $request)
    {

        $query = User::query();

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('email', 'LIKE', "%{$searchTerm}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        $users = $query->with('role')->latest()->paginate(5);

        return view('admin.manage_users', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('role', 'doctor', 'patient');
        return response()->json($user);
    }

    public function store(Request $request)
    {

        $validateData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'gender' => ['required', 'in:male,female'],
            'role' => ['required', 'in:patient,doctor'],
            'specialisation' => ['required_if:role,doctor', 'nullable', 'string', 'max:255'],
            'phone_number' => ['required_if:role,doctor', 'nullable', 'string', 'max:20', 'regex:/^(06|07)\d{8}$/'],
            'photo' => ['required_if:role,doctor', 'image', 'nullable', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'biography' => ['required_if:role,doctor', 'nullable', 'string', 'max:1000'],
            'date_of_birth' => ['required_if:role,patient', 'nullable', 'date', 'before:today'],
            'blood_type' => ['required_if:role,patient', 'nullable', 'in:O+,O-,A+,A-,B+,B-,AB+,AB-', 'max:3'],
        ]);

        $role_id = ($validateData['role'] == 'doctor') ? 2 : 3;

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('profile_photos', 'public');
        }

        $user = User::create([
            'name' => $validateData['name'],
            'email' => $validateData['email'],
            'password' => Hash::make($validateData['password']),
            'role_id' => $role_id,
        ]);

        if ($user->role_id == 2) {
            Doctor::create([
                'user_id' => $user->id,
                'specialisation' => $validateData['specialisation'],
                'biography' => $validateData['biography'],
                'gender' => $validateData['gender'],
                'phone_number' => $validateData['phone_number'],
                'photo_path' => $photoPath
            ]);
        } elseif ($user->role_id === 3) {
            Patient::create([
                'user_id' => $user->id,
                'date_of_birth' => $validateData['date_of_birth'],
                'gender' => $validateData['gender'],
                'blood_type' => $validateData['blood_type']
            ]);
        }

        return redirect()->route('admin.manage_users')
            ->with('success', 'User created successfully!');
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $validated['photo'] = $request->file('photo')->store('profile_photos', 'public');
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'photo' => $validated['photo'] ?? $user->photo,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => $validated['password']]);
        }

        if ($user->role_id == 2 && $user->doctor) {
            $user->doctor()->update([
                'specialisation' => $validated['specialisation'],
                'biography' => $validated['biography'],
                'gender' => $validated['gender'],
                'phone_number' => $validated['phone_number'],
            ]);
        } elseif ($user->role_id == 3 && $user->patient) {
            $user->patient()->update([
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'blood_type' => $validated['blood_type'],
            ]);
        }

        return redirect()->route('admin.manage_users')->with('success', 'User updated successfully!');
    }

    public function destroy(User $user)
    {
        if ($user->doctor && $user->doctor->photo_path) {
            Storage::disk('public')->delete($user->doctor->photo_path);
        }

        $user->delete();

        return redirect()->route('admin.manage_users')->with('success', 'User deleted successfully.');
    }
}
