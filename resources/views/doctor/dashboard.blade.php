{{-- doctor.dashboard.blade.php --}}
<div id="dashboard" class="content-section active">
    <div class="dashboard-stats">
        {{-- Card 1: Rendez-vous aujourd'hui --}}
        <div class="stat-card card-appointments">
            <div class="stat-icon-img-only">
                <img src="{{ asset('assets/dashboard/appointment.png') }}" alt="Rendez-vous">
            </div>
            <div class="stat-info">
                <h3>{{ $appointmentsTodayCount ?? 0 }}</h3>
                <p>Rendez-vous aujourd'hui</p>
            </div>
        </div>

        {{-- Card 2: Patients uniques --}}
        <div class="stat-card card-patients">
            <div class="stat-icon-img-only">
                <img src="{{ asset('assets/dashboard/patients.png') }}" alt="Patients">
            </div>
            <div class="stat-info">
                <h3>{{ $totalUniquePatientsCount ?? 0 }}</h3>
                <p>Patients uniques (consultés)</p>
            </div>
        </div>

        {{-- Card 3: Ordonnances ce mois --}}
        <div class="stat-card card-prescriptions">
            <div class="stat-icon-img-only">
                <img src="{{ asset('assets/dashboard/prescriptions_did.png') }}" alt="Ordonnances">
            </div>
            <div class="stat-info">
                <h3>{{ $prescriptionsThisMonthCount ?? 0 }}</h3>
                <p>Ordonnances ce mois</p>
            </div>
        </div>
    </div>

    {{-- Recent Activities Section --}}
    <div class="content-container recent-activities-container mt-4"> {{-- Adjusted margin-top --}}
        <h2 class="section-title">Activités Récentes</h2>
        @if(isset($recentActivities) && $recentActivities->count() > 0)
            <div class="div-table recent-activities-list">
                <div class="div-table-header">
                    <div class="div-table-cell activity-date-col">Date</div>
                    <div class="div-table-cell activity-type-col">Type</div>
                    <div class="div-table-cell activity-patient-col">Patient</div>
                    <div class="div-table-cell activity-desc-col">Description</div>
                    <div class="div-table-cell activity-status-col">Statut</div>
                </div>
                @foreach($recentActivities as $activity)
                    <div class="div-table-row">
                        <div class="div-table-cell activity-date-col">
                            @if(isset($activity['activity_date']))
                                {{ \Carbon\Carbon::parse($activity['activity_date'])->format('d/m/Y H:i') }}
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="div-table-cell activity-type-col">
                            @if(isset($activity['type']))
                                <span class="badge activity-type-{{ Str::slug($activity['type']) }}">{{ $activity['type'] }}</span>
                            @else
                                N/A
                            @endif
                        </div>
                        <div class="div-table-cell activity-patient-col">
                            {{ $activity['patient_name'] ?? 'N/A' }}
                        </div>
                        <div class="div-table-cell activity-desc-col">
                            {{ isset($activity['description']) ? Str::limit($activity['description'], 70) : 'N/A' }}
                        </div>
                        <div class="div-table-cell activity-status-col">
                            @if(isset($activity['status']))
                                <span class="appointment-status status-{{ Str::slug($activity['status'], '-') }}">
                                    {{ $activity['status'] }}
                                </span>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center py-3">Aucune activité récente à afficher.</p>
        @endif
    </div>
</div>
