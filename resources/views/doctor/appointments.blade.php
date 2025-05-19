<div id="appointments" class="content-section">
    <div class="appointments-container">
        <div class="patients-header">
            <h2 class="section-title">Gestion des Rendez-vous</h2>
            <button type="button" class="btn" data-modal-target="doctor-create-appointment-modal" id="btn-open-doctor-create-appt-modal"> + Créer un RDV</button>
        </div>

        <form method="GET" action="{{ route('dashboard') }}#appointments" class="mb-3 form-inline" id="filter-appointments-form">
            <div class="form-group"><label for="filter_date_doc_appt" class="sr-only">Date:</label><input type="date" name="filter_date" id="filter_date_doc_appt" class="form-control form-control-sm" value="{{ request('filter_date') }}"></div>
            <div class="form-group"><label for="filter_period_doc_appt" class="sr-only">Période:</label><select name="filter_period" id="filter_period_doc_appt" class="form-control form-control-sm"><option value="">Filtrer...</option><option value="today" {{ request('filter_period')=='today'?'selected':'' }}>Aujourd'hui</option><option value="this_week" {{ request('filter_period')=='this_week'?'selected':'' }}>Cette semaine</option><option value="this_month" {{ request('filter_period')=='this_month'?'selected':'' }}>Ce mois</option></select></div>
            <button type="submit" class="btn btn-sm btn-primary">Filtrer</button>
            <a href="{{ route('dashboard') }}#appointments" class="btn btn-sm btn-secondary ml-2">Effacer</a>
        </form>

        <div class="div-table appointments-list" id="doctor-appointments-list-container">
            <div class="div-table-header appointment-item-header-row">
                <div class="div-table-cell appointment-time-header">Date & Heure</div>
                <div class="div-table-cell appointment-patient-header">Patient</div>
                <div class="div-table-cell appointment-type-header">Type/Notes</div>
                <div class="div-table-cell appointment-status-header">Statut</div>
                <div class="div-table-cell appointment-actions-header">Actions</div>
            </div>

            @forelse ($appointments as $appointment)
                <div class="div-table-row appointment-item-data-row">
                    <div class="div-table-cell appointment-time">
                        {{ $appointment->appointment_datetime ? \Illuminate\Support\Carbon::parse($appointment->appointment_datetime)->format('d/m/Y H:i') : 'Date N/A' }}
                    </div>
                    <div class="div-table-cell appointment-patient">
                        {{ $appointment->patient->name ?? 'Patient Inconnu' }}
                    </div>
                    <div class="div-table-cell appointment-type">
                        {{ $appointment->notes ?? $appointment->reason ? Str::limit($appointment->notes ?? $appointment->reason, 30) : 'Consultation' }}
                    </div>
                    <div class="div-table-cell appointment-status-cell">
                        <span class="appointment-status @if($appointment->status === 'completed') status-completed @elseif(in_array($appointment->status, ['scheduled'])) status-scheduled @elseif($appointment->status === 'cancelled') status-cancelled @else status-default @endif">
                            @if($appointment->status === 'completed') Terminé
                            @elseif(in_array($appointment->status, ['scheduled'])) Planifié
                            @elseif($appointment->status === 'cancelled') Annulé
                            @else {{ ucfirst($appointment->status ?? 'Indéfini') }} @endif
                        </span>
                    </div>
                    <div class="div-table-cell appointment-actions">
                        @if(in_array($appointment->status, ['scheduled',]))
                            <form action="{{ route('doctor.appointments.complete', $appointment->id) }}" method="POST" style="display:inline-block; margin-right: 5px;">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-sm btn-icon btn-success-img" title="Marquer comme terminé">
                                    <img src="{{ asset('assets/dashboard/verifier.png') }}" alt="Terminé" class="button-img-icon">
                                </button>
                            </form>
                        @endif

                        @if(in_array($appointment->status, ['scheduled', 'cancelled']))
                            <form action="{{ route('doctor.appointments.destroy', $appointment->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Êtes-vous sûr de vouloir SUPPRIMER DÉFINITIVEMENT ce rendez-vous ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-icon btn-danger-img" title="Supprimer ce RDV (Action Irréversible)">
                                    <img src="{{ asset('assets/dashboard/annuler.png') }}" alt="Supprimer" class="button-img-icon">
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                 <div class="div-table-row">
                    <div class="div-table-cell" style="text-align:center; padding:20px; grid-column:1 / -1; column-span: all;">
                        Aucun rendez-vous trouvé correspondant à vos filtres.
                    </div>
                </div>
            @endforelse
        </div>

        @if(isset($appointments) && method_exists($appointments, 'hasPages') && $appointments->hasPages())
            <div class="mt-3 d-flex justify-content-center">
                {{ $appointments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
