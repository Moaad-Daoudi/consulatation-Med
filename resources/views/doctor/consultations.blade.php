@extends('layouts.doctor_dashboard')

@section('title', 'Gestion des Consultations')

@section('content')

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <strong>Erreurs de validation:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="consultations-container">
        <div class="patients-header">
            <h2 class="section-title">Consultations Médicales</h2>
            <button type="button" class="btn" data-modal-target="createConsultationModal">
                + Nouvelle Consultation
            </button>
        </div>

        {{-- Include the consultations table partial --}}
        @include('doctor.partials._consultations_table', ['consultations' => $consultations])

        {{-- Pagination links --}}
        <div class="mt-4">
            {{ $consultations->links() }}
        </div>
    </div>

    {{-- Include all the modals needed for this page --}}
    @include('doctor.partials._consultation_create_modal')
    @include('doctor.partials._consultation_edit_modal')
    @include('doctor.partials._consultation_view_modal')

@endsection