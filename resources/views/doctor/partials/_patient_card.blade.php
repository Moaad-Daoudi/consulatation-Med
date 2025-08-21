{{-- File: resources/views/doctor/partials/_patient_card.blade.php --}}

<div class="patient-card">
    <div class="patient-card-header">
        <div class="patient-avatar-sm">
            {{-- Use the initials accessor we created earlier --}}
            {{ $patient->initials }}
        </div>
        <h5 class="patient-name">{{ $patient->name }}</h5>
    </div>
    <div class="patient-card-body">
        <p class="patient-info-item">
            <span class="info-label">Email:</span>
            <span class="info-value">{{ $patient->email }}</span>
        </p>
        <p class="patient-info-item">
            <span class="info-label">Consultations avec vous:</span>
            {{-- This comes from the withCount in the controller --}}
            <span class="info-value">{{ $patient->consultations_with_doctor }}</span>
        </p>
        <p class="patient-info-item">
            <span class="info-label">Ordonnances de vous:</span>
            {{-- This also comes from withCount --}}
            <span class="info-value">{{ $patient->prescriptions_from_doctor }}</span>
        </p>
    </div>
    <div class="patient-card-footer">
        {{-- This button will trigger our JavaScript to open the modal --}}
        <button type="button" class="btn btn-sm btn-primary view-patient-dossier-btn"
                data-patient-id="{{ $patient->id }}"
                data-patient-name="{{ $patient->name }}">
            Voir Dossier Complet
        </button>
    </div>
</div>