<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    protected $workingHours = [
        'start_morning' => '09:00', 'end_morning' => '12:30',
        'start_afternoon' => '14:00', 'end_afternoon' => '16:00',
    ];
    protected $appointmentDurationMinutes = 15;
    protected $nonWorkingDays = [Carbon::SATURDAY, Carbon::SUNDAY];

    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Données invalides.', 'errors' => $validator->errors()], 422);
        }

        $doctorId = $request->input('doctor_id');
        $date = Carbon::parse($request->input('date'))->startOfDay();

        if (in_array($date->dayOfWeek, $this->nonWorkingDays)) {
            return response()->json(['slots' => [], 'message' => 'Le docteur ne travaille pas ce jour-là.']);
        }

        $availableSlots = [];
        $existingAppointments = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_datetime', $date)
            ->pluck('appointment_datetime')
            ->map(fn($datetime) => $datetime->format('H:i'))
            ->all();

        // Morning session
        $currentSlotTime = $date->copy()->setTimeFromTimeString($this->workingHours['start_morning']);
        $morningEndTime = $date->copy()->setTimeFromTimeString($this->workingHours['end_morning']);
        while ($currentSlotTime->lt($morningEndTime)) {
            $slotCandidate = $currentSlotTime->format('H:i');
            if ($currentSlotTime->copy()->addMinutes($this->appointmentDurationMinutes)->gt($morningEndTime)) break;
            if (!in_array($slotCandidate, $existingAppointments) && !($date->isToday() && $currentSlotTime->isBefore(Carbon::now()->addMinutes(5)))) {
                $availableSlots[] = $slotCandidate;
            }
            $currentSlotTime->addMinutes($this->appointmentDurationMinutes);
        }

        // Afternoon session
        $currentSlotTime = $date->copy()->setTimeFromTimeString($this->workingHours['start_afternoon']);
        $afternoonEndTime = $date->copy()->setTimeFromTimeString($this->workingHours['end_afternoon']);
        while ($currentSlotTime->lt($afternoonEndTime)) {
            $slotCandidate = $currentSlotTime->format('H:i');
            if ($currentSlotTime->copy()->addMinutes($this->appointmentDurationMinutes)->gt($afternoonEndTime)) break;
            if (!in_array($slotCandidate, $existingAppointments) && !($date->isToday() && $currentSlotTime->isBefore(Carbon::now()->addMinutes(5)))) {
                $availableSlots[] = $slotCandidate;
            }
            $currentSlotTime->addMinutes($this->appointmentDurationMinutes);
        }

        $lastPossibleSlotTimeInDay = $date->copy()->setTimeFromTimeString($this->workingHours['end_afternoon'])->subMinutes($this->appointmentDurationMinutes)->format('H:i');
        $availableSlots = array_filter($availableSlots, function($slot) use ($lastPossibleSlotTimeInDay, $date){
            $slotDateTime = $date->copy()->setTimeFromTimeString($slot);
            $endAfternoonDateTime = $date->copy()->setTimeFromTimeString($this->workingHours['end_afternoon']);
            return $slotDateTime->lt($endAfternoonDateTime) && $slotDateTime->copy()->addMinutes($this->appointmentDurationMinutes)->lte($endAfternoonDateTime);
        });

        if (empty($availableSlots)) {
            return response()->json(['slots' => [], 'message' => 'Aucun créneau disponible pour cette date.']);
        }
        return response()->json(['slots' => array_values($availableSlots)]);
    }

    public function store(Request $request)
    {
        $loggedInUser = Auth::user();
        $isDoctorMakingAppointment = $loggedInUser->role && $loggedInUser->role->name === 'doctor';

        $rules = [
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|regex:/^\d{2}:\d{2}$/',
            'notes' => 'nullable|string|max:1000',
        ];
        if ($isDoctorMakingAppointment) {
            $rules['patient_id'] = 'required|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules, [
            'patient_id.required' => 'Veuillez sélectionner un patient.',
            'patient_id.exists' => 'Le patient sélectionné est invalide.',
            'doctor_id.required' => 'Veuillez sélectionner un docteur.',
            'appointment_date.required' => 'La date du rendez-vous est requise.',
            'appointment_date.date' => 'Le format de la date est invalide.',
            'appointment_date.after_or_equal' => 'La date du rendez-vous ne peut pas être dans le passé.',
            'appointment_time.required' => 'L\'heure du rendez-vous est requise.',
            'appointment_time.regex' => 'Le format de l\'heure est invalide (HH:MM).',
        ]);

        $errorBag = $isDoctorMakingAppointment ? 'default' : 'patientAppointmentCreate';
        $modalToReopen = $isDoctorMakingAppointment ? 'doctor-create-appointment-modal' : 'patient-create-appointment-modal';

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) return response()->json(['message' => 'Erreurs de validation.', 'errors' => $validator->errors()], 422);
            return redirect()->back()->withErrors($validator, $errorBag)->withInput()->with('open_modal_on_load', $modalToReopen);
        }

        $date = Carbon::parse($request->input('appointment_date'));
        $time = $request->input('appointment_time');
        $appointmentDatetime = $date->copy()->setTimeFromTimeString($time);
        $doctorId = $request->input('doctor_id');

        if (in_array($date->dayOfWeek, $this->nonWorkingDays)) {
            $errorMsg = 'Le docteur sélectionné ne travaille pas ce jour-là.';
            if ($request->ajax() || $request->wantsJson()) return response()->json(['message' => $errorMsg, 'errors' => ['appointment_date' => [$errorMsg]]], 400);
            return redirect()->back()->with('error', $errorMsg)->withInput()->with('open_modal_on_load', $modalToReopen);
        }

        $appointmentEndDatetime = $appointmentDatetime->copy()->addMinutes($this->appointmentDurationMinutes);
        $isMorningSlot = ($appointmentDatetime->format('H:i') >= $this->workingHours['start_morning'] && $appointmentEndDatetime->format('H:i') <= $this->workingHours['end_morning']);
        $isAfternoonSlot = ($appointmentDatetime->format('H:i') >= $this->workingHours['start_afternoon'] && $appointmentEndDatetime->format('H:i') <= $this->workingHours['end_afternoon']);

        if (!$isMorningSlot && !$isAfternoonSlot) {
            $errorMsg = 'Le créneau horaire est en dehors des heures de travail ou chevauche la pause du docteur.';
            if ($request->ajax() || $request->wantsJson()) return response()->json(['message' => $errorMsg, 'errors' => ['appointment_time' => [$errorMsg]]], 400);
            return redirect()->back()->with('error', $errorMsg)->withInput()->with('open_modal_on_load', $modalToReopen);
        }

        $existing = Appointment::where('doctor_id', $doctorId)->where('appointment_datetime', $appointmentDatetime)->exists();
        if ($existing) {
            $errorMsg = 'Ce créneau est déjà réservé pour ce docteur.';
            if ($request->ajax() || $request->wantsJson()) return response()->json(['message' => $errorMsg, 'errors' => ['appointment_time' => [$errorMsg]]], 409);
            return redirect()->back()->with('error', $errorMsg)->withInput()->with('open_modal_on_load', $modalToReopen);
        }

        $patientIdToStore = $isDoctorMakingAppointment ? $request->input('patient_id') : $loggedInUser->id;

        try {
            $appointment = Appointment::create([
                'patient_id' => $patientIdToStore,
                'doctor_id' => $doctorId,
                'appointment_datetime' => $appointmentDatetime,
                'notes' => $request->input('notes'),
                'status' => 'scheduled',
            ]);
        } catch (\Exception $e) {
            Log::error("Error creating appointment: " . $e->getMessage());
            $errorMsg = 'Une erreur est survenue lors de la création du rendez-vous.';
            if ($request->ajax() || $request->wantsJson()) return response()->json(['message' => $errorMsg], 500);
            return redirect()->back()->with('error', $errorMsg)->withInput()->with('open_modal_on_load', $modalToReopen);
        }

        $successMsg = 'Rendez-vous pris avec succès!';
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => $successMsg, 'appointment' => $appointment->load('patient', 'doctor')]);
        }
        $queryParams = $request->only(['filter_date', 'filter_period']);
        return redirect()->route('dashboard', $queryParams)->with('success', $successMsg);
    }

    public function markAsCompleted(Request $request, Appointment $appointment)
    {
        if ($appointment->doctor_id !== Auth::id()) {
            return redirect()->route('dashboard', $request->query())->with('error', 'Action non autorisée.');
        }
        if ($appointment->status === 'scheduled') {
            $appointment->status = 'completed';
            $appointment->save();
            return redirect()->route('dashboard', $request->query())->with('success', 'Rendez-vous marqué comme terminé.');
        } elseif ($appointment->status === 'completed') {
            return redirect()->route('dashboard', $request->query())->with('info', 'Ce rendez-vous est déjà marqué comme terminé.');
        } else {
             return redirect()->route('dashboard', $request->query())->with('error', 'Impossible de marquer ce RDV (statut: ' . $appointment->status . ').');
        }
    }

    /**
     * Remove the specified appointment from storage (Hard Delete by Doctor).
     * This method is linked to route('doctor.appointments.destroy')
     */
    public function destroy(Request $request, Appointment $appointment)
    {
        if ($appointment->doctor_id !== Auth::id()) { // Basic authorization
            return redirect()->route('dashboard', $request->query())
                ->with('error', 'Action non autorisée. Vous ne pouvez supprimer que vos propres rendez-vous.');
        }

        try {
            $patientName = $appointment->patient->name ?? 'un patient';
            $appointmentTime = Carbon::parse($appointment->appointment_datetime)->format('d/m/Y H:i');

            $appointment->delete(); // Performs hard delete

            return redirect()->route('dashboard', $request->query())
                ->with('success', "Rendez-vous avec {$patientName} le {$appointmentTime} a été supprimé définitivement.");
        } catch (\Exception $e) {
            Log::error("Error deleting appointment {$appointment->id}: " . $e->getMessage());
            return redirect()->route('dashboard', $request->query())
                ->with('error', 'Une erreur est survenue lors de la suppression du rendez-vous.');
        }
    }

    /**
     * Allow a patient to cancel their own scheduled appointment (changes status to 'cancelled').
     * This method is linked to route('patient.appointments.cancel')
     */
    public function patientCancelAppointment(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        if ($appointment->patient_id !== $user->id) {
            return redirect()->route('dashboard')->with('error', 'Action non autorisée.');
        }

        $cancellationCutoffHours = 2;
        if ($appointment->appointment_datetime->isPast() || $appointment->appointment_datetime->diffInHours(now()) < $cancellationCutoffHours) {
            return redirect()->route('dashboard')->with('error', 'Ce RDV ne peut plus être annulé (trop proche de l\'heure du RDV ou déjà passé).');
        }

        if ($appointment->status === 'scheduled') {
            $appointment->status = 'cancelled';
            $appointment->notes = ($appointment->notes ? $appointment->notes . "\n" : '') . "Annulé par le patient le " . now()->format('d/m/Y H:i');
            $appointment->save();
            return redirect()->route('dashboard')->with('success', 'Votre rendez-vous a été annulé.');
        } else { // e.g., if already completed or already cancelled
            return redirect()->route('dashboard')->with('info', 'Ce RDV ne peut être annulé (statut: ' . $appointment->status . ').');
        }
    }
}
