<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Appointment;              
use Illuminate\Support\Carbon;           

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    $cutoffTime = Carbon::now()->subHours(2);

    Appointment::where('status', 'scheduled')
        ->where('appointment_datetime', '<', $cutoffTime)
        ->update(['status' => 'cancelled']);

})->everyMinute();