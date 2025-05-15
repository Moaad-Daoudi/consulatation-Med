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
use Illuminate\Support\Facades\Log;

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
                                        ->orderBy('consultation_date', 'desc') // Show most recent first
                                        ->get(); // Get all, or paginate if the list can be very long

            // Patient's own prescriptions
            $patientPrescriptions = PrescriptionModel::where('patient_id', Auth::id())
                                        ->with(['doctor', 'items', 'consultation'])
                                        ->withcount('items')
                                        ->orderBy('prescription_date', 'desc') // Show most recent first
                                        ->get(); // Get all, or paginate

            $activePrescriptions = collect();
            $pastPrescriptions = collect();
            $now = Carbon::now();

            // Define $allPatientPrescriptions before using it
            $allPatientPrescriptions = PrescriptionModel::where('patient_id', Auth::id())
                                        ->with(['doctor', 'items', 'consultation'])
                                        ->orderBy('prescription_date', 'desc')
                                        ->get();

            $activePrescriptions = collect();
            $pastPrescriptions = collect();
            $now = Carbon::now()->startOfDay(); // Compare against the start of today for consistency

            Log::info("--- Processing prescriptions for patient " . Auth::id() . " at " . $now->toDateTimeString() . " ---");

            foreach ($allPatientPrescriptions as $prescription) {
                $isOverallPrescriptionActive = false;
                $prescriptionDate = Carbon::parse($prescription->prescription_date)->startOfDay();
                Log::info("Prescription ID: {$prescription->id}, Date: " . $prescriptionDate->toDateString());

                if ($prescription->items->isNotEmpty()) {
                    $latestItemEndDate = null; // Track the latest end date of any item in this prescription

                    foreach ($prescription->items as $item) {
                        Log::info("  Item: {$item->medication_name}, Duration String: '{$item->duration}'");
                        $currentItemEndDate = null;

                        if (isset($item->duration) && !empty(trim($item->duration))) {
                            $durationStr = strtolower(trim($item->duration));

                            if (preg_match('/^(\d+)$/', $durationStr, $matches)) { // Just a number, assume days
                                $currentItemEndDate = $prescriptionDate->copy()->addDays((int)$matches[1] - 1); // -1 because day 1 is the prescription date
                                Log::info("    Parsed as {$matches[1]} days. End date: " . ($currentItemEndDate ? $currentItemEndDate->toDateString() : 'N/A'));
                            } elseif (preg_match('/(\d+)\s*j(?:our|ours)?/', $durationStr, $matches)) {
                                $currentItemEndDate = $prescriptionDate->copy()->addDays((int)$matches[1] - 1);
                                Log::info("    Parsed as {$matches[1]} jours. End date: " . ($currentItemEndDate ? $currentItemEndDate->toDateString() : 'N/A'));
                            } elseif (preg_match('/(\d+)\s*s(?:emaine|emaines)?/', $durationStr, $matches)) {
                                $currentItemEndDate = $prescriptionDate->copy()->addWeeks((int)$matches[1])->subDay(); // End of the last day of the last week
                                Log::info("    Parsed as {$matches[1]} semaines. End date: " . ($currentItemEndDate ? $currentItemEndDate->toDateString() : 'N/A'));
                            } elseif (preg_match('/(\d+)\s*m(?:ois)?/', $durationStr, $matches)) {
                                $currentItemEndDate = $prescriptionDate->copy()->addMonths((int)$matches[1])->subDay(); // End of the last day of the last month
                                Log::info("    Parsed as {$matches[1]} mois. End date: " . ($currentItemEndDate ? $currentItemEndDate->toDateString() : 'N/A'));
                            } else {
                                Log::warning("    Could not parse duration string: '{$durationStr}'");
                            }

                            if ($currentItemEndDate) {
                                if (is_null($latestItemEndDate) || $currentItemEndDate->greaterThan($latestItemEndDate)) {
                                    $latestItemEndDate = $currentItemEndDate;
                                }
                            }
                        } else {
                            // Item has no duration - how to handle?
                            // Option A: Consider it ongoing/active indefinitely (or for a very long default period)
                            // $latestItemEndDate = Carbon::now()->addYears(5); // Effectively makes it active
                            // Log::info("    Item has no duration, considered active by default.");
                            // break; // If one item is considered active by default, the whole prescription could be.

                            // Option B: Ignore items without duration for active/past calculation, or rely on a default prescription active period.
                            Log::info("    Item has no duration string, skipping for end date calculation of this item.");
                        }
                    } // End foreach item

                    if ($latestItemEndDate && $now->lessThanOrEqualTo($latestItemEndDate->endOfDay())) { // Compare with end of day of end date
                        $isOverallPrescriptionActive = true;
                    } elseif (is_null($latestItemEndDate) && $prescription->items->isNotEmpty()) {
                        // All items had no parsable duration. Apply a default active period for such prescriptions.
                        // Example: Active for 30 days from prescription date if no item specifies a duration.
                        if ($prescriptionDate->copy()->addDays(30)->isFuture()) {
                            $isOverallPrescriptionActive = true;
                            Log::info("  All items lacked parsable duration. Active by default 30-day rule.");
                        }
                    }


                } else { // Prescription has no items
                    // Consider it active for a short default period (e.g., 7 days from prescription_date)
                    if ($prescriptionDate->copy()->addDays(7)->isFuture()) {
                        $isOverallPrescriptionActive = true;
                        Log::info("  No items. Active by default 7-day rule.");
                    }
                }

                if ($isOverallPrescriptionActive) {
                    $activePrescriptions->push($prescription);
                    Log::info("  >> Classified as ACTIVE");
                } else {
                    $pastPrescriptions->push($prescription);
                    Log::info("  >> Classified as PAST");
                }
            }
            Log::info("--- Finished processing prescriptions. Active: " . $activePrescriptions->count() . ", Past: " . $pastPrescriptions->count() . " ---");

            // Sort them again (collections are mutable, push might not preserve order of original query)
            $activePrescriptions = $activePrescriptions->sortByDesc('prescription_date');
            $pastPrescriptions = $pastPrescriptions->sortByDesc('prescription_date');


            return view('layouts.patient_dashboard', compact(
                'doctors',
                'upcomingAppointments',
                'pastAppointments',
                'patientConsultations',
                'activePrescriptions',
                'pastPrescriptions'
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
