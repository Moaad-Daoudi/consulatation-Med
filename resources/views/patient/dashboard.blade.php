<div id="patient_dashboard_content" class="content-section active">
    <div class="dashboard-stats">
        {{-- Card 1: Upcoming Appointments --}}
        <div class="stat-card card-patient-appointments"> {{-- Added specific class --}}
            <div class="stat-icon-img-only">
                {{-- Use an image, similar to doctor's dashboard --}}
                <img src="{{ asset('assets/dashboard/appointment.png') }}" alt="Rendez-vous">
            </div>
            <div class="stat-info">
                <h3>{{ $upcomingAppointmentCount ?? 0 }}</h3>
                <p>Prochain(s) rendez-vous</p>
            </div>
        </div>

        {{-- Card 2: Active Prescriptions --}}
        <div class="stat-card card-patient-prescriptions"> {{-- Added specific class --}}
            <div class="stat-icon-img-only">
                {{-- Use an image, e.g., the one for prescriptions from doctor's dashboard or a new one --}}
                <img src="{{ asset('assets/dashboard/prescriptions_did.png') }}" alt="Ordonnances">
            </div>
            <div class="stat-info">
                <h3>{{ $activePrescriptionsCount ?? 0 }}</h3>
                <p>Ordonnance(s) active(s)</p>
            </div>
        </div>
        {{-- Add more stat cards here if needed, following the same pattern --}}
    </div>

    {{-- The rest of your patient dashboard content (Next appointment, Medication reminders) remains the same --}}
    <div class="content-container">
        <h2 class="section-title">Prochain rendez-vous</h2>
        @if($nextAppointment)
            @php
                $appointmentDateTime = \Carbon\Carbon::parse($nextAppointment->appointment_datetime);
            @endphp
            <div class="appointment-item" style="padding: 15px; border: 1px solid #eee; border-radius: 5px;">
                <div class="appointment-time" style="font-weight: bold; margin-bottom: 5px;">
                    {{ $appointmentDateTime->isoFormat('dddd D MMMM YYYY [à] HH[h]mm') }}
                    ({{ $appointmentDateTime->diffForHumans() }})
                </div>
                <div class="appointment-doctor" style="margin-bottom: 5px;">
                    Avec: <strong>Dr. {{ $nextAppointment->doctor->name ?? 'N/A' }}</strong>
                </div>
                <div class="appointment-type" style="font-size: 0.9em; color: #555; margin-bottom: 10px;">
                    Motif: {{ $nextAppointment->notes ?? 'Non spécifié' }}
                </div>
                <div class="appointment-status status-{{ strtolower($nextAppointment->status ?? 'scheduled') }}" style="display: inline-block; padding: 3px 8px; border-radius: 15px; font-size: 0.8em; color: white;">
                    {{ ucfirst($nextAppointment->status ?? 'Prévu') }}
                </div>
            </div>
        @else
            <p>Vous n'avez aucun rendez-vous à venir.</p>
        @endif
    </div>

    <div class="content-container">
        <h2 class="section-title">Rappels de médicaments (Ordonnances Actives)</h2>
        @if($medicationReminders && $medicationReminders->count() > 0)
            <div class="medication-list">
                @foreach($medicationReminders as $reminder)
                <div class="medication-item">
                    <div class="medication-name">{{ $reminder['name'] }}</div>
                    <div class="medication-dosage">
                        Posologie: {{ $reminder['dosage'] ?? 'N/A' }} - {{ $reminder['frequency'] ?? 'N/A' }}
                        <br>
                        Durée: {{ $reminder['duration'] ?? 'Selon prescription' }}
                        @if($reminder['notes'])
                            <br><em>Note: {{ $reminder['notes'] }}</em>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <p>Vous n'avez aucun rappel de médicament actif pour le moment.</p>
        @endif
    </div>
</div>
