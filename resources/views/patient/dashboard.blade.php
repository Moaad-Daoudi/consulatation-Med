<div id="patient_dashboard_content" class="content-section active"> {{-- Ensure ID matches data-section --}}
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon appointments">📅</div>
            <div class="stat-info">
                <h3>{{-- Dynamic data: $upcomingAppointmentCount ?? --}}1</h3>
                <p>Prochain rendez-vous</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon prescriptions">💊</div>
            <div class="stat-info">
                <h3>{{-- $activePrescriptionsCount ?? --}}2</h3>
                <p>Ordonnances actives</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🔬</div>
            <div class="stat-info">
                <h3>{{-- $pendingLabResultsCount ?? --}}1</h3>
                <p>Résultats en attente</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon messages">💬</div>
            <div class="stat-info">
                <h3>{{-- $newMessagesCount ?? --}}2</h3>
                <p>Nouveaux messages</p>
            </div>
        </div>
    </div>

    <div class="content-container">
        <h2 class="section-title">Prochain rendez-vous</h2>
        {{-- Fetch $nextAppointment --}}
        <div class="appointment-item">
            <div class="appointment-time">Aujourd'hui, 11:30</div>
            <div class="appointment-doctor">Dr. Richard Martin</div>
            <div class="appointment-type">Résultats analyses</div>
            <div class="appointment-status status-scheduled">À venir</div>
        </div>
        <div class="form-actions" style="justify-content: flex-start; margin-top: 20px;">
            <button class="btn btn-secondary btn-sm">Modifier</button>
            <button class="btn btn-sm">Ajouter à l'agenda</button>
        </div>
    </div>

    <div class="content-container">
        <h2 class="section-title">Rappels de médicaments</h2>
        <div class="medication-list">
            {{-- Loop through $medicationReminders --}}
            <div class="medication-item">
                <div class="medication-name">Paracétamol 500mg</div>
                <div class="medication-dosage">1 comprimé matin et soir pendant 5 jours</div>
            </div>
            <div class="medication-item">
                <div class="medication-name">Ibuprofène 400mg</div>
                <div class="medication-dosage">1 comprimé si douleur, maximum 3 par jour pendant 3 jours</div>
            </div>
        </div>
    </div>
</div>
