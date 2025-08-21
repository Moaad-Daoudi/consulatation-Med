@extends('layouts.patient_dashboard')

@section('title', 'Mes Rendez-vous')

@section('content')

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    {{-- Upcoming Appointments Section --}}
    <div class="content-container">
        <div class="section-title" style="display: flex; justify-content: space-between; align-items: center;">
            <span>Mes rendez-vous à venir</span>
            <button type="button" class="btn btn-sm btn-primary" data-modal-target="patient-create-appointment-modal">
                + Prendre un nouveau RDV
            </button>
        </div>
        @include('patient.partials._upcoming_appointments_table', ['appointments' => $upcomingAppointments])
    </div>

    {{-- Past Appointments Section --}}
    <div class="content-container mt-4">
        <h2 class="section-title">Historique des rendez-vous</h2>
        @include('patient.partials._past_appointments_table', ['appointments' => $pastAppointments])
        
        <div class="mt-4">
            {{ $pastAppointments->links() }}
        </div>
    </div>

    {{-- Include the modal for creating a new appointment --}}
    @include('patient.partials._create_appointment_modal', ['doctors' => $doctors])

@endsection