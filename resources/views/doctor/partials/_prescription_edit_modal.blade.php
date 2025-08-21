<!-- Edit Prescription Modal -->
<div class="modal-overlay" id="editPrescriptionModal">
    <div class="modal modal-content" style="max-width: 900px;">
        <div class="modal-header">
            <h5 class="modal-title">Modifier l'Ordonnance</h5>
            <button type="button" class="modal-close">×</button>
        </div>
        {{-- The action URL will be set by JavaScript --}}
        <form id="editPrescriptionForm" action="" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div id="editPrescriptionErrors" class="alert alert-danger" style="display: none;"></div>
                
                <div class="modal-form">
                    {{-- Patient, Date, Consultation, Notes fields --}}
                    <div class="form-group"><label for="edit_patient_id">Patient *</label><select class="form-control" id="edit_patient_id" name="patient_id" required>@foreach($patientsForModal as $p)<option value="{{$p->id}}">{{$p->name}}</option>@endforeach</select></div>
                    <div class="form-group"><label for="edit_prescription_date">Date *</label><input type="date" class="form-control" id="edit_prescription_date" name="prescription_date" required></div>
                    <div class="form-group full-width"><label for="edit_consultation_id">Consultation Liée</label><select class="form-control" id="edit_consultation_id" name="consultation_id"></select></div>
                    <div class="form-group full-width"><label for="edit_general_notes">Notes Générales</label><textarea class="form-control" id="edit_general_notes" name="general_notes" rows="2"></textarea></div>
                </div>

                <hr class="my-4">
                <h3 class="section-subtitle mb-3">Médicaments</h3>

                {{-- Medication rows will be dynamically inserted here by JS --}}
                <div id="edit-medication-fields-container"></div>

                <button type="button" class="btn btn-outline-primary mb-3" id="add-edit-medication-row-btn">+ Ajouter un autre médicament</button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer les Modifications</button>
            </div>
        </form>
    </div>
</div>