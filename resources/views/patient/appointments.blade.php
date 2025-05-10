<div id="patient_appointments_content" class="content-section">
    <div class="content-container">
        <div class="section-title d-flex justify-content-between align-items-center">
            <span>Mes rendez-vous à venir</span>
            <button type="button" class="btn btn-primary btn-sm" data-modal-target="patient-create-appointment-modal">
                + Prendre un nouveau RDV
            </button>
        </div>

        @if(isset($upcomingAppointments) && $upcomingAppointments->count() > 0)
            <div class="table-responsive">
                <table class="table patient-appointments-table">
                    <thead>
                        <tr>
                            <th class="appointment-time-header">Date & Heure</th>
                            <th class="appointment-doctor-header">Docteur</th>
                            <th class="appointment-type-header">Motif/Notes</th>
                            <th class="appointment-status-header">Statut</th>
                            {{-- REMOVED Actions Header --}}
                            {{-- <th class="appointment-actions-header">Actions</th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingAppointments as $appointment)
                        <tr>
                            <td class="appointment-time">
                                {{ $appointment->appointment_datetime ? \Illuminate\Support\Carbon::parse($appointment->appointment_datetime)->format('d/m/Y H:i') : 'Date N/A' }}
                            </td>
                            <td class="appointment-doctor">
                                Dr. {{ $appointment->doctor->name ?? 'N/A' }}
                            </td>
                            <td class="appointment-type">
                                {{ $appointment->notes ? Str::limit($appointment->notes, 40) : 'Consultation' }}
                            </td>
                            <td class="appointment-status-cell">
                                <span class="appointment-status status-{{$appointment->status ?? 'default'}}">
                                    {{ ucfirst($appointment->status ?? 'Indéfini') }}
                                </span>
                            </td>
                            {{-- REMOVED Actions Cell and Cancel Button Logic --}}
                            {{--
                            <td class="appointment-actions">
                                @php
                                    $isCancellableByPatient = false;
                                    if ($appointment->appointment_datetime) {
                                        $apptDateTime = \Illuminate\Support\Carbon::parse($appointment->appointment_datetime);
                                        $hoursDifference = now()->diffInHours($apptDateTime, false);
                                        $isCancellableByPatient = $appointment->status === 'scheduled' &&
                                                                  $apptDateTime->isFuture() &&
                                                                  $hoursDifference <= -2;
                                    }
                                @endphp

                                @if($isCancellableByPatient)
                                    <form action="{{ route('patient.appointments.cancel', $appointment->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');" style="display:inline-block;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Annuler ce rendez-vous">Annuler</button>
                                    </form>
                                @endif
                            </td>
                            --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>Aucun rendez-vous à venir.</p>
        @endif
    </div>

    {{-- Past Appointments Section --}}
    <div class="content-container mt-4">
        <h2 class="section-title">Historique des rendez-vous</h2>
        @if(isset($pastAppointments) && $pastAppointments->count() > 0)
            <div class="table-responsive">
                <table class="table patient-appointments-table">
                    <thead>
                        <tr>
                            <th class="appointment-time-header">Date & Heure</th>
                            <th class="appointment-doctor-header">Docteur</th>
                            <th class="appointment-type-header">Motif/Notes</th>
                            <th class="appointment-status-header">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pastAppointments as $appointment)
                        <tr>
                            <td class="appointment-time">
                                {{ $appointment->appointment_datetime ? \Illuminate\Support\Carbon::parse($appointment->appointment_datetime)->format('d/m/Y H:i') : 'Date N/A' }}
                            </td>
                            <td class="appointment-doctor">
                                Dr. {{ $appointment->doctor->name ?? 'N/A' }}
                            </td>
                            <td class="appointment-type">
                                {{ $appointment->notes ? Str::limit($appointment->notes, 40) : 'Consultation' }}
                            </td>
                            <td class="appointment-status-cell">
                                <span class="appointment-status status-{{$appointment->status ?? 'default'}}">
                                    @if($appointment->status === 'completed') Terminé
                                    @elseif($appointment->status === 'cancelled') Annulé
                                    @else {{ ucfirst($appointment->status ?? 'Passé') }}
                                    @endif
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(method_exists($pastAppointments, 'links'))
                <div class="mt-3">
                    {{ $pastAppointments->links() }}
                </div>
            @endif
        @else
            <p>Aucun rendez-vous dans l'historique.</p>
        @endif
    </div>
</div>
