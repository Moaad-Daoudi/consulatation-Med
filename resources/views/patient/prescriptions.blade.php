@extends('layouts.patient_dashboard')

@section('title', 'Mes Ordonnances')

@section('content')

    {{-- Active Prescriptions Section --}}
    <div class="medical-file-section-container mb-4">
        <h2 class="section-title">Mes Ordonnances Actives (30 derniers jours)</h2>
        
        @forelse($activePrescriptions as $prescription)
            @include('patient.partials._prescription_card', ['prescription' => $prescription])
        @empty
            <p class="text-center py-3">Aucune ordonnance active pour le moment.</p>
        @endforelse
    </div>

    {{-- Past Prescriptions Section --}}
    <div class="medical-file-section-container">
        <h2 class="section-title">Historique des Ordonnances</h2>

        @forelse($pastPrescriptions as $prescription)
            @include('patient.partials._prescription_card', ['prescription' => $prescription])
        @empty
            <p class="text-center py-3">Aucun historique d'ordonnances passées.</p>
        @endforelse
    </div>

    {{-- Include the modal for viewing prescription details --}}
    @include('patient.partials._prescription_view_modal')

@endsection