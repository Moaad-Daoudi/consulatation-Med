{{-- This view extends the main doctor layout --}}
@extends('layouts.doctor_dashboard')

@section('title', 'Gestion des Rendez-vous')

@section('content')

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
    
    <div class="appointments-container">
        <div class="patients-header">
            <h2 class="section-title">Gestion des Rendez-vous</h2>
            {{-- This button opens the modal defined in your main layout --}}
            <button type="button" class="btn" data-modal-target="doctor-create-appointment-modal">
                + Créer un RDV
            </button>
        </div>

        <!-- Modal for the DOCTOR to create a new appointment -->
        @include('doctor.partials._modal_appointments')

        {{-- Include the filter form partial --}}
        @include('doctor.partials._appointment_filters')

        {{-- Include the appointments table partial, passing the data from the controller --}}
        @include('doctor.partials._appointments_table', ['appointments' => $appointments])

        {{-- Pagination links. The appends() method ensures filters are not lost when changing pages. --}}
        <div class="mt-4">
            {{ $appointments->appends(request()->query())->links() }}
        </div>
    </div>

@endsection