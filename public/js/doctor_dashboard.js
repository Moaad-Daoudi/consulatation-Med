document.addEventListener('DOMContentLoaded', function() {

    // ===================================================================
    // CONFIGURATION & GLOBAL SETUP
    // ===================================================================
    const config = window.doctorDashboardConfig || {};
    const routes = config.routes || {};
    const session = config.session || {};
    const auth = config.auth || {};
    const oldInput = config.oldInput || {};
    const csrfToken = config.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    // ===================================================================
    // HELPER FUNCTIONS
    // ===================================================================
    function decodeHtmlEntities(str) {
        if (typeof str !== 'string') return str;
        const textArea = document.createElement('textarea');
        textArea.innerHTML = str;
        return textArea.value;
    }

    async function apiFetch(url, options = {}) {
        const defaultOptions = {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
        };

        const fetchOptions = { ...defaultOptions, ...options, headers: { ...defaultOptions.headers, ...options.headers } };
        if (options.body) {
            fetchOptions.body = JSON.stringify(options.body);
        }

        const response = await fetch(url, fetchOptions);

        if (!response.ok) {
            const errorData = await response.json().catch(() => ({ message: `HTTP Error: ${response.statusText}` }));
            const error = new Error(errorData.message || 'An unknown network error occurred.');
            error.response = response;
            error.data = errorData;
            throw error;
        }
        return response.json();
    }


    // ===================================================================
    // MAIN EVENT LISTENER (EVENT DELEGATION)
    // ===================================================================
    document.body.addEventListener('click', function(event) {
        const target = event.target;

        const modalTrigger = target.closest('[data-modal-target]');
        if (modalTrigger) {
            event.preventDefault();
            const modalId = modalTrigger.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            if (modal) {
                document.querySelectorAll('.modal-overlay.active').forEach(m => m.classList.remove('active'));
                modal.classList.add('active');
            }
        }

        // --- Close any modal ---
        if (target.matches('.modal-close, .modal-close-btn, .modal-overlay')) {
            const modal = target.closest('.modal-overlay');
            if (modal) modal.classList.remove('active');
        }

        // --- View Patient Dossier button ---
        const dossierButton = target.closest('.view-patient-dossier-btn');
        if (dossierButton) {
            handleViewDossier(dossierButton);
        }

        // --- Add/Remove Prescription Medication Rows ---
        if (target.matches('#add-edit-medication-row-btn')) addMedicationRow('edit');
        const editConsultButton = target.closest('.edit-consultation-btn');
        if (editConsultButton) {
            try {
                console.log("Edit button clicked. Data:", editConsultButton.dataset.details); 
        
                const details = JSON.parse(editConsultButton.dataset.details);
                populateEditModal(details);
                document.getElementById('editConsultationModal')?.classList.add('active');
            } catch (e) {
                console.error("Failed to parse consultation details for editing:", e);
                alert("Could not open the editor. The data for this consultation may be corrupt.");
            }
        }

        const viewConsultButton = target.closest('.view-consultation-btn');
        if (viewConsultButton) {
            const details = JSON.parse(viewConsultButton.dataset.details);
            populateViewModal(details);
            document.getElementById('viewConsultationModal')?.classList.add('active');
        }

        if (target.matches('#add-medication-row-btn')) {
            addMedicationRow('create');
        }
        const removeMedButton = target.closest('.remove-medication-row-btn');
        if (removeMedButton) {
            removeMedicationRow(removeMedButton);
        }

        const viewPrescButton = target.closest('.view-prescription-btn');
        if (viewPrescButton) {
            handleViewPrescription(viewPrescButton);
        }

        const editPrescButton = target.closest('.edit-prescription-btn');
        if (editPrescButton) {
            handleEditPrescription(editPrescButton);
        }
    });


    // ===================================================================
    // FEATURE-SPECIFIC LOGIC
    // ===================================================================

    // --- Patient Dossier Modal ---
    async function handleViewDossier(button) {
        const patientId = button.dataset.patientId;
        const modal = document.getElementById('viewPatientDossierModal');
        const body = document.getElementById('viewPatientDossierModalBody');
        const nameSpan = document.getElementById('dossier_patient_name');

        if (!modal || !body || !nameSpan) return;

        nameSpan.textContent = '...';
        body.innerHTML = '<p class="text-center py-5">Chargement...</p>';
        modal.classList.add('active');

        try {
            const patientData = await apiFetch(`${routes.patientDossierBaseUrl}/${patientId}/dossier`);
            nameSpan.textContent = patientData.name || 'N/A';
            body.innerHTML = buildDossierHtml(patientData);
        } catch (error) {
            body.innerHTML = `<p class="text-danger text-center py-5">Erreur: ${error.message}</p>`;
            console.error('Dossier fetch error:', error);
        }
    }

    function buildDossierHtml(data) {
        const dob = data.patient_profile?.date_of_birth ? new Date(data.patient_profile.date_of_birth).toLocaleDateString('fr-FR') : 'Non renseignée';

        let content = `
            <h4>Informations Générales</h4>
            <p><strong>Nom:</strong> ${data.name || 'N/A'}</p>
            <p><strong>Email:</strong> ${data.email || 'N/A'}</p>
            <p><strong>Date de Naissance:</strong> ${dob}</p>
            <hr class="my-4">
            <h4>Consultations avec Dr. ${auth.userName} (${data.received_consultations?.length || 0})</h4>`;
        
        if (data.received_consultations && data.received_consultations.length > 0) {
            content += '<div class="list-group list-group-flush">';
            data.received_consultations.forEach(consult => {
                const consultDate = new Date(consult.consultation_date).toLocaleString('fr-FR');
                content += `
                    <div class="list-group-item">
                        <h6 class="mb-1">Le ${consultDate}</h6>
                        <p class="mb-1"><strong>Motif:</strong> ${decodeHtmlEntities(consult.reason_for_visit) || 'N/A'}</p>
                        <p class="mb-1"><strong>Diagnostic:</strong> ${decodeHtmlEntities(consult.diagnosis) || 'N/A'}</p>
                    </div>`;
            });
            content += '</div>';
        } else {
            content += `<p>Aucune consultation enregistrée avec ce docteur.</p>`;
        }

        content += `<hr class="my-4"><h4>Ordonnances par Dr. ${auth.userName} (${data.received_prescriptions?.length || 0})</h4>`;

        if (data.received_prescriptions && data.received_prescriptions.length > 0) {
            content += '<div class="list-group list-group-flush">';
            data.received_prescriptions.forEach(presc => {
                const prescDate = new Date(presc.prescription_date).toLocaleDateString('fr-FR');
                let itemsSummary = (presc.items || []).map(item => item.medication_name).join(', ');
                content += `
                    <div class="list-group-item">
                        <h6 class="mb-1">Ordonnance du ${prescDate}</h6>
                        <p class="mb-1"><strong>Médicaments:</strong> ${itemsSummary || 'Aucun'}</p>
                    </div>`;
            });
            content += '</div>';
        } else {
            content += `<p>Aucune ordonnance enregistrée par ce docteur.</p>`;
        }
        return content;
    }


    // --- Create Appointment Modal - Fetch Available Slots ---
    async function fetchDoctorModalAvailableSlots() {
        const dateInput = document.getElementById('modal_doc_create_date_input');
        const timeSelect = document.getElementById('modal_doc_create_time_select');
        const loadingDiv = document.getElementById('modal_doc_slots_loading');
        const errorDiv = document.getElementById('modal_doc_slots_error');
        const doctorId = document.querySelector('#doctor-create-appointment-modal input[name="doctor_id"]').value;

        if (!dateInput || !timeSelect || !loadingDiv || !errorDiv || !doctorId || !dateInput.value) return;

        timeSelect.innerHTML = '<option value="">Chargement...</option>';
        timeSelect.disabled = true;
        errorDiv.style.display = 'none';
        loadingDiv.style.display = 'block';

        try {
            const data = await apiFetch(routes.availableSlots, {
                method: 'POST',
                body: { doctor_id: doctorId, date: dateInput.value }
            });

            timeSelect.innerHTML = '<option value="">-- Choisissez une heure --</option>';
            if (data.slots && data.slots.length > 0) {
                data.slots.forEach(slot => {
                    const option = document.createElement('option');
                    option.value = slot;
                    option.textContent = slot;
                    timeSelect.appendChild(option);
                });
                timeSelect.disabled = false;
            } else {
                errorDiv.textContent = data.message || 'Aucun créneau disponible.';
                errorDiv.style.display = 'block';
            }
        } catch (error) {
            errorDiv.textContent = `Erreur: ${error.message}`;
            errorDiv.style.display = 'block';
            console.error('Slot fetch error:', error);
        } finally {
            loadingDiv.style.display = 'none';
        }
    }

    // Functions for consulations
    function populateEditModal(data) {
        const editForm = document.getElementById('editConsultationForm');
        if (!editForm) return;
        
        editForm.action = `/doctor/consultations/${data.id}`;
        
        const patientNameInput = editForm.querySelector('#edit_patient_name');
        const dateInput = editForm.querySelector('#edit_consultation_date');
        const reasonInput = editForm.querySelector('#edit_reason');
        const symptomsInput = editForm.querySelector('#edit_symptoms');
        const diagnosisInput = editForm.querySelector('#edit_diagnosis');
        const notesInput = editForm.querySelector('#edit_notes');

        if (patientNameInput) patientNameInput.value = data.patient?.name || 'N/A';
        if (dateInput) dateInput.value = data.consultation_date ? data.consultation_date.slice(0, 16) : '';
        if (reasonInput) reasonInput.value = data.reason_for_visit || '';
        if (symptomsInput) symptomsInput.value = data.symptoms || '';
        if (diagnosisInput) diagnosisInput.value = data.diagnosis || '';
        if (notesInput) notesInput.value = data.notes || '';
    }

    function populateViewModal(data) {
        const body = document.getElementById('viewConsultationModalBody');
        if (!body) return;
        
        const date = data.consultation_date ? new Date(data.consultation_date).toLocaleString('fr-FR') : 'N/A';
        
        body.innerHTML = `
            <p><strong>Patient:</strong> ${data.patient?.name || 'N/A'}</p>
            <p><strong>Date:</strong> ${date}</p>
            <hr>
            <p><strong>Motif de la visite:</strong></p>
            <p style="white-space: pre-wrap;">${data.reason_for_visit || 'Non spécifié'}</p>
            <p><strong>Symptômes:</strong></p>
            <p style="white-space: pre-wrap;">${data.symptoms || 'Non spécifié'}</p>
            <p><strong>Diagnostic:</strong></p>
            <p style="white-space: pre-wrap;">${data.diagnosis || 'Non spécifié'}</p>
            <p><strong>Notes du docteur:</strong></p>
            <p style="white-space: pre-wrap;">${data.notes || 'Aucune'}</p>
        `;
    }


    // --- Prescription Form - Add/Remove Dynamic Medication Rows ---
    let medicationIndex = {
        create: document.querySelectorAll('#medication-fields-container .medication-item-row').length,
        edit: document.querySelectorAll('#edit-medication-fields-container .medication-item-row').length
    };

    function addMedicationRow(context = 'create') {
        const containerId = context === 'create' ? 'medication-fields-container' : 'edit-medication-fields-container';
        const container = document.getElementById(containerId);
        const template = document.getElementById('medication-row-template');
        if (!container || !template) return;

        const index = medicationIndex[context];
        const templateContent = template.innerHTML.replace(/__INDEX__/g, index);
        const newRowDiv = document.createElement('div');
        newRowDiv.innerHTML = templateContent;
        container.appendChild(newRowDiv.firstElementChild);
        medicationIndex[context]++;
    }

    function removeMedicationRow(button) {
        const row = button.closest('.medication-item-row');
        const container = row.parentElement;
        if (container.querySelectorAll('.medication-item-row').length > 1) {
            row.remove();
        } else {
            alert("Vous devez avoir au moins un médicament dans l'ordonnance.");
        }
    }


    // --- Prescription Form - Fetch Consultations for Patient ---
    async function populateConsultationsDropdown(patientId, selectElement, selectedId = null) {
        const loadingIndicator = document.getElementById('consultation_loading') || document.getElementById('edit_consultation_loading');
        
        if (!selectElement) return;

        selectElement.innerHTML = '<option value="">-- Chargement... --</option>';
        selectElement.disabled = true;
        if (loadingIndicator) loadingIndicator.style.display = 'inline';

        if (!patientId) {
            selectElement.innerHTML = '<option value="">-- Sélectionnez d\'abord un patient --</option>';
            if (loadingIndicator) loadingIndicator.style.display = 'none';
            return;
        }

        try {
            const consultations = await apiFetch(`/doctor/patients/${patientId}/consultations-for-linking`);
            selectElement.innerHTML = '<option value="">-- Aucune (Optionnel) --</option>';
            
            if (consultations.length > 0) {
                consultations.forEach(consult => {
                    const date = new Date(consult.consultation_date).toLocaleDateString('fr-FR');
                    const reason = consult.reason_for_visit ? consult.reason_for_visit.substring(0, 40) : 'N/A';
                    const option = new Option(`Le ${date} (Motif: ${reason}...)`, consult.id);
                    selectElement.add(option);
                });
            }
            
            if (selectedId) {
                selectElement.value = selectedId;
            }

        } catch (error) {
            console.error("Failed to fetch consultations:", error);
            selectElement.innerHTML = '<option value="">-- Erreur de chargement --</option>';
        } finally {
            selectElement.disabled = false;
            if (loadingIndicator) loadingIndicator.style.display = 'none';
        }
    }

    async function handleViewPrescription(button) {
        const url = button.dataset.url;
        const modal = document.getElementById('viewPrescriptionModal');
        const body = document.getElementById('viewPrescriptionModalBody');
        if (!modal || !body || !url) return;

        body.innerHTML = '<div class="text-center p-5">Chargement...</div>';
        modal.classList.add('active');

        try {
            const data = await apiFetch(url);
            body.innerHTML = buildPrescriptionViewHtml(data);
        } catch (error) {
            body.innerHTML = `<p class="text-danger text-center p-5">Erreur: ${error.message}</p>`;
            console.error('Prescription view fetch error:', error);
        }
    }

    function buildPrescriptionViewHtml(data) {
        const date = new Date(data.prescription_date).toLocaleDateString('fr-FR', {
            year: 'numeric', month: 'long', day: 'numeric'
        });

        let consultationHtml = `
            <div class="prescription-detail-item">
                <span class="detail-label">Consultation Liée</span>
                <span class="detail-value">Aucune</span>
            </div>`;
        
        if (data.consultation) {
            const consultDate = new Date(data.consultation.consultation_date).toLocaleDateString('fr-FR');
            consultationHtml = `
                <div class="prescription-detail-item">
                    <span class="detail-label">Consultation Liée</span>
                    <span class="detail-value">
                        Le ${consultDate} (Motif: ${data.consultation.reason_for_visit})
                    </span>
                </div>`;
        }

        let itemsHtml = '<p>Aucun médicament listé.</p>';
        if (data.items && data.items.length > 0) {
            itemsHtml = data.items.map(item => `
                <div class="medication-view-item">
                    <div class="medication-name">${decodeHtmlEntities(item.medication_name)}</div>
                    <div class="medication-instructions">
                        ${item.dosage ? `<span><strong>Posologie:</strong> ${decodeHtmlEntities(item.dosage)}</span>` : ''}
                        ${item.frequency ? `<span><strong>Fréquence:</strong> ${decodeHtmlEntities(item.frequency)}</span>` : ''}
                        ${item.duration ? `<span><strong>Durée:</strong> ${decodeHtmlEntities(item.duration)}</span>` : ''}
                    </div>
                    ${item.notes ? `<div class="medication-notes"><strong>Note:</strong> ${decodeHtmlEntities(item.notes)}</div>` : ''}
                </div>
            `).join('');
        }

        return `
            <div class="prescription-view-header">
                <div class="prescription-detail-item">
                    <span class="detail-label">Patient</span>
                    <span class="detail-value patient-name">${data.patient.name}</span>
                </div>
                <div class="prescription-detail-item">
                    <span class="detail-label">Date de Prescription</span>
                    <span class="detail-value">${date}</span>
                </div>
                ${consultationHtml}
            </div>

            <div class="prescription-view-section">
                <h6 class="section-subtitle">Notes Générales</h6>
                <p class="general-notes">${data.general_notes || 'Aucune note générale.'}</p>
            </div>
            
            <div class="prescription-view-section">
                <h6 class="section-subtitle">Médicaments</h6>
                <div class="medications-list-view">
                    ${itemsHtml}
                </div>
            </div>
        `;
    }

    async function handleEditPrescription(button) {
        const url = button.dataset.editUrl;
        const modal = document.getElementById('editPrescriptionModal');
        if (!modal || !url) return;

        const container = document.getElementById('edit-medication-fields-container');
        container.innerHTML = '<p>Chargement des données...</p>';
        modal.classList.add('active');

        try {
            const data = await apiFetch(url);
            await populateEditForm(data);
        } catch (error) {
            container.innerHTML = `<p class="text-danger">Erreur: ${error.message}</p>`;
            console.error('Prescription edit fetch error:', error);
        }
    }

    async function populateEditForm(data) {
        const form = document.getElementById('editPrescriptionForm');
        const container = document.getElementById('edit-medication-fields-container');
        const template = document.getElementById('medication-row-template');
        if (!form || !container || !template) return;

        form.action = `/doctor/prescriptions/${data.id}`;
        form.querySelector('#edit_patient_id').value = data.patient_id;
        form.querySelector('#edit_prescription_date').value = data.prescription_date;
        form.querySelector('#edit_general_notes').value = data.general_notes || '';

        container.innerHTML = '';
        medicationIndex.edit = 0;

        if (data.items && data.items.length > 0) {
            data.items.forEach(item => {
                const index = medicationIndex.edit;
                const content = template.innerHTML.replace(/__INDEX__/g, index);
                const newRow = document.createElement('div');
                newRow.innerHTML = content;
                newRow.querySelector(`input[name="medications[${index}][medication_name]"]`).value = item.medication_name;
                newRow.querySelector(`input[name="medications[${index}][dosage]"]`).value = item.dosage || '';
                newRow.querySelector(`input[name="medications[${index}][frequency]"]`).value = item.frequency || '';
                newRow.querySelector(`input[name="medications[${index}][duration]"]`).value = item.duration || '';
                newRow.querySelector(`input[name="medications[${index}][notes]"]`).value = item.notes || '';
                const hiddenIdInput = document.createElement('input');
                hiddenIdInput.type = 'hidden';
                hiddenIdInput.name = `medications[${index}][id]`;
                hiddenIdInput.value = item.id;
                newRow.querySelector('.medication-item-row').appendChild(hiddenIdInput);
                container.appendChild(newRow.firstElementChild);
                medicationIndex.edit++;
            });
        }

        const consultSelect = form.querySelector('#edit_consultation_id');
        await populateConsultationsDropdown(data.patient_id, consultSelect, data.consultation_id);
    }

    // ===================================================================
    // INITIALIZATION ON PAGE LOAD
    // ===================================================================

    function initializePage() {
        document.querySelectorAll('.alert-success').forEach(alert => {
            setTimeout(() => alert.style.opacity = '0', 5000);
        });

        const dateInput = document.getElementById('modal_doc_create_date_input');
        if (dateInput) {
            dateInput.addEventListener('change', fetchDoctorModalAvailableSlots);
        }
        
        const createPatientSelect = document.getElementById('prescription_patient_id');
        if (createPatientSelect) {
            createPatientSelect.addEventListener('change', () => {
                const consultSelect = document.getElementById('prescription_consultation_id');
                populateConsultationsDropdown(createPatientSelect.value, consultSelect);
            });
            if (createPatientSelect.value) { 
                const consultSelect = document.getElementById('prescription_consultation_id');
                populateConsultationsDropdown(createPatientSelect.value, consultSelect, oldInput.consultationId);
            }
        }

        const editPatientSelect = document.getElementById('edit_patient_id');
        if (editPatientSelect) {
            editPatientSelect.addEventListener('change', () => {
                const consultSelect = document.getElementById('edit_consultation_id');
                populateConsultationsDropdown(editPatientSelect.value, consultSelect);
            });
        }

        // --- Open modal if session indicates an error ---
        if (session.openModalOnLoad) {
            const modalEl = document.getElementById(session.openModalOnLoad);
            if (modalEl) modalEl.classList.add('active');
        }

        // --- Trigger initial fetch if appointment form is re-populated ---
        if (session.openModalOnLoad === 'doctor-create-appointment-modal' && dateInput?.value) {
            fetchDoctorModalAvailableSlots();
        }
    }
    // Run all initialization logic
    initializePage();

});