<div class="medical-entry-card">
    <div class="entry-header">
        <h5>Ordonnance du {{ $prescription->prescription_date->format('d/m/Y') }}</h5>
        @if($prescription->doctor)
            <span class="doctor-name">Prescrite par Dr. {{ $prescription->doctor->name }}</span>
        @endif
    </div>

    @if($prescription->general_notes)
        <div class="entry-detail">
            <strong>Notes Générales:</strong>
            <div class="detail-content">{{ Str::limit($prescription->general_notes, 100) }}</div>
        </div>
    @endif
    
    <div class="text-right mt-3">
        <button type="button" class="btn btn-sm btn-info view-prescription-btn"
                data-url="{{ route('patient.prescriptions.show', $prescription->id) }}">
            Voir les Détails
        </button>
    </div>
</div>