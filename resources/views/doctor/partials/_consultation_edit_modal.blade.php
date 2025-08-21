<div class="modal-overlay" id="editConsultationModal">
    <div class="modal modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Modifier la Consultation</h5>
            <button type="button" class="modal-close">×</button>
        </div>
        <form method="POST" action="" id="editConsultationForm">
            @csrf
            @method('PUT')
            <div class="modal-body modal-form">
                <div class="form-group"><label>Patient</label>
                    <input type="text" id="edit_patient_name" class="form-control" readonly>
                </div>
                <div class="form-group"><label for="edit_consultation_date">Date et Heure *</label>
                    <input type="datetime-local" name="consultation_date" id="edit_consultation_date" class="form-control" required>
                </div>
                <div class="form-group full-width"><label for="edit_reason">Motif *</label>
                    <textarea name="reason_for_visit" id="edit_reason" class="form-control" required></textarea>
                </div>
                <div class="form-group full-width"><label for="edit_symptoms">Symptômes</label>
                    <textarea name="symptoms" id="edit_symptoms" class="form-control"></textarea>
                </div>
                <div class="form-group full-width"><label for="edit_diagnosis">Diagnostic</label>
                    <textarea name="diagnosis" id="edit_diagnosis" class="form-control"></textarea>
                </div>
                 <div class="form-group full-width"><label for="edit_notes">Notes Docteur</label>
                    <textarea name="notes" id="edit_notes" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" class="btn btn-primary">Mettre à Jour</button>
            </div>
        </form>
    </div>
</div>