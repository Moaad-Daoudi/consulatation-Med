document.addEventListener('DOMContentLoaded', function() {

    // ===================================================================
    // CONFIGURATION & GLOBAL SETUP
    // ===================================================================
    const config = window.patientConfig || {};
    const routes = config.routes || {};
    const oldInput = config.oldInput || {};
    const csrfToken = config.csrfToken;

    // ===================================================================
    // GENERIC MODAL HANDLING
    // ===================================================================
    document.body.addEventListener('click', function(event) {
        const target = event.target;

        // --- Open any modal via data-modal-target attribute ---
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
        if (target.matches('.modal-close, .modal-close-btn') || target.classList.contains('modal-overlay')) {
            const modal = target.closest('.modal-overlay');
            if (modal) modal.classList.remove('active');
        }

        // --- View Prescription button ---
        const viewPrescButton = target.closest('.view-prescription-btn');
        if (viewPrescButton) {
            handleViewPatientPrescription(viewPrescButton);
        }
    });


    // ===================================================================
    // "CREATE APPOINTMENT" MODAL LOGIC
    // ===================================================================
    
    const doctorSelect = document.getElementById('modal_patient_appt_doctor_select');
    const dateInput = document.getElementById('modal_patient_appt_date_input');
    
    async function fetchPatientModalAvailableSlots() {
        const timeSelect = document.getElementById('modal_patient_appt_time_select');
        const loadingDiv = document.getElementById('modal_patient_slots_loading');
        const errorDiv = document.getElementById('modal_patient_slots_error');

        if (!doctorSelect || !dateInput || !timeSelect || !loadingDiv || !errorDiv) return;

        const doctorId = doctorSelect.value;
        const selectedDate = dateInput.value;

        timeSelect.innerHTML = '<option value="">Chargement...</option>';
        timeSelect.disabled = true;
        errorDiv.style.display = 'none';
        loadingDiv.style.display = 'block';

        if (!doctorId || !selectedDate) {
            timeSelect.innerHTML = '<option value="">Sélectionnez un médecin et une date</option>';
            loadingDiv.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(routes.availableSlots, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ doctor_id: doctorId, date: selectedDate })
            });

            if (!response.ok) throw new Error('Network response was not ok.');
            const data = await response.json();
            
            timeSelect.innerHTML = '<option value="">-- Choisissez une heure --</option>';
            if (data.slots && data.slots.length > 0) {
                data.slots.forEach(slot => {
                    const option = new Option(slot, slot);
                    if (oldInput.appointment_time === slot) {
                        option.selected = true;
                    }
                    timeSelect.appendChild(option);
                });
                timeSelect.disabled = false;
            } else {
                errorDiv.textContent = data.message || 'Aucun créneau disponible.';
                errorDiv.style.display = 'block';
            }

        } catch (error) {
            errorDiv.textContent = 'Erreur lors du chargement des créneaux.';
            errorDiv.style.display = 'block';
            console.error('Slot fetch error:', error);
        } finally {
            loadingDiv.style.display = 'none';
        }
    }

    async function handleViewPatientPrescription(button) {
        const url = button.dataset.url;
        const modal = document.getElementById('viewPrescriptionModal');
        const body = document.getElementById('viewPrescriptionModalBody');
        if (!modal || !body || !url) return;

        body.innerHTML = '<div class="text-center p-5">Chargement...</div>';
        modal.classList.add('active');

        try {
            const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) throw new Error('Network response was not ok.');
            const data = await response.json();
            
            body.innerHTML = buildPatientPrescriptionViewHtml(data);
        } catch (error) {
            body.innerHTML = `<p class="text-danger text-center p-5">Erreur: ${error.message}</p>`;
            console.error('Prescription view fetch error:', error);
        }
    }

    function buildPatientPrescriptionViewHtml(data) {
        const date = new Date(data.prescription_date).toLocaleDateString('fr-FR', {
            year: 'numeric', month: 'long', day: 'numeric'
        });

        let itemsHtml = '<p>Aucun médicament listé.</p>';
        if (data.items && data.items.length > 0) {
            itemsHtml = data.items.map(item => `
                <div class="medication-view-item">
                    <div class="medication-name">${item.medication_name}</div>
                    <div class="medication-instructions">
                        ${item.dosage ? `<span><strong>Posologie:</strong> ${item.dosage}</span>` : ''}
                        ${item.frequency ? `<span><strong>Fréquence:</strong> ${item.frequency}</span>` : ''}
                        ${item.duration ? `<span><strong>Durée:</strong> ${item.duration}</span>` : ''}
                    </div>
                    ${item.notes ? `<div class="medication-notes"><strong>Note:</strong> ${item.notes}</div>` : ''}
                </div>
            `).join('');
        }

        return `
            <div class="prescription-view-header">
                <div class="prescription-detail-item">
                    <span class="detail-label">Docteur</span>
                    <span class="detail-value patient-name">Dr. ${data.doctor.name}</span>
                </div>
                <div class="prescription-detail-item">
                    <span class="detail-label">Date de Prescription</span>
                    <span class="detail-value">${date}</span>
                </div>
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

    // ===================================================================
    // INITIALIZATION
    // ===================================================================
    
    function initializePage() {
        if (doctorSelect) doctorSelect.addEventListener('change', fetchPatientModalAvailableSlots);
        if (dateInput) dateInput.addEventListener('change', fetchPatientModalAvailableSlots);
        
        // --- Handle re-opening a modal after a validation error ---
        if (session.openModalOnLoad) {
        const modalElement = document.getElementById(session.openModalOnLoad);
        if (modalElement) {
            modalElement.classList.add('active');
            if (session.openModalOnLoad === 'patient-create-appointment-modal' && doctorSelect.value && dateInput.value) {
                fetchPatientModalAvailableSlots();
            }
        }
    }
    }

    // Run the initialization logic
    initializePage();

});