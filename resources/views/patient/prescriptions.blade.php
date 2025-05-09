<div id="patient_prescriptions_content" class="content-section">
    <div class="content-container">
        <h2 class="section-title">Ordonnances actives</h2>
        {{-- Loop through $activePrescriptions --}}
        <div class="prescription-card">
            <div class="prescription-header">
                <div class="prescription-doctor">Dr. Richard Martin</div>
                <div class="prescription-date">01/05/2025</div>
            </div>
            <div class="prescription-details">
                <strong>Diagnostic:</strong> Syndrome grippal
            </div>
            <div class="medication-list">
                <div class="medication-item">
                    <div class="medication-name">Paracétamol 500mg</div>
                    <div class="medication-dosage">1 comprimé matin et soir pendant 5 jours</div>
                </div>
            </div>
            <div class="prescription-actions">
                <button class="btn btn-sm btn-secondary">Télécharger PDF</button>
                <button class="btn btn-sm">Envoyer à la pharmacie</button>
            </div>
        </div>
        {{-- More active prescriptions --}}
    </div>

    <div class="content-container">
        <h2 class="section-title">Historique des ordonnances</h2>
        {{-- Loop through $pastPrescriptions --}}
        <div class="prescription-card">
            <div class="prescription-header">
                <div class="prescription-doctor">Dr. Richard Martin</div>
                <div class="prescription-date">15/04/2025</div>
            </div>
            {{-- Details and medications --}}
            <div class="prescription-actions">
                <button class="btn btn-sm btn-secondary">Télécharger PDF</button>
            </div>
        </div>
    </div>
</div>
