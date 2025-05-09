<div id="appointments" class="content-section">
    <div class="appointments-container">
        <div class="patients-header">
            <h2 class="section-title">Gestion des Rendez-vous</h2>
            {{-- MODIFIED: Button to trigger the new modal --}}
            <button type="button" class="btn" data-modal-target="doctor-create-appointment-modal" id="btn-open-doctor-create-appt-modal">
                + Créer un RDV
            </button>
        </div>

        {{-- Filters Form --}}
        <form method="GET" action="{{-- route('doctor.appointments.index_filtered_or_similar') --}}" class="mb-3 form-inline" id="filter-appointments-form">
            <div class="form-group">
                <label for="filter_date_doc_appt" class="sr-only">Date:</label> {{-- sr-only for accessibility if label is implicit --}}
                <input type="date" name="filter_date" id="filter_date_doc_appt" class="form-control form-control-sm" value="{{ request('filter_date') }}">
            </div>
            <div class="form-group">
                 <label for="filter_period_doc_appt" class="sr-only">Période:</label>
                 <select name="filter_period" id="filter_period_doc_appt" class="form-control form-control-sm">
                    <option value="">Tout (à venir par défaut)</option>
                    <option value="today" {{ request('filter_period') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                    <option value="this_week" {{ request('filter_period') == 'this_week' ? 'selected' : '' }}>Cette semaine</option>
                    <option value="this_month" {{ request('filter_period') == 'this_month' ? 'selected' : '' }}>Ce mois</option>
                 </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">Filtrer</button>
            <a href="{{-- route('doctor.appointments.index_default_or_similar') --}}" class="btn btn-sm btn-secondary ml-2" id="clear-filters-btn-doc-appt">Effacer</a>
        </form>

        <div class="appointments-list" id="doctor-appointments-list-container">
            @if(isset($appointments) && $appointments->count() > 0)
                @foreach ($appointments as $appointment)
                    <div class="appointment-item">
                        <div class="appointment-time">
                            {{ $appointment->appointment_datetime->format('d/m/Y H:i') }}
                        </div>
                        <div class="appointment-patient">
                            {{ $appointment->patient->name ?? 'Patient Inconnu' }}
                        </div>
                        <div class="appointment-type">
                            {{ $appointment->notes ? Str::limit($appointment->notes, 30) : ($appointment->type ?? 'Consultation') }}
                        </div>
                        <div class="appointment-status status-{{ $appointment->status ?? 'scheduled' }}">
                            {{ ucfirst($appointment->status ?? 'Prévu') }}
                        </div>
                        <div class="appointment-actions">
                            {{-- Add action buttons like view details, edit, cancel --}}
                            {{-- <a href="{{ route('doctor.appointments.show', $appointment->id) }}" class="btn btn-sm">Voir</a> --}}
                        </div>
                    </div>
                @endforeach
            @else
                <p>Aucun rendez-vous à afficher pour les filtres sélectionnés.</p>
            @endif
        </div>
        @if(isset($appointments) && method_exists($appointments, 'hasPages') && $appointments->hasPages())
            <div class="mt-3" id="appointments-pagination-links">
                {{ $appointments->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
