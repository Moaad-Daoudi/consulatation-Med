<!-- View Patient Dossier Modal -->
<div class="modal-overlay" id="viewPatientDossierModal">
    <div class="modal modal-content" style="max-width: 900px;"> {{-- Make it wider for more content --}}
        <div class="modal-header">
            <h5 class="modal-title">Dossier Patient: <span id="dossier_patient_name_header"></span></h5>
            <button type="button" class="modal-close" data-modal-dismiss="viewPatientDossierModal">×</button>
        </div>
        <div class="modal-body" id="viewPatientDossierModalBody">
            {{-- Content will be loaded by JavaScript --}}
            <p class="text-center py-5">Chargement du dossier patient...</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary modal-close-btn">Fermer</button>
        </div>
    </div>
</div>