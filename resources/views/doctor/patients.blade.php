{{-- This view extends the main doctor layout --}}
@extends('layouts.doctor_dashboard')

@section('title', 'Mes Patients')

@section('content')

    <div class="patients-container">
        <div class="patients-header">
            <h2 class="section-title">Mes Patients</h2>
            {{-- Optional: A button to add a new patient could go here --}}
            {{-- <button type="button" class="btn" data-modal-target="add-patient-modal">+ Nouveau patient</button> --}}
        </div>

        @if(isset($doctorPatients) && $doctorPatients->count() > 0)
            {{-- The container for the patient cards --}}
            <div class="patient-cards-container mt-4">
                @foreach($doctorPatients as $patient)
                    {{-- We will use a partial for the card to keep this file clean --}}
                    @include('doctor.partials._patient_card', ['patient' => $patient])
                @endforeach
            </div>

            {{-- Pagination links --}}
            <div class="mt-4">
                {{ $doctorPatients->links() }}
            </div>
        @else
            <p class="mt-4 text-center">Aucun patient trouvé ayant eu des consultations ou des ordonnances avec vous.</p>
        @endif
    </div>
    
    {{-- This is where the modal for the dossier will live --}}
    @include('doctor.partials._patient_dossier_modal')

@endsection

@push('scripts')
    {{-- We will add the JavaScript to power the modal here --}}
    <script src="{{ asset('js/doctor_patient_dossier.js') }}" defer></script>
@endpush