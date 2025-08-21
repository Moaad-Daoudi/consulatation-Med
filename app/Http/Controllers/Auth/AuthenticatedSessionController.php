<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // The LoginRequest handles the authentication attempt.
        $request->authenticate();

        // The LoginRequest handles session regeneration.
        $request->session()->regenerate();

        // Now, just handle the redirect logic.
        $user = Auth::user();

        switch ($user->role->role) {
            case 'admin':
                return redirect()->intended(route('admin.dashboard'));
            case 'doctor':
                return redirect()->intended(route('doctor.dashboard'));
            case 'patient':
                return redirect()->intended(route('patient.dashboard'));
            default:
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                abort(403, 'You do not have a valid role to access this system.');
        }
    }
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
