<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\Role;
use App\Models\Patient;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validateData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'blood_type' => ['required', 'in:O+,O-,A+,A-,B+,B-,AB+,AB-']
        ]);

        $role = Role::where('role','patient')->first();

        $user = User::create([
            'name' => $validateData['name'],
            'email' => $validateData['email'],
            'password' => Hash::make($validateData['password']),
            'role_id' => $role->id
        ]);

        if($role->role == 'patient') {
            Patient::create([
                'user_id' => $user->id,
                'date_of_birth' => $validateData['date_of_birth'],
                'gender' => $validateData['gender'],
                'blood_type' => $validateData['blood_type']
            ]);
        }
        else {
            abort(500, 'Patient role not found. Please run database seeders.');
        }
        event(new Registered($user));

        return redirect(route('login'))->with('status', 'Account created. Please log in.');
    }
}
