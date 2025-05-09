<div id="consultations" class="content-section">
    <div class="consultations-container">
        <div class="patients-header"> {{-- Re-using class, ensure it's suitable --}}
            <h2 class="section-title">Consultations récentes</h2>
            <button class="btn" id="new-consultation-btn-trigger">+ Nouvelle consultation</button> {{-- This might open a modal or go to a create page depending on your JS --}}
        </div>
        {{-- Loop through $consultations --}}
        <div class="consultation-card">
            <div class="consultation-header">
                <div class="consultation-patient">Sophie Dubois</div>
                <div class="consultation-date">05/05/2025 - 09:00</div>
            </div>
            <div class="consultation-details">
                <p><strong>Motif:</strong> Douleurs abdominales, fatigue</p>
                <p><strong>Notes:</strong> Patient se plaint de douleurs abdominales depuis 3 jours. Examen physique révèle une légère sensibilité dans le quadrant inférieur droit. Prescription d'antalgiques et analyses sanguines.</p>
            </div>
            <div class="consultation-actions">
                <button class="btn btn-sm">Voir détails</button>
                <button class="btn btn-sm btn-secondary">Ordonnance</button>
            </div>
        </div>
        <div class="consultation-card">
            <div class="consultation-header">
                <div class="consultation-patient">Jean Lefebvre</div>
                <div class="consultation-date">05/05/2025 - 10:15</div>
            </div>
            <div class="consultation-details">
                <p><strong>Motif:</strong> Suivi traitement hypertension</p>
                <p><strong>Notes:</strong> Tension artérielle sous contrôle: 130/85. Patient suit correctement son traitement. Renouvellement de l'ordonnance pour 3 mois.</p>
            </div>
            <div class="consultation-actions">
                <button class="btn btn-sm">Voir détails</button>
                <button class="btn btn-sm btn-secondary">Ordonnance</button>
            </div>
        </div>
    </div>
</div>
