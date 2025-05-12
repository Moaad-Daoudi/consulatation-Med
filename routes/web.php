<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment as AppointmentModel; // Using an alias for clarity
use Illuminate\Support\Carbon;

// Import Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Doctor\PatientManagementController; // Ensure this controller exists if used

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function (Request $request) {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->role && $user->role->name === 'doctor') {
            // Data for Doctor's Dashboard
            $patientsForModal = User::whereHas('role', fn($q) => $q->where('name', 'patient'))
                                      ->orderBy('name')
                                      ->get(['id', 'name']);
            $doctorsForModal = [];
            if (Auth::user()->role->name !== 'doctor') { // Example logic for an admin role
                $doctorsForModal = User::whereHas('role', fn($q) => $q->where('name', 'doctor'))
                                         ->orderBy('name')
                                         ->get(['id', 'name']);
            }

            // Fetch doctor's appointments with filtering
            $appointmentsQuery = AppointmentModel::where('doctor_id', Auth::id())->with('patient');
            if ($request->filled('filter_date')) {
                $appointmentsQuery->whereDate('appointment_datetime', Carbon::parse($request->input('filter_date')));
            } elseif ($request->filled('filter_period')) {
                $period = $request->input('filter_period');
                if ($period === 'today') { $appointmentsQuery->whereDate('appointment_datetime', Carbon::today()); }
                elseif ($period === 'this_week') { $appointmentsQuery->whereBetween('appointment_datetime', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]); }
                elseif ($period === 'this_month') { $appointmentsQuery->whereMonth('appointment_datetime', Carbon::now()->month)->whereYear('appointment_datetime', Carbon::now()->year); }
            } else {
                 $appointmentsQuery->where('appointment_datetime', '>=', Carbon::today()->startOfDay());
            }
            $appointments = $appointmentsQuery->orderBy('appointment_datetime', 'asc')->paginate(15);

            return view('layouts.doctor_dashboard', compact(
                'patientsForModal',
                'doctorsForModal',
                'appointments'
                // 'consultations' // Removed
            ));

        } elseif ($user->role && $user->role->name === 'patient') {
            // Data for Patient's Dashboard
            $doctors = User::whereHas('role', fn($q) => $q->where('name', 'doctor'))
                             ->orderBy('name')->get(['id', 'name']); // For "Create Appointment" modal
            $now = Carbon::now();
            $upcomingAppointments = AppointmentModel::where('patient_id', $user->id)
                                        ->where('appointment_datetime', '>=', $now)
                                        ->where('status', '!=', 'cancelled')
                                        ->with('doctor')
                                        ->orderBy('appointment_datetime', 'asc')
                                        ->get();
            $pastAppointments = AppointmentModel::where('patient_id', $user->id)
                                    ->where('appointment_datetime', '<', $now)
                                    ->with('doctor')
                                    ->orderBy('appointment_datetime', 'desc')
                                    ->paginate(10, ['*'], 'past_appointments_page');

            // Removed patientConsultations fetching
            // $patientConsultations = ...

            return view('layouts.patient_dashboard', compact( // Ensure your patient layout path is correct
                'doctors',
                'upcomingAppointments',
                'pastAppointments'
                // 'patientConsultations' // Removed
            ));
        }
    }
    return view('dashboard'); // Default Breeze dashboard
})->middleware(['auth', 'verified'])->name('dashboard');


// --- Appointment Related Routes ---
Route::post('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])
    ->middleware(['auth'])
    ->name('appointments.available_slots');

Route::post('/patient/appointments', [AppointmentController::class, 'store'])
    ->middleware(['auth', 'role:patient'])
    ->name('patient.appointments.store');

// Patient hard DELETES their appointment
Route::delete('/patient/appointments/{appointment}/delete', [AppointmentController::class, 'destroy'])
    ->middleware(['auth', 'role:patient'])
    ->name('patient.appointments.destroy');

// Routes for DOCTOR's specific actions
Route::middleware(['auth', 'verified', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'markAsCompleted'])->name('appointments.complete');
    Route::delete('/appointments/{appointment}/delete', [AppointmentController::class, 'destroy'])->name('appointments.destroy'); // For hard delete by doctor

    // Ensure PatientManagementController exists if this route is active
    if (class_exists(PatientManagementController::class)) {
        Route::post('/patients/store-from-modal', [PatientManagementController::class, 'storeFromModal'])->name('patients.store_from_modal');
    }

    // REMOVED: Consultation Route for Doctor
    // if (class_exists(App\Http\Controllers\Doctor\ConsultationController::class)) { // No longer needed
    //    Route::post('/consultations', [App\Http\Controllers\Doctor\ConsultationController::class, 'store'])->name('consultations.store');
    // }
});


// --- Profile Routes ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
