<?php

use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\AppointmentController as DoctorAppointmentController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\ConsultationController;
use App\Http\Controllers\Doctor\PrescriptionController;
use App\Http\Controllers\Doctor\ProfileController;

use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\Patient\AppointmentController as PatientAppointmentController;
use App\Http\Controllers\Patient\PrescriptionController as PatientPrescriptionController;
use App\Http\Controllers\Patient\DossierController;


use App\Http\Controllers\AppointmentController;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots'])
         ->name('appointments.available_slots');

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/manage_users', [UserController::class, 'index'])->name('manage_users');
    Route::post('/manage_users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::resource('appointments', AdminAppointmentController::class)->except(['create', 'edit', 'show']);
});

Route::middleware(['auth', 'doctor'])->prefix('doctor')->name('doctor.')->group(function () {

    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');

    Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('appointments');
    Route::post('/appointments', [DoctorAppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/complete', [DoctorAppointmentController::class, 'markAsCompleted'])->name('appointments.complete');
    Route::delete('/appointments/{appointment}', [DoctorAppointmentController::class, 'destroy'])->name('appointments.destroy');

    Route::get('/patients', [PatientController::class, 'index'])->name('patients');
    Route::get('/patients/{patient}/dossier', [PatientController::class, 'showDossier'])->name('patients.dossier');

    Route::resource('consultations', ConsultationController::class)->except([
        'create', 'show', 'edit'
    ])->names([
        'index' => 'consultations.index',
        'store' => 'consultations.store',
        'update' => 'consultations.update',
        'destroy' => 'consultations.destroy',
    ]);

    Route::resource('prescriptions', PrescriptionController::class)->except(['create']);
    Route::get('/patients/{patient}/consultations-for-linking', [PrescriptionController::class, 'getPatientConsultationsForLinking'])->name('prescriptions.consultations_for_patient');
    
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::middleware(['auth', 'patient'])->prefix('patient')->name('patient.')->group(function () {

    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');

    Route::resource('appointments', PatientAppointmentController::class)->only(['index','store','destroy']);

    Route::get('/dossier-medical', [DossierController::class, 'index'])->name('dossier_medical');

    Route::get('/prescriptions', [PatientPrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/{prescription}', [PatientPrescriptionController::class, 'show'])->name('prescriptions.show');
});

require __DIR__ . '/auth.php';
