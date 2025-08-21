<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Appointment;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    // These properties are well-defined.
    protected $workingHours = [
        'start_morning' => '09:00', 'end_morning'   => '12:30',
        'start_afternoon' => '14:00', 'end_afternoon' => '16:00',
    ];
    protected $appointmentSlotIntervalMinutes = 15;
    protected $nonWorkingDays = [Carbon::SATURDAY, Carbon::SUNDAY];

    /**
     * Calculate and return available appointment slots for a given doctor and date.
     */
    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data.', 'errors' => $validator->errors()], 422);
        }

        $doctorId = $request->input('doctor_id');
        $date = Carbon::parse($request->input('date'))->startOfDay();

        if (in_array($date->dayOfWeek, $this->nonWorkingDays)) {
            return response()->json(['slots' => [], 'message' => 'The doctor does not work on this day.']);
        }

        // Get all booked slots for the doctor on the selected date.
        $bookedSlots = Appointment::where('doctor_id', $doctorId)
            ->whereDate('appointment_datetime', $date)
            ->where('status', '!=', 'cancelled')
            ->get()
            ->map(fn($appt) => $appt->appointment_datetime->format('H:i'))
            ->toArray();

        // Generate all potential slots for the day.
        $morningSlots = $this->generateSlotsForPeriod(
            $date->copy()->setTimeFromTimeString($this->workingHours['start_morning']),
            $date->copy()->setTimeFromTimeString($this->workingHours['end_morning'])
        );
        $afternoonSlots = $this->generateSlotsForPeriod(
            $date->copy()->setTimeFromTimeString($this->workingHours['start_afternoon']),
            $date->copy()->setTimeFromTimeString($this->workingHours['end_afternoon'])
        );
        
        $allPotentialSlots = array_merge($morningSlots, $afternoonSlots);

        // Filter out slots that are already booked or are in the past (if it's today).
        $availableSlots = array_filter($allPotentialSlots, function ($slot) use ($bookedSlots, $date) {
            $isBooked = in_array($slot, $bookedSlots);
            
            // THE FIX: Only check if the slot is in the past IF the selected date is today.
            $isInThePast = false;
            if ($date->isToday()) {
                // Allow booking up to 5 minutes from now.
                $slotTime = Carbon::parse($date->toDateString() . ' ' . $slot);
                if ($slotTime->isBefore(Carbon::now()->addMinutes(5))) {
                    $isInThePast = true;
                }
            }
            
            return !$isBooked && !$isInThePast;
        });
        
        if (empty($availableSlots)) {
            return response()->json(['slots' => [], 'message' => 'No available slots for this day.']);
        }
        
        return response()->json(['slots' => array_values($availableSlots)]);
    }

    /**
     * A private helper that generates all possible time slots in a given period.
     * It no longer needs to know about booked slots or the current time.
     * @return array
     */
    private function generateSlotsForPeriod(Carbon $startTime, Carbon $endTime): array
    {
        $slots = [];
        $currentSlotTime = $startTime->copy();

        while ($currentSlotTime->lt($endTime)) {
            $slots[] = $currentSlotTime->format('H:i');
            $currentSlotTime->addMinutes($this->appointmentSlotIntervalMinutes);
        }
        
        return $slots;
    }
}