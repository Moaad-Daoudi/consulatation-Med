<div id="patient_medical_file_content" class="content-section">
    {{-- Section 1: Informations Personnelles --}}
    <div class="medical-file-section-container mb-4">
        <h2 class="section-title">Informations Personnelles</h2>
        <div class="personal-info-grid">
            <div class="info-block">
                <span class="info-label">Nom complet:</span>
                <span class="info-value">{{ Auth::user()->name ?? 'N/A' }}</span>
            </div>
            <div class="info-block">
                <span class="info-label">Email:</span>
                <span class="info-value">{{ Auth::user()->email ?? 'N/A' }}</span>
            </div>
            <div class="info-block">
                <span class="info-label">Téléphone:</span>
                <span class="info-value">{{ Auth::user()->phone_number ?? 'Non renseigné' }}</span>
            </div>
            <div class="info-block">
                <span class="info-label">Date de naissance:</span>
                <span class="info-value">
                    @if(Auth::user()->patient && Auth::user()->patient->date_of_birth)
                        {{ \Carbon\Carbon::parse(Auth::user()->patient->date_of_birth)->format('d/m/Y') }}
                    @else
                        Non renseignée
                    @endif
                </span>
            </div>
            <div class="info-block">
                <span class="info-label">Sexe:</span>
                <span class="info-value">
                    @if(Auth::user()->patient && Auth::user()->patient->gender)
                        @if(Auth::user()->patient->gender === 'male')
                            Homme
                        @elseif(Auth::user()->patient->gender === 'female')
                            Femme
                        @elseif(Auth::user()->patient->gender === 'other')
                            Autre
                        @else
                            {{ ucfirst(Auth::user()->patient->gender) }} {{-- Fallback for other values --}}
                        @endif
                    @else
                        Non renseigné
                    @endif
                </span>
            </div>
            <div class="info-block">
                <span class="info-label">Adresse Postale:</span>
                <span class="info-value">
                    {{-- Assuming 'address' column exists on the Patient model --}}
                    {{ optional(Auth::user()->patient)->address ?? 'Non renseignée' }}
                </span>
            </div>
             <div class="info-block">
                <span class="info-label">Téléphone d'urgence:</span>
                <span class="info-value">
                    {{ optional(Auth::user()->patient)->emergency_contact ?? 'Non renseigné' }}
                </span>
            </div>
        </div>
        <div class="mt-4 text-end">
            {{--
                IMPORTANT: The route 'profile.edit' is likely not defined if you are using an SPA approach.
                This button should either be removed, or use JavaScript to switch to the
                'patient_settings_content' data-section.
                Example JS approach (rough, would need integration with your existing SPA logic):
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="activateProfileSettingsSection()">
                    Modifier mes informations
                </button>
                <script>
                    function activateProfileSettingsSection() {
                        // Find the profile link in the sidebar and click it programmatically
                        const profileLink = document.querySelector('a[data-section="patient_settings_content"]');
                        if (profileLink) {
                            profileLink.click();
                        }
                    }
                </script>
            --}}
            {{-- For now, let's make it a button that does nothing or link to the SPA section via JS if desired later --}}
            <button type="button" class="btn btn-sm btn-outline-primary"
                    onclick="document.querySelector('a[data-section=\'patient_settings_content\']')?.click();">
                Modifier mes informations
            </button>
        </div>
    </div>

    {{-- Section 2: Antécédents Médicaux (from Consultations) --}}
    <div class="medical-file-section-container mb-4">
        <h2 class="section-title">Antécédents Médicaux et Consultations</h2>
        @if(isset($patientConsultations) && $patientConsultations->count() > 0)
            @foreach($patientConsultations as $consultation)
                <div class="medical-entry-card">
                    <div class="entry-header">
                        <h5>Consultation du {{ \Carbon\Carbon::parse($consultation->consultation_date)->format('d/m/Y H:i') }}</h5>
                        @if($consultation->doctor)
                            <span class="doctor-name">Avec Dr. {{ $consultation->doctor->name }}</span>
                        @endif
                    </div>
                    <div class="entry-detail">
                        <strong>Motif de la visite:</strong>
                        <div class="detail-content">{{ $consultation->reason_for_visit ?: 'Non spécifié' }}</div>
                    </div>
                    @if($consultation->symptoms)
                        <div class="entry-detail">
                            <strong>Symptômes décrits:</strong>
                            <div class="detail-content">{{ $consultation->symptoms }}</div>
                        </div>
                    @endif
                    @if($consultation->notes)
                        <div class="entry-detail">
                            <strong>Notes du médecin:</strong>
                            <div class="detail-content">{{ $consultation->notes }}</div>
                        </div>
                    @endif
                    @if($consultation->diagnosis)
                        <div class="entry-detail">
                            <strong>Diagnostic:</strong>
                            <div class="detail-content">{{ $consultation->diagnosis }}</div>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-center py-3">Aucun antécédent de consultation enregistré.</p>
        @endif
    </div>

    {{-- Section 3: Prescriptions & Treatment History --}}
    <div class="medical-file-section-container">
        <h2 class="section-title">Historique des Ordonnances</h2>
        {{-- Assuming $activePrescriptions and $pastPrescriptions are combined or you choose one to display here --}}
        {{-- For simplicity, let's assume you pass a single $allPatientPrescriptions collection for the medical file --}}
        @php
            // Combine prescriptions if they are separate, or use a single collection passed from controller
            // This is just an example; adjust based on how you pass data from the controller
            if (isset($activePrescriptions) && isset($pastPrescriptions)) {
                $allPatientPrescriptionsForFile = $activePrescriptions->merge($pastPrescriptions)->sortByDesc('prescription_date');
            } elseif (isset($allPatientPrescriptions)) { // If controller passes 'allPatientPrescriptions'
                 $allPatientPrescriptionsForFile = $allPatientPrescriptions;
            } else {
                $allPatientPrescriptionsForFile = collect();
            }
        @endphp

        @if($allPatientPrescriptionsForFile->count() > 0)
            @foreach($allPatientPrescriptionsForFile as $prescription)
                <div class="medical-entry-card">
                    <div class="entry-header">
                        <h5>Ordonnance du {{ \Carbon\Carbon::parse($prescription->prescription_date)->format('d/m/Y') }}</h5>
                        @if($prescription->doctor)
                            <span class="doctor-name">Par Dr. {{ $prescription->doctor->name }}</span>
                        @endif
                    </div>

                    @if($prescription->consultation)
                        <p class="text-muted mb-2" style="font-size:0.9em;">
                            Liée à la consultation du {{ \Carbon\Carbon::parse($prescription->consultation->consultation_date)->format('d/m/Y') }}
                            (Motif: {{ Str::limit($prescription->consultation->reason_for_visit, 50) }})
                        </p>
                    @endif

                    @if($prescription->general_notes)
                        <div class="entry-detail">
                            <strong>Notes générales de l'ordonnance:</strong>
                            <div class="detail-content">{{ $prescription->general_notes }}</div>
                        </div>
                    @endif

                    @if($prescription->items && $prescription->items->count() > 0)
                        <div class="entry-detail mt-3">
                            <strong>Médicaments prescrits:</strong>
                            <ul class="list-unstyled mt-2">
                                @foreach($prescription->items as $item)
                                    <li class="medication-list-item">
                                        <span class="med-name">{{ $item->medication_name }}</span>
                                        <span class="med-details">
                                            @if($item->dosage) Dose: {{ $item->dosage }}. @endif
                                            @if($item->frequency) Fréquence: {{ $item->frequency }}. @endif
                                            @if($item->duration) Durée: {{ $item->duration }}. @endif
                                        </span>
                                        @if($item->notes)<span class="med-notes">Notes: {{ $item->notes }}</span>@endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p>Aucun médicament listé pour cette ordonnance.</p>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-center py-3">Aucune ordonnance enregistrée.</p>
        @endif
    </div>
</div>
