<div id="patient_appointments_content" class="content-section">
    <div class="content-container">
        <div class="section-title d-flex justify-content-between align-items-center">
            <span>Mes rendez-vous à venir</span>
            {{-- NOUVEAU: Button to trigger the new appointment modal --}}
            <button type="button" class="btn btn-primary btn-sm" data-modal-target="patient-create-appointment-modal">
                + Prendre un nouveau RDV
            </button>
        </div>
        {{-- Loop through $upcomingAppointments --}}
        @if(isset($upcomingAppointments) && $upcomingAppointments->count() > 0)
            @foreach($upcomingAppointments as $appointment)
            <div class="appointment-item">
                <div class="appointment-time">{{ $appointment->appointment_datetime->format('d/m/Y H:i') }}</div>
                <div class="appointment-doctor">Dr. {{ $appointment->doctor->name ?? 'N/A' }}</div>
                <div class="appointment-type">{{ $appointment->notes ? Str::limit($appointment->notes, 30) : 'Consultation' }}</div>
                <div class="appointment-status status-{{$appointment->status}}">{{ ucfirst($appointment->status) }}</div>
                {{-- Add actions like cancel if applicable --}}
            </div>
            @endforeach
        @else
            <p>Aucun rendez-vous à venir.</p>
            {{-- Static example for display --}}
            <div class="appointment-item">
                <div class="appointment-time">Demain, 11:30</div>
                <div class="appointment-doctor">Dr. Richard Martin (Exemple)</div>
                <div class="appointment-type">Résultats analyses</div>
                <div class="appointment-status status-scheduled">À venir</div>
            </div>
        @endif
    </div>

    <div class="content-container">
        <h2 class="section-title">Historique des rendez-vous</h2>
        {{-- Loop through $pastAppointments --}}
        @if(isset($pastAppointments) && $pastAppointments->count() > 0)
            @foreach($pastAppointments as $appointment)
            <div class="appointment-item">
                <div class="appointment-time">{{ $appointment->appointment_datetime->format('d/m/Y H:i') }}</div>
                <div class="appointment-doctor">Dr. {{ $appointment->doctor->name ?? 'N/A' }}</div>
                <div class="appointment-type">{{ $appointment->notes ? Str::limit($appointment->notes, 30) : 'Consultation' }}</div>
                <div class="appointment-status status-{{$appointment->status}}">{{ ucfirst($appointment->status) }}</div>
            </div>
            @endforeach
        @else
            <p>Aucun rendez-vous passé.</p>
            {{-- Static example for display --}}
            <div class="appointment-item">
                <div class="appointment-time">Hier, 09:00</div>
                <div class="appointment-doctor">Dr. Richard Martin (Exemple)</div>
                <div class="appointment-type">Consultation générale</div>
                <div class="appointment-status status-completed">Terminé</div>
            </div>
        @endif
    </div>
</div>
<style> /* Quick style for button alignment */
    .section-title.d-flex { display: flex; justify-content: space-between; align-items: center; }
</style>
