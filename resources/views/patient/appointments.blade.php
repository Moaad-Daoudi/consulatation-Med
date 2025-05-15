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
                            <th class="appointment-actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($upcomingAppointments as $appointment)
                        <tr>
                            <td class="appointment-time">
                                @php $displayDateTime = $appointment->appointment_datetime ?? null; @endphp
                                {{ $displayDateTime ? \Illuminate\Support\Carbon::parse($displayDateTime)->format('d/m/Y H:i') : 'Date N/A' }}
                            </td>
                            <td class="appointment-doctor">
                                Dr. {{ $appointment->doctor->name ?? 'N/A' }}
                            </td>
                            <td class="appointment-type">
                                @php $displayNotes = $appointment->notes ?? null; @endphp
                                {{ $displayNotes ? Str::limit($displayNotes, 40) : 'Consultation' }}
                            </td>
                            <td class="appointment-status-cell">
                                <span class="appointment-status status-{{$appointment->status ?? 'default'}}">
                                    {{ ucfirst($appointment->status ?? 'Indéfini') }}
                                </span>
                            </td>
                            <td class="appointment-actions">
                                @php
                                    $isCancellableByPatient = false;
                                    $appointmentDateFieldSource = $appointment->appointment_datetime ?? null;

                                    if ($appointmentDateFieldSource && $appointment->status) {
                                        $apptDateTime = \Illuminate\Support\Carbon::parse($appointmentDateFieldSource);
                                        $now = \Illuminate\Support\Carbon::now();
                                        $cancellableStatuses = ['scheduled', 'pending', 'confirmed'];
                                        $statusIsOkayForCancel = in_array(strtolower($appointment->status), $cancellableStatuses);
                                        $isAppointmentInFuture = $apptDateTime->isAfter($now);
                                        $twoHoursFromNow = $now->copy()->addHours(2);
                                        $isFarEnoughInFuture = $apptDateTime->gte($twoHoursFromNow);
                                        if ($statusIsOkayForCancel && $isAppointmentInFuture && $isFarEnoughInFuture) {
                                            $isCancellableByPatient = true;
                                        }
                                    }
                                @endphp
                                @if($isCancellableByPatient)
                                    <form action="{{ route('patient.appointments.destroy', $appointment->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');" style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-warning" title="Annuler ce rendez-vous" style="background-color: red;">Annuler</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>Aucun rendez-vous à venir.</p>
        @endif
    </div>

    <div class="content-container mt-4">
        <h2 class="section-title">Historique des rendez-vous</h2>
        @if(isset($pastAppointments) && $pastAppointments->count() > 0)
            <div class="table-responsive">
                <table class="table patient-appointments-table">
                    <thead><tr><th class="appointment-time-header">Date & Heure</th><th class="appointment-doctor-header">Docteur</th><th class="appointment-type-header">Motif/Notes</th><th class="appointment-status-header">Statut</th></tr></thead>
                    <tbody>
                        @foreach($pastAppointments as $appointment)
                        <tr>
                            <td class="appointment-time">@php $dPDT = $appointment->appointment_datetime ?? null; @endphp {{ $dPDT ? \Illuminate\Support\Carbon::parse($dPDT)->format('d/m/Y H:i') : 'N/A' }}</td>
                            <td class="appointment-doctor">Dr. {{ $appointment->doctor->name ?? 'N/A' }}</td>
                            <td class="appointment-type">@php $dPN = $appointment->notes ?? null; @endphp {{ $dPN ? Str::limit($dPN, 40) : 'Consultation' }}</td>
                            <td class="appointment-status-cell"><span class="appointment-status status-{{$appointment->status??'default'}}">@if($appointment->status === 'completed')Terminé @elseif($appointment->status === 'cancelled')Annulé @else{{ucfirst($appointment->status??'Passé')}}@endif</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if(method_exists($pastAppointments, 'links')) <div class="mt-3">{{ $pastAppointments->links() }}</div> @endif
        @else
            <p>Aucun rendez-vous dans l'historique.</p>
        @endif
    </div>
</div>
