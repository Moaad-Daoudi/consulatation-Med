{{-- File: resources/views/patient/dashboard.blade.php --}}

@extends('layouts.patient_dashboard')

@section('title', 'Tableau de Bord')

@section('content')

    {{-- Statistics Cards Section --}}
    <div class="dashboard-stats">
        <div class="stat-card card-patient-appointments">
            <div class="stat-icon-img-only">
                <img src="{{ asset('dashboard/appointment.png') }}" alt="Rendez-vous">
            </div>
            <div class="stat-info">
                {{-- This variable comes from the Patient/DashboardController --}}
                <h3>{{ $upcomingAppointmentCount }}</h3>
                <p>Prochain(s) rendez-vous</p>
            </div>
        </div>

        <div class="stat-card card-patient-prescriptions">
            <div class="stat-icon-img-only">
                <img src="{{ asset('dashboard/prescriptions_did.png') }}" alt="Ordonnances">
            </div>
            <div class="stat-info">
                <h3>{{ $activePrescriptionsCount }}</h3>
                <p>Ordonnance(s) active(s)</p>
            </div>
        </div>
    </div>

    {{-- Next Appointment Details Section --}}
    <div class="content-container">
        <h2 class="section-title">Prochain rendez-vous</h2>
        
        @if($nextAppointment)
            @php
                $appointmentDateTime = \Carbon\Carbon::parse($nextAppointment->appointment_datetime);
            @endphp
            <div class="appointment-item" style="padding: 15px; border: 1px solid #eee; border-radius: 8px;">
                <div class="appointment-time" style="font-weight: bold; margin-bottom: 5px; font-size: 1.1rem;">
                    {{-- Format date for better readability --}}
                    {{ $appointmentDateTime->isoFormat('dddd D MMMM YYYY [à] HH[h]mm') }}
                    <span style="font-weight: normal; color: #555;">({{ $appointmentDateTime->diffForHumans() }})</span>
                </div>
                <div class="appointment-doctor" style="margin-bottom: 10px;">
                    Avec: <strong>Dr. {{ $nextAppointment->doctor->name ?? 'N/A' }}</strong>
                </div>
                <div class="appointment-notes" style="font-size: 0.9em; color: #666;">
                    Motif: {{ $nextAppointment->notes ?: 'Non spécifié' }}
                </div>
            </div>
        @else
            <div class="text-center" style="background-color: var(--bg-light); border-radius: 8px;">
                <p>Vous n'avez aucun rendez-vous à venir.</p>
                {{-- Add a button to encourage booking --}}
                <a href="{{-- route('patient.appointments') --}}" class="btn btn-sm btn-primary">Prendre un rendez-vous</a>
            </div>
        @endif
    </div>

@endsection