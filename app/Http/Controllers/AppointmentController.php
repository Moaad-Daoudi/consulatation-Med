<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class AppointmentController extends Controller
{
    protected $workingHours = [
        'start_morning' => '09:00', 'end_morning' => '12:30',
        'start_afternoon' => '14:00', 'end_afternoon' => '16:00',
    ];
    protected $appointmentSlotIntervalMinutes = 15;
    protected $nonWorkingDays = [Carbon::SATURDAY, Carbon::SUNDAY];

    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
        ]);
        if ($validator->fails()) return response()->json(['message' => 'Données invalides.', 'errors' => $validator->errors()], 422);

        $doctorId = $request->input('doctor_id');
        $date = Carbon::parse($request->input('date'))->startOfDay();
        if (in_array($date->dayOfWeek, $this->nonWorkingDays)) return response()->json(['slots' => [], 'message' => 'Le docteur ne travaille pas ce jour-là.']);

        $availableSlots = [];
        $existingAppointmentStartTimes = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_datetime', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('appointment_datetime')
            ->map(fn($datetime) => $datetime->format('H:i'))
            ->all();

        $currentSlotTime = $date->copy()->setTimeFromTimeString($this->workingHours['start_morning']);
        $morningEndTime = $date->copy()->setTimeFromTimeString($this->workingHours['end_morning']);
        while ($currentSlotTime->lt($morningEndTime)) {
            $slotCandidate = $currentSlotTime->format('H:i');
            if ($currentSlotTime->copy()->addMinutes($this->appointmentSlotIntervalMinutes)->gt($morningEndTime)) break;
            if (!in_array($slotCandidate, $existingAppointmentStartTimes) && !($date->isToday() && $currentSlotTime->isBefore(Carbon::now()->addMinutes(5)))) {
                $availableSlots[] = $slotCandidate;
            }
            $currentSlotTime->addMinutes($this->appointmentSlotIntervalMinutes);
        }
        $currentSlotTime = $date->copy()->setTimeFromTimeString($this->workingHours['start_afternoon']);
        $afternoonEndTime = $date->copy()->setTimeFromTimeString($this->workingHours['end_afternoon']);
        while ($currentSlotTime->lt($afternoonEndTime)) {
            $slotCandidate = $currentSlotTime->format('H:i');
            if ($currentSlotTime->copy()->addMinutes($this->appointmentSlotIntervalMinutes)->gt($afternoonEndTime)) break;
            if (!in_array($slotCandidate, $existingAppointmentStartTimes) && !($date->isToday() && $currentSlotTime->isBefore(Carbon::now()->addMinutes(5)))) {
                $availableSlots[] = $slotCandidate;
            }
            $currentSlotTime->addMinutes($this->appointmentSlotIntervalMinutes);
        }
        $lastPossibleStartTime = $date->copy()->setTimeFromTimeString($this->workingHours['end_afternoon'])->subMinutes($this->appointmentSlotIntervalMinutes)->format('H:i');
        $availableSlots = array_filter($availableSlots, function($slot) use ($lastPossibleStartTime) { return $slot <= $lastPossibleStartTime; });

        if (empty($availableSlots)) return response()->json(['slots' => [], 'message' => 'Aucun créneau disponible.']);
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
            'notes' => 'nullable|string|max:1000'
        ];

        if ($isDoctorMakingAppointment) {
            $rules['patient_id'] = 'required|exists:users,id';
        }

        $validator = Validator::make($request->all(), $rules);

        // [Rest of your validation code remains the same until the existing appointment check]

        $date = Carbon::parse($request->input('appointment_date'));
        $time = $request->input('appointment_time');
        $appointmentDatetime = $date->copy()->setTimeFromTimeString($time);
        $doctorId = $request->input('doctor_id');
        $patientIdToStore = $isDoctorMakingAppointment ? $request->input('patient_id') : $loggedInUser->id;

        // Check if patient already has an appointment with this doctor on this day
        $existingWithSameDoctor = Appointment::where('patient_id', $patientIdToStore)
            ->where('doctor_id', $doctorId)
            ->whereDate('appointment_datetime', $date)
            ->where('status', '!=', 'cancelled')
            ->exists();

        if ($existingWithSameDoctor) {
            $errorMsg = 'Vous avez déjà un rendez-vous avec ce docteur dans ce jour.';
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'message' => $errorMsg,
                    'errors' => ['appointment_date' => [$errorMsg]]
                ], 409);
            }
            return redirect()->back()
                ->with('error', $errorMsg)
                ->withInput()
                ->with('open_modal_on_load', 'default_modal'); // Replace 'default_modal' with the appropriate modal name if needed
        }

        // [Rest of your existing code for checking time slots, etc.]

        // Then proceed with creating the appointment
        $appointmentData = [
            'patient_id' => $patientIdToStore,
            'doctor_id' => $doctorId,
            'appointment_datetime' => $appointmentDatetime,
            'status' => 'scheduled',
            'notes' => $request->input('notes')
        ];

        try {
            $appointment = Appointment::create($appointmentData);
        }
        catch (\Exception $e) {
            // [Your existing error handling]
        }

        return redirect()->route('dashboard', $request->only(['filter_date', 'filter_period']))
            ->with('success', 'Rendez-vous pris!');
    }
    public function markAsCompleted(Request $request, Appointment $appointment)
    {
        if ($appointment->doctor_id !== Auth::id()) return redirect()->route('dashboard', $request->query())->with('error', 'Non autorisé.');
        if (in_array($appointment->status, ['scheduled', 'pending', 'confirmed'])) { $appointment->status = 'completed'; $appointment->save(); return redirect()->route('dashboard', $request->query())->with('success', 'RDV terminé.'); }
        return redirect()->route('dashboard', $request->query())->with('info', 'Statut RDV: ' . $appointment->status);
    }

    public function destroy(Request $request, Appointment $appointment)
    {
        $user = Auth::user();
        $isDoctor = $user->role && $user->role->name === 'doctor';
        $isPatient = $user->role && $user->role->name === 'patient';
        $canDelete = false;
        $errorMsg = 'Action non autorisée/Conditions non remplies.';
        $apptDateTime = Carbon::parse($appointment->appointment_datetime);

        if ($isDoctor && $appointment->doctor_id === $user->id) {
            if (in_array($appointment->status, ['scheduled', 'pending', 'confirmed', 'cancelled'])) {
                $canDelete = true;
            } else {
                $errorMsg = 'Statut ne permet pas suppression par docteur.';
            }
        } elseif ($isPatient && $appointment->patient_id === $user->id) {
            // Modified condition: Only check if appointment is in the future
            if (in_array($appointment->status, ['scheduled', 'pending', 'confirmed']) && $apptDateTime->isFuture()) {
                $canDelete = true;
            } else {
                if (!in_array($appointment->status, ['scheduled', 'pending', 'confirmed'])) {
                    $errorMsg = 'Statut ne permet pas suppression.';
                } else if (!$apptDateTime->isFuture()) {
                    $errorMsg = 'Vous ne pouvez pas supprimer un rendez-vous passé.';
                }
            }
        }

        if (!$canDelete) {
            return redirect()->route('dashboard', $request->query())->with('error', $errorMsg);
        }

        try {
            $appointment->delete();
            return redirect()->route('dashboard', $request->query())->with('success', 'RDV supprimé.');
        } catch (\Exception $e) {
            Log::error("Error deleting appt {$appointment->id}: ".$e->getMessage());
            return redirect()->route('dashboard',$request->query())->with('error','Erreur suppression RDV.');
        }
    }
}
