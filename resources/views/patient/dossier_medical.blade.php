@extends('layouts.patient_dashboard')

@section('title', 'Mon Dossier Médical')

@section('content')

{{-- Section 1: Personal Information --}}
<div class="medical-file-section-container mb-4">
    <h2 class="section-title">Informations Personnelles</h2>
    <div class="personal-info-grid">
        <div class="info-block">
            <span class="info-label">Nom complet:</span>
            <span class="info-value">{{ $patientUser->name ?? 'N/A' }}</span>
        </div>
        <div class="info-block">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $patientUser->email ?? 'N/A' }}</span>
        </div>
        <div class="info-block">
            <span class="info-label">Date de naissance:</span>
            <span class="info-value">
                @if($patientUser->patient?->date_of_birth)
                    {{ \Carbon\Carbon::parse($patientUser->patient->date_of_birth)->format('d/m/Y') }}
                @else
                    Non renseignée
                @endif
            </span>
        </div>
        <div class="info-block">
            <span class="info-label">Sexe:</span>
            <span class="info-value text-capitalize">
                {{ $patientUser->patient?->gender ?? 'Non renseigné' }}
            </span>
        </div>
        <div class="info-block">
            <span class="info-label">Groupe Sanguin:</span>
            <span class="info-value">
                {{ $patientUser->patient?->blood_type ?? 'Non renseigné' }}
            </span>
        </div>
    </div>
</div>

{{-- Section 2: Medical History (Consultations) --}}
<div class="medical-file-section-container mb-4">
    <h2 class="section-title">Historique des Consultations</h2>
    @forelse($patientUser->receivedConsultations as $consultation)
        <div class="medical-entry-card">
            <div class="entry-header">
                <h5>Consultation du {{ $consultation->consultation_date->format('d/m/Y H:i') }}</h5>
                @if($consultation->doctor)
                    <span class="doctor-name">Avec Dr. {{ $consultation->doctor->name }}</span>
                @endif
            </div>
            <div class="entry-detail">
                <strong>Motif de la visite:</strong>
                <div class="detail-content">{{ $consultation->reason_for_visit ?: 'N/A' }}</div>
            </div>
            @if($consultation->diagnosis)
                <div class="entry-detail">
                    <strong>Diagnostic:</strong>
                    <div class="detail-content">{{ $consultation->diagnosis }}</div>
                </div>
            @endif
        </div>
    @empty
        <p class="text-center py-3">Aucun historique de consultation trouvé.</p>
    @endforelse
</div>

{{-- Section 3: Prescriptions History --}}
<div class="medical-file-section-container">
    <h2 class="section-title">Historique des Ordonnances</h2>
    @forelse($patientUser->receivedPrescriptions as $prescription)
        <div class="medical-entry-card">
            <div class="entry-header">
                <h5>Ordonnance du {{ $prescription->prescription_date->format('d/m/Y') }}</h5>
                @if($prescription->doctor)
                    <span class="doctor-name">Par Dr. {{ $prescription->doctor->name }}</span>
                @endif
            </div>
            @if($prescription->items->isNotEmpty())
                <div class="entry-detail">
                    <strong>Médicaments prescrits:</strong>
                    <ul class="list-unstyled mt-2">
                        @foreach($prescription->items as $item)
                            <li class="medication-list-item">
                                <span class="med-name">{{ $item->medication_name }}</span>
                                <span class="med-details">
                                    {{ $item->dosage }} - {{ $item->frequency }} - {{ $item->duration }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p>Aucun médicament listé pour cette ordonnance.</p>
            @endif
        </div>
    @empty
        <p class="text-center py-3">Aucun historique d'ordonnance trouvé.</p>
    @endforelse
</div>

@endsection