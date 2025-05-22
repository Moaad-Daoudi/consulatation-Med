<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Appointment as AppointmentModel;
use App\Models\Consultation as ConsultationModel;
use App\Models\Prescription as PrescriptionModel;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Doctor\PatientManagementController;
use App\Http\Controllers\Doctor\ConsultationController;
use App\Http\Controllers\Doctor\PrescriptionController;


Route::get('/', function () {
    $doctors_data = User::whereHas('role', function ($query) {
                            $query->where('name', 'doctor');
                        })
                        ->whereHas('doctor')
                        ->with('doctor')
                        ->get();

    return view('welcome', ['doctors_list' => $doctors_data]);
});

Route::get('/dashboard', function (Request $request) {
    if (Auth::check()) {
        $user = Auth::user();

        if ($user->role && $user->role->name === 'doctor') {
            $patientsForModal = User::whereHas('role', fn($q) => $q->where('name', 'patient'))
                                      ->orderBy('name')
                                      ->get(['id', 'name', 'email']);

            $doctorsForModal = [];

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

            $patientIdsFromConsultations = ConsultationModel::where('doctor_id', Auth::id())->distinct()->pluck('patient_id');
            $patientIdsFromPrescriptions = PrescriptionModel::where('doctor_id', Auth::id())->distinct()->pluck('patient_id');
            $distinctPatientIds = $patientIdsFromConsultations->merge($patientIdsFromPrescriptions)->unique();

            $doctorPatients = User::whereIn('id', $distinctPatientIds)
                                ->whereHas('role', fn($q) => $q->where('name', 'patient'))
                                ->withCount(['patientConsultations as consultations_with_doctor' => function ($query) {
                                    $query->where('doctor_id', Auth::id());
                                }, 'receivedPrescriptions as prescriptions_from_doctor' => function ($query) {
                                    $query->where('doctor_id', Auth::id());
                                }])
                                ->orderBy('name')
                                ->paginate(10, ['*'], 'doctor_patients_page');

            $prescriptionsForDashboard = PrescriptionModel::where('doctor_id', Auth::id())
                                ->with(['patient', 'items', 'consultation'])
                                ->withCount('items')
                                ->latest('prescription_date')
                                ->paginate(10, ['*'], 'prescriptions_page');

            // --- DOCTOR DASHBOARD ---
            $doctorId = Auth::id();
            $appointmentsTodayCount = AppointmentModel::where('doctor_id', $doctorId)
                                        ->whereDate('appointment_datetime', Carbon::today())
                                        ->count();
            $totalUniquePatientsCount = ConsultationModel::where('doctor_id', $doctorId)
                                           ->distinct('patient_id')
                                           ->count('patient_id');
            $prescriptionsThisMonthCount = PrescriptionModel::where('doctor_id', $doctorId)
                                             ->whereMonth('prescription_date', Carbon::now()->month)
                                             ->whereYear('prescription_date', Carbon::now()->year)
                                             ->count();

            // --- DOCTOR RECENT ACTIVITIES ---
            $recentConsultations = ConsultationModel::where('doctor_id', $doctorId)
                                    ->with('patient')
                                    ->latest('consultation_date')
                                    ->limit(3)->get();
            $recentAppointments = AppointmentModel::where('doctor_id', $doctorId)
                                    ->with('patient')
                                    ->orderBy('appointment_datetime', 'desc')
                                    ->limit(3)->get();
            $recentPrescriptions = PrescriptionModel::where('doctor_id', $doctorId)
                                    ->with('patient')
                                    ->latest('prescription_date')
                                    ->limit(2)->get();

            $activities = collect();
            foreach ($recentConsultations as $consult) {
                $activities->push([
                    'type' => 'Consultation', 'activity_date' => $consult->consultation_date,
                    'patient_name' => $consult->patient->name ?? 'N/A',
                    'description' => 'Nouvelle consultation: ' . Str::limit($consult->reason_for_visit, 50),
                    'status' => 'Terminé', 'object' => $consult
                ]);
            }
            foreach ($recentAppointments as $appt) {
                if (!$recentConsultations->contains('appointment_id', $appt->id)) {
                    $activities->push([
                        'type' => 'Rendez-vous', 'activity_date' => $appt->appointment_datetime,
                        'patient_name' => $appt->patient->name ?? 'N/A',
                        'description' => 'RDV', 'status' => ucfirst($appt->status), 'object' => $appt
                    ]);
                }
            }
            foreach ($recentPrescriptions as $presc) {
                 $activities->push([
                    'type' => 'Ordonnance', 'activity_date' => $presc->prescription_date,
                    'patient_name' => $presc->patient->name ?? 'N/A',
                    'description' => 'Ordonnance créée/mise à jour', 'status' => 'Délivrée', 'object' => $presc
                ]);
            }
            $recentActivities = $activities->sortByDesc('activity_date')->take(5);

            return view('layouts.doctor_dashboard', compact(
                'patientsForModal', 'doctorsForModal', 'appointments', 'consultations',
                'prescriptionsForDashboard', 'doctorPatients', 'appointmentsTodayCount',
                'totalUniquePatientsCount', 'prescriptionsThisMonthCount',
                'recentActivities'
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

            $patientConsultations = ConsultationModel::where('patient_id', Auth::id())
                                        ->with(['doctor'])
                                        ->latest('consultation_date')
                                        ->orderBy('consultation_date', 'desc')
                                        ->get();

            $allPatientPrescriptions = PrescriptionModel::where('patient_id', Auth::id())
                                        ->with(['doctor', 'items', 'consultation'])
                                        ->orderBy('prescription_date', 'desc')
                                        ->get();

            $activePrescriptions = collect();
            $pastPrescriptions = collect();
            $currentDateTime = Carbon::now()->startOfDay();

            Log::info("--- PATIENT DASHBOARD: Processing prescriptions for patient " . Auth::id() . " at " . $currentDateTime->toDateTimeString() . " ---");
            foreach ($allPatientPrescriptions as $prescription) {
                $isOverallPrescriptionActive = false;
                $prescriptionDate = Carbon::parse($prescription->prescription_date)->startOfDay();
                Log::info("Prescription ID: {$prescription->id}, Date: " . $prescriptionDate->toDateString());

                if ($prescription->items->isNotEmpty()) {
                    $latestItemEndDate = null;
                    foreach ($prescription->items as $item) {
                        Log::info("  Item: {$item->medication_name}, Duration String: '{$item->duration}'");
                        $currentItemEndDate = null;
                        if (isset($item->duration) && !empty(trim($item->duration))) {
                            $durationStr = strtolower(trim($item->duration));
                            if (preg_match('/^(\d+)$/', $durationStr, $matches)) { // Just a number, assume days
                                $currentItemEndDate = $prescriptionDate->copy()->addDays((int)$matches[1] -1); // -1 as day 1 is prescription date
                            } elseif (preg_match('/(\d+)\s*j(?:our|ours)?/', $durationStr, $matches)) {
                                $currentItemEndDate = $prescriptionDate->copy()->addDays((int)$matches[1] -1);
                            } elseif (preg_match('/(\d+)\s*s(?:emaine|emaines)?/', $durationStr, $matches)) {
                                $currentItemEndDate = $prescriptionDate->copy()->addWeeks((int)$matches[1])->subDay(); // End of last day of last week
                            } elseif (preg_match('/(\d+)\s*m(?:ois)?/', $durationStr, $matches)) {
                                $currentItemEndDate = $prescriptionDate->copy()->addMonths((int)$matches[1])->subDay(); // End of last day of last month
                            } else {
                                Log::warning("    Could not parse duration string: '{$durationStr}' for item ID {$item->id}");
                            }

                            if ($currentItemEndDate) {
                                Log::info("    Parsed. End date for item ID {$item->id}: " . $currentItemEndDate->toDateString());
                                if (is_null($latestItemEndDate) || $currentItemEndDate->greaterThan($latestItemEndDate)) {
                                    $latestItemEndDate = $currentItemEndDate;
                                }
                            }
                        } else {
                            Log::info("    Item ID {$item->id} has no duration string, skipping for end date calculation of this item.");
                        }
                    }

                    if ($latestItemEndDate && $currentDateTime->lessThanOrEqualTo($latestItemEndDate->endOfDay())) {
                        $isOverallPrescriptionActive = true;
                        Log::info("  Prescription ID {$prescription->id} active based on latest item end date: " . $latestItemEndDate->toDateString());
                    } elseif (is_null($latestItemEndDate) && $prescription->items->isNotEmpty()) {
                        // All items had no parsable duration. Apply a default active period.
                        if ($prescriptionDate->copy()->addDays(30)->isAfter($currentDateTime)) {
                            $isOverallPrescriptionActive = true;
                            Log::info("  Prescription ID {$prescription->id} (all items lacked parsable duration) active by default 30-day rule.");
                        }
                    }
                } else {
                    if ($prescriptionDate->copy()->addDays(7)->isAfter($currentDateTime)) {
                        $isOverallPrescriptionActive = true;
                        Log::info("  Prescription ID {$prescription->id} (no items) active by default 7-day rule.");
                    }
                }

                if ($isOverallPrescriptionActive) {
                    $activePrescriptions->push($prescription);
                    Log::info("  >> Prescription ID {$prescription->id} Classified as ACTIVE");
                } else {
                    $pastPrescriptions->push($prescription);
                    Log::info("  >> Prescription ID {$prescription->id} Classified as PAST");
                }
            }
            Log::info("--- PATIENT DASHBOARD: Finished processing prescriptions. Active: " . $activePrescriptions->count() . ", Past: " . $pastPrescriptions->count() . " ---");

            $activePrescriptions = $activePrescriptions->sortByDesc('prescription_date');
            $pastPrescriptions = $pastPrescriptions->sortByDesc('prescription_date');

            $upcomingAppointmentCount = $upcomingAppointments->count();
            $nextAppointment = $upcomingAppointments->first();
            $activePrescriptionsCount = $activePrescriptions->count();

            $medicationReminders = collect();
            foreach ($activePrescriptions as $prescription) {
                foreach ($prescription->items as $item) {
                    $medicationReminders->push([
                        'name' => $item->medication_name,
                        'dosage' => $item->dosage,
                        'frequency' => $item->frequency,
                        'duration' => $item->duration,
                        'notes' => $item->notes,
                        'prescription_date' => $prescription->prescription_date
                    ]);
                }
            }
            $medicationReminders = $medicationReminders->sortBy('prescription_date')->sortBy('name');


            return view('layouts.patient_dashboard', compact(
                'doctors',
                'upcomingAppointments',
                'pastAppointments',
                'patientConsultations',
                'activePrescriptions',
                'pastPrescriptions',
                'upcomingAppointmentCount',
                'nextAppointment',
                'activePrescriptionsCount',
                'medicationReminders'
            ));
        }
    }
    return view('dashboard'); 
})->middleware(['auth', 'verified'])->name('dashboard');


// --- Appointment Related Routes ---
Route::post('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])
    ->middleware(['auth'])
    ->name('appointments.available_slots');

// --- Patient Specific Appointment Routes ---
Route::middleware(['auth', 'verified', 'role:patient'])->prefix('patient')->name('patient.')->group(function() {
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::delete('/appointments/{appointment}/delete', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
});


// --- Doctor Specific Routes ---
Route::middleware(['auth', 'verified', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    // Doctor Appointment Management
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/complete', [AppointmentController::class, 'markAsCompleted'])->name('appointments.complete');
    Route::delete('/appointments/{appointment}/delete', [AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Doctor Patient Management
    if (class_exists(PatientManagementController::class)) {
        Route::post('/patients/store-from-modal', [PatientManagementController::class, 'storeFromModal'])->name('patients.store_from_modal');
        Route::get('/patients/{patient}/dossier', [PatientManagementController::class, 'showDossier'])->name('patients.show_dossier');
    }

    // Doctor Consultation Management
    Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');
    Route::get('/consultations/{consultation}/edit', [ConsultationController::class, 'edit'])->name('consultations.edit');
    Route::put('/consultations/{consultation}', [ConsultationController::class, 'update'])->name('consultations.update');
    Route::delete('/consultations/{consultation}', [ConsultationController::class, 'destroy'])->name('consultations.destroy');

    // Doctor Prescription (Ordonnance) Management
    Route::resource('prescriptions', PrescriptionController::class)->except(['create']);
    Route::get('/patients/{patient}/consultations-for-linking', [PrescriptionController::class, 'getPatientConsultationsForLinking'])
         ->name('patients.consultations_for_linking');

});


// --- Profile Routes ---
Route::middleware('auth')->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

require __DIR__.'/auth.php';
