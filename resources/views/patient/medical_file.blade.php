<div id="patient_medical_file_content" class="content-section">
    <div class="content-container">
        <h2 class="section-title">Informations personnelles</h2>
        <div class="medical-info-section">
            <div class="medical-info-item">
                <div class="medical-info-label">Nom complet</div>
                <div class="medical-info-value">{{ Auth::user()->name ?? 'N/A' }}</div>
            </div>
            <div class="medical-info-item">
                <div class="medical-info-label">Date de naissance</div>
                <div class="medical-info-value">{{-- $patientData->dob ?? --}}15/03/1985</div>
            </div>
             <div class="medical-info-item">
                <div class="medical-info-label">Email</div>
                <div class="medical-info-value">{{ Auth::user()->email ?? 'N/A' }}</div>
            </div>
            {{-- More personal info from $patientData --}}
        </div>
    </div>

    <div class="content-container">
        <h2 class="section-title">Antécédents médicaux</h2>
        <div class="medical-info-section">
            <div class="medical-info-header">Allergies</div>
            <ul class="medical-info-list">
                {{-- Loop through $patientData->allergies --}}
                <li class="medical-info-item">
                    <div class="medical-info-label">Pénicilline</div>
                    <div class="medical-info-value">Réaction cutanée sévère</div>
                </li>
            </ul>
        </div>
        {{-- More sections like Maladies chroniques, Interventions, Vaccinations --}}
    </div>

     <div class="content-container">
        <h2 class="section-title">Historique des consultations</h2>
        <div class="medical-info-section">
            {{-- Loop through $patientData->consultations --}}
            <div class="medical-info-item">
                <div class="medical-info-label">01/05/2025</div>
                <div class="medical-info-value">
                    <strong>Dr. Richard Martin - Consultation générale</strong><br>
                    Syndrome grippal. Prescription: paracétamol et ibuprofène. Repos conseillé 3 jours.
                </div>
            </div>
        </div>
    </div>
</div>
