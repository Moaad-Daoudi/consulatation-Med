<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment as AppointmentModel;
use App\Models\Consultation as ConsultationModel;
use App\Models\Prescription as PrescriptionModel; // Added for dashboard data
// use App\Models\Doctor; // Only if you have a separate Doctor model for a separate table
// use App\Models\Patient; // Only if you have a separate Patient model for a separate table
use Illuminate\Support\Carbon;

// Import Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Doctor\PatientManagementController;
use App\Http\Controllers\Doctor\ConsultationController;
use App\Http\Controllers\Doctor\PrescriptionController; // Added

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function (Request $request) {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->role && $user->role->name === 'doctor') {
            $patientsForModal = User::whereHas('role', fn($q) => $q->where('name', 'patient'))
                                      ->orderBy('name')
                                      ->get(['id', 'name', 'email']); // For select dropdowns in various modals

            $doctorsForModal = []; // Only relevant if a non-doctor is creating appointments for doctors
            // This condition is unlikely to be true if already in doctor's dashboard logic
            // if (Auth::user()->role->name !== 'doctor') {
            //     $doctorsForModal = User::whereHas('role', fn($q) => $q->where('name', 'doctor'))
            //                              ->orderBy('name')
            //                              ->get(['id', 'name']);
            // }

            // Appointments
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
            $appointments = $appointmentsQuery->orderBy('appointment_datetime', 'asc')->paginate(15, ['*'], 'appointments_page');

            // Consultations
            $consultations = ConsultationModel::where('doctor_id', Auth::id())
                                ->with(['patient', 'appointment'])
                                ->latest('consultation_date')
                                ->paginate(10, ['*'], 'consultations_page');

            // Get patient IDs from consultations and prescriptions by the current doctor
            $patientIdsFromConsultations = ConsultationModel::where('doctor_id', Auth::id())
                                            ->distinct()
                                            ->pluck('patient_id');

            $patientIdsFromPrescriptions = PrescriptionModel::where('doctor_id', Auth::id())
                                            ->distinct()
                                            ->pluck('patient_id');

            // Merge and get unique patient IDs
            $distinctPatientIds = $patientIdsFromConsultations->merge($patientIdsFromPrescriptions)->unique();

            // Fetch these patients with their last interaction date (this is a bit more complex)
            // For simplicity now, just fetch the patients. We can add last interaction later.
            $doctorPatients = User::whereIn('id', $distinctPatientIds)
                                ->whereHas('role', fn($q) => $q->where('name', 'patient')) // Ensure they are patients
                                ->withCount(['patientConsultations as consultations_with_doctor' => function ($query) {
                                    $query->where('doctor_id', Auth::id());
                                }, 'receivedPrescriptions as prescriptions_from_doctor' => function ($query) {
                                    $query->where('doctor_id', Auth::id());
                                }])
                                ->orderBy('name')
                                ->paginate(10, ['*'], 'doctor_patients_page'); // Paginate this list

            // Prescriptions for History
            $prescriptionsForDashboard = PrescriptionModel::where('doctor_id', Auth::id())
                                ->with(['patient', 'items', 'consultation']) // Eager load relations
                                ->withCount('items')
                                ->latest('prescription_date')
                                ->paginate(10, ['*'], 'prescriptions_page');


            return view('layouts.doctor_dashboard', compact(
                'patientsForModal',
                'doctorsForModal', // Will be empty for a doctor user based on current logic
                'appointments',
                'consultations',
                'prescriptionsForDashboard',
                'doctorPatients'
            ));

        } elseif ($user->role && $user->role->name === 'patient') {
            $doctors = User::whereHas('role', fn($q) => $q->where('name', 'doctor'))
                             ->orderBy('name')->get(['id', 'name']);
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

            // Patient's own consultations
            $patientConsultations = ConsultationModel::where('patient_id', Auth::id())
                                        ->with(['doctor'])
                                        ->latest('consultation_date')
                                        ->paginate(10, ['*'], 'patient_consultations_page');

            // Patient's own prescriptions
            $patientPrescriptions = PrescriptionModel::where('patient_id', Auth::id())
                                        ->with(['doctor', 'items', 'consultation'])
                                        ->withCount('items')
                                        ->latest('prescription_date')
                                        ->paginate(10, ['*'], 'patient_prescriptions_page');


            return view('layouts.patient_dashboard', compact(
                'doctors',
                'upcomingAppointments',
                'pastAppointments',
                'patientConsultations',
                'patientPrescriptions'
            ));
        }
    }
    // Fallback dashboard view if no specific role dashboard is matched or user not authenticated properly for a role dashboard
    return view('dashboard'); // Generic dashboard, or redirect to login if Auth::check() fails earlier
})->middleware(['auth', 'verified'])->name('dashboard');


// --- Appointment Related Routes ---
Route::post('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])
    ->middleware(['auth']) // Any authenticated user can check slots, controller should authorize if needed
    ->name('appointments.available_slots');

// --- Patient Specific Appointment Routes ---
Route::middleware(['auth', 'verified', 'role:patient'])->prefix('patient')->name('patient.')->group(function() {
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::delete('/appointments/{appointment}/delete', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
});


// --- Doctor Specific Routes ---
Route::middleware(['auth', 'verified', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    // Doctor Appointment Management
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store'); // Name: doctor.appointments.store
    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'markAsCompleted'])->name('appointments.complete');
    Route::delete('/appointments/{appointment}/delete', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Doctor Patient Management (from modal)
    if (class_exists(PatientManagementController::class)) { // Keep if controller is optional
        Route::post('/patients/store-from-modal', [PatientManagementController::class, 'storeFromModal'])->name('patients.store_from_modal');
    }

    // New Route for Patient Dossier
    Route::get('/patients/{patient}/dossier', [PatientManagementController::class, 'showDossier'])->name('patients.show_dossier'); // Fully qualified name: doctor.patients.show_dossier

    // Doctor Consultation Management
    Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');
    Route::get('/consultations/{consultation}/edit', [ConsultationController::class, 'edit'])->name('consultations.edit');
    Route::put('/consultations/{consultation}', [ConsultationController::class, 'update'])->name('consultations.update');
    Route::delete('/consultations/{consultation}', [ConsultationController::class, 'destroy'])->name('consultations.destroy');
    // Note: A GET /consultations/{consultation} (show) route is not strictly needed if view modal is AJAX/JS populated
    // but can be useful.

    // Doctor Prescription (Ordonnance) Management
    Route::resource('prescriptions', PrescriptionController::class)->except(['create']);
    // - index: Data fetched by main dashboard route for SPA history list
    // - create: Form is part of the 'ordonnances' SPA section, not a separate page
    // - store: POST doctor/prescriptions
    // - show: GET doctor/prescriptions/{prescription} (for View Modal AJAX)
    // - edit: GET doctor/prescriptions/{prescription}/edit (for populating Edit Modal via session redirect)
    // - update: PUT/PATCH doctor/prescriptions/{prescription}
    // - destroy: DELETE doctor/prescriptions/{prescription}

    // Route for fetching consultations for a patient (used when linking prescriptions)
    Route::get('/patients/{patient}/consultations-for-linking', [PrescriptionController::class, 'getPatientConsultationsForLinking'])
         ->name('patients.consultations_for_linking'); // Name: doctor.patients.consultations_for_linking

});


// --- Profile Routes ---
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
