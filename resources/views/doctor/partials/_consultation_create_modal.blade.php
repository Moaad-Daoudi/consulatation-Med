<div class="modal-overlay" id="createConsultationModal">
    <div class="modal modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Nouvelle Consultation</h5>
            <button type="button" class="modal-close">×</button>
        </div>
        <form method="POST" action="{{ route('doctor.consultations.store') }}">
            @csrf
            <div class="modal-body modal-form">
                <div class="form-group"><label for="create_patient_id">Patient *</label>
                    <select name="patient_id" id="create_patient_id" class="form-control" required>
                        <option value="">Sélectionner un patient...</option>
                        @foreach ($patientsForModal as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label for="create_consultation_date">Date et Heure *</label>
                    <input type="datetime-local" name="consultation_date" id="create_consultation_date" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                </div>
                <div class="form-group full-width"><label for="create_reason">Motif *</label>
                    <textarea name="reason_for_visit" id="create_reason" class="form-control" required></textarea>
                </div>
                <div class="form-group full-width"><label for="create_symptoms">Symptômes</label>
                    <textarea name="symptoms" id="create_symptoms" class="form-control"></textarea>
                </div>
                <div class="form-group full-width"><label for="create_diagnosis">Diagnostic</label>
                    <textarea name="diagnosis" id="create_diagnosis" class="form-control"></textarea>
                </div>
                 <div class="form-group full-width"><label for="create_notes">Notes Docteur</label>
                    <textarea name="notes" id="create_notes" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</div>