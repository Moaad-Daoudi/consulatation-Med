{{-- This file extends the main doctor layout you created --}}
@extends('layouts.doctor_dashboard')

{{-- This sets the page title in the <title> tag and can be used in the topbar --}}
@section('title', 'Tableau de Bord')

{{-- This is the main content that gets injected into the @yield('content') of the layout --}}
@section('content')

    {{-- A place to show success/error messages after an action --}}
    @if (session('success'))
        <div id="flash-message" class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Section for the main statistics cards at the top --}}
    <div class="dashboard-stats">
        {{-- Card 1: Appointments today --}}
        <div class="stat-card card-appointments">
            <div class="stat-icon-img-only">
                <img src="{{ asset('dashboard/appointment.png') }}" alt="Rendez-vous">
            </div>
            <div class="stat-info">
                {{-- This variable comes directly from the DashboardController --}}
                <h3>{{ $appointmentsTodayCount }}</h3>
                <p>Rendez-vous aujourd'hui</p>
            </div>
        </div>

        {{-- Card 2: Unique patients --}}
        <div class="stat-card card-patients">
            <div class="stat-icon-img-only">
                <img src="{{ asset('dashboard/patients.png') }}" alt="Patients">
            </div>
            <div class="stat-info">
                <h3>{{ $totalUniquePatientsCount }}</h3>
                <p>Patients uniques (consultés)</p>
            </div>
        </div>

        {{-- Card 3: Prescriptions this month --}}
        <div class="stat-card card-prescriptions">
            <div class="stat-icon-img-only">
                <img src="{{ asset('dashboard/prescriptions_did.png') }}" alt="Ordonnances">
            </div>
            <div class="stat-info">
                <h3>{{ $prescriptionsThisMonthCount }}</h3>
                <p>Ordonnances ce mois</p>
            </div>
        </div>
    </div>

    {{-- Section for the list of recent activities --}}
    <div class="content-container recent-activities-container mt-4">
        <h2 class="section-title">Activités Récentes</h2>

        {{-- Check if the $recentActivities collection exists and is not empty --}}
        @if(isset($recentActivities) && $recentActivities->isNotEmpty())
            <div class="div-table recent-activities-list">
                {{-- The Header Row --}}
                <div class="div-table-header">
                    <div class="div-table-cell activity-date-col">Date</div>
                    <div class="div-table-cell activity-type-col">Type</div>
                    <div class="div-table-cell activity-patient-col">Patient</div>
                    <div class="div-table-cell activity-desc-col">Description</div>
                    <div class="div-table-cell activity-status-col">Statut</div>
                </div>

                {{-- Loop through each activity passed from the controller --}}
                @foreach($recentActivities as $activity)
                    <div class="div-table-row">
                        <div class="div-table-cell activity-date-col">
                            {{-- Format the date for display --}}
                            {{ \Carbon\Carbon::parse($activity['date'])->format('d/m/Y H:i') }}
                        </div>
                        <div class="div-table-cell activity-type-col">
                            {{-- Display the activity type (e.g., Rendez-vous, Consultation) --}}
                            <span class="badge activity-type-{{ Str::slug($activity['type']) }}">
                                {{ $activity['type'] }}
                            </span>
                        </div>
                        <div class="div-table-cell activity-patient-col">
                            {{ $activity['patient_name'] }}
                        </div>
                        <div class="div-table-cell activity-desc-col">
                            {{ $activity['description'] }}
                        </div>
                        <div class="div-table-cell activity-status-col">
                             <span class="appointment-status status-{{ Str::slug($activity['status'], '-') }}">
                                {{ $activity['status'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- This message shows if there are no recent activities --}}
            <p class="text-center py-3">Aucune activité récente à afficher.</p>
        @endif
    </div>
@endsection