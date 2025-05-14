<div id="patients" class="content-section">
    <div class="patients-container">
        <div class="patients-header">
            <h2 class="section-title">Mes Patients</h2>
            {{-- Button to add a new patient (if you have this separate functionality, otherwise remove) --}}
            <button type="button" class="btn" data-modal-target="add-patient-modal" id="btn-open-add-patient-modal">+ Nouveau patient</button>
        </div>

        @if(isset($doctorPatients) && $doctorPatients->count() > 0)
            <div class="patient-cards-container mt-4">
                @foreach($doctorPatients as $patient)
                    <div class="patient-card">
                        <div class="patient-card-header">
                            <div class="patient-avatar-sm">
                                {{ strtoupper(substr($patient->name, 0, 2)) }}
                            </div>
                            <h5 class="patient-name">{{ $patient->name }}</h5>
                        </div>
                        <div class="patient-card-body">
                            <p class="patient-info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value">{{ $patient->email }}</span>
                            </p>
                            {{-- Add other relevant info if available directly on User model like phone, DOB --}}
                            {{-- <p class="patient-info-item">
                                <span class="info-label">Téléphone:</span>
                                <span class="info-value">{{ $patient->phone ?? 'N/A' }}</span>
                            </p>
                            <p class="patient-info-item">
                                <span class="info-label">Date de Naissance:</span>
                                <span class="info-value">{{ $patient->date_of_birth ? $patient->date_of_birth->format('d/m/Y') : 'N/A' }}</span>
                            </p> --}}
                            <p class="patient-info-item">
                                <span class="info-label">Consultations avec vous:</span>
                                <span class="info-value">{{ $patient->consultations_with_doctor }}</span>
                            </p>
                            <p class="patient-info-item">
                                <span class="info-label">Ordonnances de vous:</span>
                                <span class="info-value">{{ $patient->prescriptions_from_doctor }}</span>
                            </p>
                            {{-- You could fetch and display last visit date here if needed --}}
                        </div>
                        <div class="patient-card-footer">
                            <button type="button" class="btn btn-sm btn-primary view-patient-dossier-btn"
                                    data-patient-id="{{ $patient->id }}"
                                    data-modal-target="viewPatientDossierModal"> {{-- Or data-section-target for SPA --}}
                                Voir Dossier Complet
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($doctorPatients->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $doctorPatients->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
                </div>
            @endif
        @else
            <p class="mt-4 text-center">Aucun patient trouvé ayant eu des consultations ou des ordonnances avec vous.</p>
        @endif
    </div>
</div>


