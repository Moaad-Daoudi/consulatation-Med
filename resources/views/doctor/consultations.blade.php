<div id="consultations" class="content-section">
    <div class="consultations-container">
        <div class="patients-header"> {{-- Renamed from section-header to patients-header for consistency with other sections if applicable --}}
            <h2 class="section-title">Consultations Médicales</h2>
            <button type="button" class="btn btn-primary" data-modal-target="createConsultationModal" id="btn-trigger-create-consultation-modal">
                + Nouvelle Consultation
            </button>
        </div>

        <div class="div-table consultations-list mt-3">
            <div class="div-table-header">
                <div class="div-table-cell">Date</div>
                <div class="div-table-cell">Patient</div>
                <div class="div-table-cell">Motif</div>
                <div class="div-table-cell">Actions</div>
            </div>
            @if(isset($consultations) && $consultations->count() > 0)
                @foreach($consultations as $consultation)
                <div class="div-table-row consultation-item-row">
                    <div class="div-table-cell consultation-date">
                        {{ $consultation->consultation_date ? \Illuminate\Support\Carbon::parse($consultation->consultation_date)->format('d/m/Y H:i') : 'N/A' }}
                    </div>
                    <div class="div-table-cell consultation-patient">
                        {{ $consultation->patient->name ?? 'Patient Inconnu' }}
                        @if($consultation->appointment_id && $consultation->appointment)
                            <br><small class="text-muted">(Lié au RDV du: {{ \Illuminate\Support\Carbon::parse($consultation->appointment->appointment_datetime)->format('d/m/Y H:i') }})</small>
                        @endif
                    </div>
                    <div class="div-table-cell consultation-reason">
                        {{ Str::limit($consultation->reason_for_visit ?? 'N/A', 50) }}
                    </div>
                    <div class="div-table-cell consultation-actions">
                        <button type="button" class="btn btn-sm btn-info view-consultation-details-btn"
                                data-modal-target="viewConsultationDetailModal"
                                data-consultation-details="{{ htmlspecialchars(json_encode($consultation->load(['patient', 'appointment'])), ENT_QUOTES, 'UTF-8') }}">
                            👁️ Voir
                        </button>
                        <button type="button" class="btn btn-sm btn-warning edit-consultation-btn"
                                data-modal-target="editConsultationModal"
                                data-id="{{ $consultation->id }}"
                                data-patient-name="{{ $consultation->patient->name ?? 'N/A' }}"
                                data-consultation-date="{{ $consultation->consultation_date->format('Y-m-d\TH:i') }}"
                                data-reason-for-visit="{{ htmlspecialchars($consultation->reason_for_visit ?? '', ENT_QUOTES) }}"
                                data-symptoms="{{ htmlspecialchars($consultation->symptoms ?? '', ENT_QUOTES) }}"
                                data-notes="{{ htmlspecialchars($consultation->notes ?? '', ENT_QUOTES) }}"
                                data-diagnosis="{{ htmlspecialchars($consultation->diagnosis ?? '', ENT_QUOTES) }}">
                                {{-- Removed data-treatment-plan --}}
                            ✏️ Modifier
                        </button>
                        <form action="{{ route('doctor.consultations.destroy', $consultation->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette consultation ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer Consultation">❌</button>
                        </form>
                    </div>
                </div>
                @endforeach
            @else
                <div class="div-table-row">
                    <div class="div-table-cell" style="text-align:center; padding: 20px; display:block; width:100%;">Aucune consultation trouvée.</div>
                </div>
            @endif
        </div>
        @if(isset($consultations) && method_exists($consultations, 'links') && $consultations->hasPages())
            <div class="mt-3">
                {{ $consultations->appends(request()->except('page', 'consultations_page'))->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
