<div id="dashboard" class="content-section active">
    <div class="dashboard-stats">
        <div class="stat-card">
            <div class="stat-icon patients">👥</div>
            <div class="stat-info">
                <h3>{{-- Dynamic Data: e.g., $stats['total_patients'] ?? --}}128</h3>
                <p>Patients au total</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon appointments">📅</div>
            <div class="stat-info">
                <h3>{{-- $stats['appointments_today'] ?? --}}8</h3>
                <p>Rendez-vous aujourd'hui</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💊</div>
            <div class="stat-info">
                <h3>{{-- $stats['prescriptions_month'] ?? --}}42</h3>
                <p>Ordonnances ce mois</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon messages">💬</div>
            <div class="stat-info">
                <h3>{{-- $stats['new_messages'] ?? --}}5</h3>
                <p>Nouveaux messages</p>
            </div>
        </div>
    </div>

    <div class="ordonnance-container"> {{-- This class name might be too specific if content varies --}}
        <h2 class="section-title">Activités récentes</h2>
        {{-- Loop through $recentActivities here --}}
        <div class="appointment-item">
            <div class="appointment-time">Aujourd'hui, 09:00</div>
            <div class="appointment-patient">Mme Sophie Dubois</div>
            <div class="appointment-type">Consultation générale</div>
            <div class="appointment-status status-completed">Terminé</div>
        </div>
        <div class="appointment-item">
            <div class="appointment-time">Aujourd'hui, 10:15</div>
            <div class="appointment-patient">M. Jean Lefebvre</div>
            <div class="appointment-type">Suivi traitement</div>
            <div class="appointment-status status-completed">Terminé</div>
        </div>
        <div class="appointment-item">
            <div class="appointment-time">Aujourd'hui, 11:30</div>
            <div class="appointment-patient">Mme Clara Martin</div>
            <div class="appointment-type">Résultats analyses</div>
            <div class="appointment-status status-scheduled">À venir</div>
        </div>
    </div>
</div>
