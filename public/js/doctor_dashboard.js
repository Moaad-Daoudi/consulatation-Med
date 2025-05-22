document.addEventListener('DOMContentLoaded', function() {
    // --- Configuration from Blade ---
    const config = window.doctorDashboardConfig || {};
    const routes = config.routes || {};
    const session = config.session || {};
    const auth = config.auth || {};
    const oldInput = config.oldInput || {};
    const csrfToken = config.csrfToken || document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // --- function to decode HTML entities ---
    function decodeHtmlEntities(str) {
        if (typeof str !== 'string') {
            console.warn('decodeHtmlEntities received non-string input:', str);
            return str;
        }
        const textArea = document.createElement('textarea');
        textArea.innerHTML = str;
        return textArea.value;
    }

    // --- SPA Navigation Logic ---
    const menuLinks = document.querySelectorAll('.sidebar-menu .menu-link');
    const contentSections = document.querySelectorAll('.content-wrapper .content-section');
    const pageTitleElement = document.getElementById('dynamicPageTitle');

    function activateSection(sectionId, fromLocalStorage = false) {
        let sectionFound = false;
        contentSections.forEach(section => {
            if (section.id === sectionId) {
                section.classList.add('active');
                sectionFound = true;
            } else {
                section.classList.remove('active');
            }
        });

        if (sectionFound) {
             if (!fromLocalStorage || !localStorage.getItem('activeDoctorSection')) {
                localStorage.setItem('activeDoctorSection', sectionId);
             }
        } else if (contentSections.length > 0 && !document.querySelector('.content-section.active')) {
            contentSections[0].classList.add('active');
            sectionId = contentSections[0].id;
            localStorage.setItem('activeDoctorSection', sectionId);
        }
        return sectionId;
    }

    menuLinks.forEach(link => {
        const isLogoutLink = link.getAttribute('href') === routes.logout || (link.onclick && link.onclick.toString().includes('logout-form'));
        if (isLogoutLink) return;

        link.addEventListener('click', function(e) {
            e.preventDefault();
            menuLinks.forEach(item => item.classList.remove('active'));
            this.classList.add('active');
            const targetSectionId = this.getAttribute('data-section');
            activateSection(targetSectionId);
            if (pageTitleElement && this.querySelector('span')) {
                pageTitleElement.textContent = this.querySelector('span').textContent;
            }
        });
    });

    let activeSectionFromPHP = session.activeSectionOnLoad;
    let savedSection = localStorage.getItem('activeDoctorSection');
    let initialSectionId = 'dashboard';

    if (activeSectionFromPHP) {
        initialSectionId = activeSectionFromPHP;
        localStorage.setItem('activeDoctorSection', activeSectionFromPHP);
    } else if (savedSection && document.getElementById(savedSection)) {
        initialSectionId = savedSection;
    }


    const finalActiveSectionId = activateSection(initialSectionId, true);
    const activeLink = document.querySelector(`.sidebar-menu .menu-link[data-section="${finalActiveSectionId}"]`);

    if (activeLink) {
        menuLinks.forEach(item => item.classList.remove('active'));
        activeLink.classList.add('active');
        if (pageTitleElement && activeLink.querySelector('span')) {
            pageTitleElement.textContent = activeLink.querySelector('span').textContent;
        }
    } else if (menuLinks.length > 0) {
        menuLinks.forEach(item => item.classList.remove('active'));
        menuLinks[0].classList.add('active');
         if (pageTitleElement && menuLinks[0].querySelector('span')) {
            pageTitleElement.textContent = menuLinks[0].querySelector('span').textContent;
        }
        if(contentSections.length > 0 && menuLinks[0].getAttribute('data-section')) {
            activateSection(menuLinks[0].getAttribute('data-section'));
        }
    }


    // --- Alert Auto-Dismissal ---
    document.querySelectorAll('.alert-success').forEach(successAlert => {
        setTimeout(() => {
            successAlert.style.transition = 'opacity 0.5s ease';
            successAlert.style.opacity = '0';
            setTimeout(() => { successAlert.style.display = 'none'; }, 500);
        }, 7000);
    });
    document.querySelectorAll('.alert-danger').forEach(errorAlert => {
        const parentModal = errorAlert.closest('.modal-overlay.active');
        const isGeneralErrorNotSpecificToModal = !errorAlert.closest('.modal-body');

        if (!parentModal && !isGeneralErrorNotSpecificToModal && !session.openModalOnLoad) {
             setTimeout(() => {
                errorAlert.style.transition = 'opacity 0.5s ease';
                errorAlert.style.opacity = '0';
                setTimeout(() => { errorAlert.style.display = 'none'; }, 500);
            }, 15000);
        }
    });


    // --- General Modal Toggle Logic ---
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            if (modal) {
                document.querySelectorAll('.modal-overlay.active').forEach(activeModal => {
                    if (activeModal.id !== modalId) activeModal.classList.remove('active');
                });
                modal.classList.add('active');
                if (modalId === 'doctor-create-appointment-modal') {
                    const dateInput = document.getElementById('modal_doc_create_date_input');
                    if (dateInput && dateInput.value) {
                        fetchDoctorModalAvailableSlots();
                    }
                }
            }
        });
    });
    document.querySelectorAll('.modal-close, .modal-close-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation();
            const modal = button.closest('.modal-overlay');
            if (modal) modal.classList.remove('active');
        });
    });
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('active'); });
    });


    // --- START: JS for Patient Dossier Modal ---
    document.querySelectorAll('.view-patient-dossier-btn').forEach(button => {
        button.addEventListener('click', function() {
            const patientId = this.dataset.patientId;
            const dossierModal = document.getElementById('viewPatientDossierModal');
            const dossierModalBody = document.getElementById('viewPatientDossierModalBody');
            const dossierPatientNameSpan = document.getElementById('dossier_patient_name');

            if (!dossierModal || !dossierModalBody || !dossierPatientNameSpan) {
                console.error('Dossier modal elements not found!');
                return;
            }

            dossierPatientNameSpan.textContent = '';
            dossierModalBody.innerHTML = '<p class="text-center py-5">Chargement du dossier patient...</p>';
            dossierModal.classList.add('active');

            const fetchUrl = `${routes.patientDossierBaseUrl}/${patientId}/dossier`;

            fetch(fetchUrl, { headers: { 'Accept': 'application/json' } })
                .then(response => {
                    if (!response.ok) { throw new Error(`HTTP error ${response.status}`); }
                    return response.json();
                })
                .then(patientData => {
                    dossierPatientNameSpan.textContent = patientData.name || 'N/A';
                    let contentHtml = `
                        <h4>Informations Générales :</h4><br>
                        <p><strong>Nom:</strong> ${patientData.name || 'N/A'}</p>
                        <p><strong>Email:</strong> ${patientData.email || 'N/A'}</p>
                        ${patientData.phone_number ? `<p><strong>Téléphone:</strong> ${patientData.phone_number}</p>` : ''}
                        ${patientData.date_of_birth ? `<p><strong>Date de Naissance:</strong> ${new Date(patientData.date_of_birth).toLocaleDateString('fr-FR')}</p>` : ''}
                        ${patientData.address ? `<p><strong>Adresse:</strong> ${patientData.address}</p>` : ''}
                        <hr class="my-4">
                    `;

                    contentHtml += `<h4>Consultations avec Dr. ${auth.userName} (${patientData.patient_consultations ? patientData.patient_consultations.length : 0}) :</h4><br>`;
                    if (patientData.patient_consultations && patientData.patient_consultations.length > 0) {
                        contentHtml += '<div class="list-group list-group-flush">';
                        patientData.patient_consultations.forEach(consult => {
                            const consultDate = new Date(consult.consultation_date_time || consult.consultation_date).toLocaleString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour:'2-digit', minute: '2-digit' });
                            contentHtml += `
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Consultation du ${consultDate}</h6>
                                    </div>
                                    <p class="mb-1"><strong>Motif:</strong> ${decodeHtmlEntities(consult.reason_for_visit) || 'N/A'}</p>
                                    <p class="mb-1"><strong>Symptômes:</strong> ${decodeHtmlEntities(consult.symptoms) ? decodeHtmlEntities(consult.symptoms).substring(0,100) : 'N/A'}</p>
                                    <p class="mb-1"><strong>Diagnostic:</strong> ${decodeHtmlEntities(consult.diagnosis) || 'N/A'}</p>
                                </div>
                                <br>
                            `;
                        });
                        contentHtml += '</div>';
                    } else {
                        contentHtml += `<p>Aucune consultation enregistrée avec Dr. ${auth.userName}.</p>`;
                    }
                    contentHtml += '<hr class="my-4">';

                    contentHtml += `<h4>Ordonnances par Dr. ${auth.userName} (${patientData.received_prescriptions ? patientData.received_prescriptions.length : 0}) :</h4><br>`;
                    if (patientData.received_prescriptions && patientData.received_prescriptions.length > 0) {
                        contentHtml += '<div class="list-group list-group-flush">';
                        patientData.received_prescriptions.forEach(presc => {
                            const prescDate = new Date(presc.prescription_date).toLocaleDateString('fr-FR');
                            let itemsSummary = (presc.items || []).map(item => item.medication_name).slice(0, 2).join(', ');
                            if ((presc.items || []).length > 2) itemsSummary += '...';

                            contentHtml += `
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">Ordonnance du ${prescDate}</h6>     
                                        <small>${presc.items_count || (presc.items || []).length} médicament(s)</small>
                                    </div>
                                    <p class="mb-1"><strong>Médicaments:</strong> ${itemsSummary || 'N/A'}</p>
                                    <p class="mb-1"><strong>Notes:</strong> ${decodeHtmlEntities(presc.general_notes) || 'Aucune'}</p>
                                </div>
                                <br>
                            `;
                        });
                        contentHtml += '</div>';
                    } else {
                        contentHtml += `<p>Aucune ordonnance enregistrée par Dr. ${auth.userName}.</p>`;
                    }
                    dossierModalBody.innerHTML = contentHtml;
                })
                .catch(error => {
                    console.error('Error fetching patient dossier:', error);
                    dossierModalBody.innerHTML = '<p class="text-danger text-center py-5">Erreur lors du chargement du dossier patient.</p>';
                });
        });
    });


    // --- for Doctor Create Appointment Modal ---
    const doctorCreateApptModalEl = document.getElementById('doctor-create-appointment-modal');
    let doctorSelectInModal, doctorHiddenInputInModal, dateInputDocModal, timeSelectDocModal, slotsLoadingDocModal, slotsErrorDocModal;

    if (doctorCreateApptModalEl) {
        doctorSelectInModal = document.getElementById('modal_doc_assign_doctor_select');
        doctorHiddenInputInModal = doctorCreateApptModalEl.querySelector('input[name="doctor_id"]');
        dateInputDocModal = document.getElementById('modal_doc_create_date_input');
        timeSelectDocModal = document.getElementById('modal_doc_create_time_select');
        slotsLoadingDocModal = document.getElementById('modal_doc_slots_loading');
        slotsErrorDocModal = document.getElementById('modal_doc_slots_error');

        function fetchDoctorModalAvailableSlots() {
            const doctorId = (doctorSelectInModal && doctorSelectInModal.offsetParent !== null && doctorSelectInModal.value) ? doctorSelectInModal.value : (doctorHiddenInputInModal ? doctorHiddenInputInModal.value : null);
            const selectedDate = dateInputDocModal.value;
            const previouslySelectedTime = timeSelectDocModal.dataset.oldTime || oldInput.appointmentTime;

            timeSelectDocModal.innerHTML = '<option value="">Chargement...</option>';
            timeSelectDocModal.disabled = true;
            if(slotsErrorDocModal) slotsErrorDocModal.style.display = 'none';

            if (!doctorId || !selectedDate) {
                timeSelectDocModal.innerHTML = '<option value="">Sélectionnez un médecin et une date</option>'; return;
            }
            if (new Date(selectedDate) < new Date(new Date().toISOString().split('T')[0])) {
                 timeSelectDocModal.innerHTML = '<option value="">Date invalide</option>';
                 if(slotsErrorDocModal) { slotsErrorDocModal.textContent = 'La date ne peut pas être dans le passé.'; slotsErrorDocModal.style.display = 'block';}
                 return;
            }
            if(slotsLoadingDocModal) slotsLoadingDocModal.style.display = 'block';

            fetch(routes.availableSlots, {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json'},
                body: JSON.stringify({ doctor_id: doctorId, date: selectedDate })
            })
            .then(response => response.ok ? response.json() : response.json().then(err => { console.error("Server error:", err); throw err; }))
            .then(data => {
                if(slotsLoadingDocModal) slotsLoadingDocModal.style.display = 'none';
                timeSelectDocModal.innerHTML = '<option value="">-- Choisissez une heure --</option>';
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(slot => {
                        const option = document.createElement('option'); option.value = slot; option.textContent = slot;
                        if (slot === previouslySelectedTime) option.selected = true;
                        timeSelectDocModal.appendChild(option);
                    });
                    timeSelectDocModal.disabled = false;
                } else {
                    if (previouslySelectedTime) {
                         const option = document.createElement('option');
                         option.value = previouslySelectedTime; option.textContent = previouslySelectedTime + " (Non disponible)";
                         option.disabled = true; option.selected = true;
                         timeSelectDocModal.appendChild(option);
                    } else {
                        timeSelectDocModal.innerHTML = '<option value="">Aucun créneau</option>';
                    }
                    if(slotsErrorDocModal){ slotsErrorDocModal.textContent = data.message || 'Aucun créneau disponible.'; slotsErrorDocModal.style.display = 'block';}
                }
            })
            .catch(error => {
                if(slotsLoadingDocModal) slotsLoadingDocModal.style.display = 'none';
                timeSelectDocModal.innerHTML = '<option value="">Erreur</option>';
                console.error('Error fetching slots for doctor modal:', error);
                let errorMsg = 'Erreur de chargement des créneaux.';
                if(error && error.errors) { errorMsg = Object.values(error.errors).flat().join(' '); }
                else if (error && error.message) { errorMsg = error.message; }
                if(slotsErrorDocModal){ slotsErrorDocModal.textContent = `Erreur: ${errorMsg}`; slotsErrorDocModal.style.display = 'block';}
            });
        }
        if(doctorSelectInModal) doctorSelectInModal.addEventListener('change', fetchDoctorModalAvailableSlots);
        if(dateInputDocModal) dateInputDocModal.addEventListener('change', fetchDoctorModalAvailableSlots);
        if(timeSelectDocModal && oldInput.appointmentTime) timeSelectDocModal.dataset.oldTime = oldInput.appointmentTime;
    }


    const modalToOpenFromSession = session.openModalOnLoad;
    const consultationIdForError = session.consultationIdForErrorBag;
    const prescriptionIdForErrorBag = session.prescriptionIdForErrorBag;

    if (modalToOpenFromSession) {
        const modalElement = document.getElementById(modalToOpenFromSession);

        if (modalToOpenFromSession === 'doctor-create-appointment-modal' && dateInputDocModal && dateInputDocModal.value) {
            fetchDoctorModalAvailableSlots();
        }
        if (modalToOpenFromSession === 'createConsultationModal') {
            const createConsultPatientSelect = document.getElementById('modal_create_consult_patient_id');
        }
        if (modalToOpenFromSession === 'createPrescriptionModal') {
            const createPrescPatientSelect = document.getElementById('prescription_patient_id');
            if(createPrescPatientSelect && createPrescPatientSelect.value){
                createPrescPatientSelect.dispatchEvent(new Event('change'));
            }
        }
    }


    // --- for Ordonnances (Prescriptions) Section (CREATE FORM) ---
    const addMedicationBtn = document.getElementById('add-medication-row-btn');
    const medicationFieldsContainer = document.getElementById('medication-fields-container');
    const medicationRowTemplateEl = document.getElementById('medication-row-template');
    let createMedicationIndex = medicationFieldsContainer ? medicationFieldsContainer.querySelectorAll('.medication-item-row').length : 0;

    if (addMedicationBtn && medicationFieldsContainer && medicationRowTemplateEl) {
        addMedicationBtn.addEventListener('click', function() {
            const templateContent = medicationRowTemplateEl.innerHTML.replace(/__INDEX__/g, createMedicationIndex);
            const newRowDiv = document.createElement('div');
            newRowDiv.innerHTML = templateContent;
            const newRowElement = newRowDiv.firstElementChild;

            newRowElement.querySelectorAll('[id*="__INDEX__"]').forEach(el => {
                el.id = el.id.replace(/__INDEX__/g, createMedicationIndex);
            });
            newRowElement.querySelectorAll('label[for*="__INDEX__"]').forEach(label => {
                label.htmlFor = label.htmlFor.replace(/__INDEX__/g, createMedicationIndex);
            });

            medicationFieldsContainer.appendChild(newRowElement);
            createMedicationIndex++;
        });

        medicationFieldsContainer.addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('remove-medication-row-btn')) {
                if (medicationFieldsContainer.querySelectorAll('.medication-item-row').length > 1) {
                    e.target.closest('.medication-item-row').remove();
                } else {
                    alert("Vous ne pouvez pas retirer le dernier médicament. Ajoutez-en un autre d'abord si vous souhaitez modifier celui-ci.");
                }
            }
        });
    }


    // --- for Consultation Modals (Create, Edit, View) ---
    const editConsultationModalEl = document.getElementById('editConsultationModal');
    const editConsultationFormEl = document.getElementById('editConsultationForm');
    const viewConsultationDetailModalEl = document.getElementById('viewConsultationDetailModal');

    document.querySelectorAll('.edit-consultation-btn').forEach(button => {
        button.addEventListener('click', function() {
            const consultationId = this.dataset.id;
            if(editConsultationFormEl) editConsultationFormEl.action = `${routes.consultationsBaseUrl}/${consultationId}`;

            const patientNameDisplay = document.getElementById('edit_consult_patient_name_display');
            const dateTimeInput = document.getElementById('edit_consult_consultation_date_time');
            const reasonInput = document.getElementById('edit_consult_reason_for_visit');
            const symptomsInput = document.getElementById('edit_consult_symptoms');
            const notesInput = document.getElementById('edit_consult_notes');
            const diagnosisInput = document.getElementById('edit_consult_diagnosis');
            const errorBagIdInput = document.getElementById('edit_consultation_id_for_error_bag');

            if(patientNameDisplay) patientNameDisplay.value = this.dataset.patientName || '';
            if(dateTimeInput) dateTimeInput.value = this.dataset.consultationDate || '';
            if(reasonInput) reasonInput.value = decodeHtmlEntities(this.dataset.reasonForVisit || '');
            if(symptomsInput) symptomsInput.value = decodeHtmlEntities(this.dataset.symptoms || '');
            if(notesInput) notesInput.value = decodeHtmlEntities(this.dataset.notes || '');
            if(diagnosisInput) diagnosisInput.value = decodeHtmlEntities(this.dataset.diagnosis || '');
            if(errorBagIdInput) errorBagIdInput.value = consultationId;

            const errorDivGeneral = document.getElementById('editConsultationErrorsGeneral');
            if(errorDivGeneral) {
                errorDivGeneral.style.display = 'none';
                if(errorDivGeneral.querySelector('ul')) errorDivGeneral.querySelector('ul').innerHTML = '';
            }
            if(editConsultationModalEl) editConsultationModalEl.classList.add('active');
        });
    });

    document.querySelectorAll('.view-consultation-details-btn').forEach(button => {
        button.addEventListener('click', function() {
            let rawDetailsString, decodedDetailsString, details;
            try {
                rawDetailsString = this.getAttribute('data-consultation-details');
                decodedDetailsString = decodeHtmlEntities(rawDetailsString);
                details = JSON.parse(decodedDetailsString);

                const patientNameEl = document.getElementById('view_consult_patient_name');
                const dateEl = document.getElementById('view_consult_date');
                const appointmentInfoEl = document.getElementById('view_consult_appointment_info');
                const reasonEl = document.getElementById('view_consult_reason');
                const symptomsEl = document.getElementById('view_consult_symptoms');
                const notesEl = document.getElementById('view_consult_notes');
                const diagnosisEl = document.getElementById('view_consult_diagnosis');

                if(patientNameEl) patientNameEl.textContent = details.patient ? details.patient.name : 'N/A';
                if(dateEl) dateEl.textContent = details.consultation_date_time || details.consultation_date ? new Date(details.consultation_date_time || details.consultation_date).toLocaleString('fr-FR') : 'N/A';

                let appointmentInfoText = 'Aucun';
                if (details.appointment) {
                    appointmentInfoText = `RDV du ${new Date(details.appointment.appointment_datetime).toLocaleString('fr-FR')}`;
                }
                if(appointmentInfoEl) appointmentInfoEl.textContent = appointmentInfoText;

                if(reasonEl) reasonEl.textContent = decodeHtmlEntities(details.reason_for_visit) || 'N/A';
                if(symptomsEl) symptomsEl.textContent = decodeHtmlEntities(details.symptoms) || 'N/A';
                if(notesEl) notesEl.textContent = decodeHtmlEntities(details.notes) || 'N/A';
                if(diagnosisEl) diagnosisEl.textContent = decodeHtmlEntities(details.diagnosis) || 'N/A';

                if(viewConsultationDetailModalEl) viewConsultationDetailModalEl.classList.add('active');
            } catch (e) {
                console.error("Error parsing consultation details for view:", e, rawDetailsString);
                alert("Erreur lors de l'affichage des détails de la consultation.");
            }
        });
    });


    // --- for Prescription Form - Link to Consultation ---
    function fetchAndPopulatePatientConsultations(patientId, targetSelectElement, loadingElement, preSelectedConsultationId = null) {
        if (!targetSelectElement || !loadingElement) {
            console.warn("Consultation select/loading element missing for fetchAndPopulate");
            return;
        }
        targetSelectElement.innerHTML = '<option value="">-- Chargement... --</option>';
        targetSelectElement.disabled = true;
        loadingElement.style.display = 'inline';

        if (!patientId) {
            loadingElement.style.display = 'none';
            targetSelectElement.innerHTML = '<option value="">-- Sélectionnez d\'abord un patient --</option>';
            return;
        }
        const fetchUrl = `${routes.consultationsForLinkingBaseUrl}/${patientId}/consultations-for-linking`;
        fetch(fetchUrl, { headers: { 'Accept': 'application/json' } })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.json();
            })
            .then(consultations => {
                loadingElement.style.display = 'none';
                targetSelectElement.innerHTML = '<option value="">-- Aucune Consultation --</option>';
                if (consultations && consultations.length > 0) {
                    consultations.forEach(consult => {
                        const option = document.createElement('option');
                        option.value = consult.id;
                        const consultDate = new Date(consult.consultation_date_time || consult.consultation_date).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                        option.textContent = `Consultation du ${consultDate} (Motif: ${decodeHtmlEntities(consult.reason_for_visit) ? decodeHtmlEntities(consult.reason_for_visit).substring(0,40) : 'N/A'})`;
                        if (preSelectedConsultationId && preSelectedConsultationId == consult.id) {
                            option.selected = true;
                        }
                        targetSelectElement.appendChild(option);
                    });
                }
                targetSelectElement.disabled = false;
            })
            .catch(error => {
                console.error("Error fetching consultations for linking:", error);
                loadingElement.style.display = 'none';
                targetSelectElement.innerHTML = '<option value="">-- Erreur de chargement --</option>';
            });
    }

    // For CREATE Prescription form
    const createPrescriptionPatientSelect = document.getElementById('prescription_patient_id');
    const createPrescriptionConsultationSelect = document.getElementById('prescription_consultation_id');
    const createPrescriptionConsultationLoading = document.getElementById('prescription_consultation_loading');

    if (createPrescriptionPatientSelect && createPrescriptionConsultationSelect && createPrescriptionConsultationLoading) {
        createPrescriptionPatientSelect.addEventListener('change', function() {
            fetchAndPopulatePatientConsultations(this.value, createPrescriptionConsultationSelect, createPrescriptionConsultationLoading, oldInput.consultationId);
        });
        if (createPrescriptionPatientSelect.value && (oldInput.patientId === createPrescriptionPatientSelect.value || !oldInput.patientId) ) {
            fetchAndPopulatePatientConsultations(createPrescriptionPatientSelect.value, createPrescriptionConsultationSelect, createPrescriptionConsultationLoading, oldInput.consultationId);
        }
    }

    // For EDIT Prescription form (inside modal)
    const editPrescriptionModalPatientSelect = document.getElementById('edit_prescription_patient_id');
    const editPrescriptionModalConsultationSelect = document.getElementById('edit_prescription_consultation_id');
    const editPrescriptionModalConsultationLoading = document.getElementById('edit_prescription_consultation_loading');

    if (editPrescriptionModalPatientSelect && editPrescriptionModalConsultationSelect && editPrescriptionModalConsultationLoading) {
        editPrescriptionModalPatientSelect.addEventListener('change', function() {
            const currentPrescription = session.editingPrescription || {};
            const preSelectedConsultId = oldInput.consultationId || currentPrescription.consultation_id || null;
            fetchAndPopulatePatientConsultations(this.value, editPrescriptionModalConsultationSelect, editPrescriptionModalConsultationLoading, preSelectedConsultId);
        });
    }


    // --- for "Edit Prescription Modal" ---
    document.querySelectorAll('.edit-prescription-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const editUrl = this.dataset.editUrl;
            if (editUrl) {
                window.location.href = editUrl;
            } else {
                console.error('CRITICAL ERROR: data-edit-url is missing on the edit prescription button!');
                alert('Erreur: URL de modification manquante.');
            }
        });
    });

    const editingPrescriptionDataFromSession = session.editingPrescription;
    const prescriptionOpenModalOnLoad = session.openModalOnLoad;
    const prescriptionIdForErrorFromSession = session.prescriptionIdForErrorBag;
    const editPrescriptionModalElement = document.getElementById('editPrescriptionModal');

    if(editPrescriptionModalElement) {
        const shouldProcessEditModal =
            (editingPrescriptionDataFromSession && prescriptionOpenModalOnLoad === 'editPrescriptionModal') ||
            (prescriptionOpenModalOnLoad === 'editPrescriptionModal' && prescriptionIdForErrorFromSession);

        if (shouldProcessEditModal) {
            const form = editPrescriptionModalElement.querySelector('form#form-edit-prescription');
            if (form) {
                const targetPrescriptionId = (editingPrescriptionDataFromSession && editingPrescriptionDataFromSession.id) ? editingPrescriptionDataFromSession.id : prescriptionIdForErrorFromSession;
                if (targetPrescriptionId) {
                    form.action = `${routes.prescriptionsBaseUrl}/${targetPrescriptionId}`;
                }

                const patientSelectField = form.querySelector('#edit_prescription_patient_id');
                if (editingPrescriptionDataFromSession && (!prescriptionIdForErrorFromSession || prescriptionIdForErrorFromSession != editingPrescriptionDataFromSession.id)) {
                    const editPatientId = editingPrescriptionDataFromSession.patient_id;
                    if (editPatientId && editPrescriptionModalConsultationSelect && editPrescriptionModalConsultationLoading) {
                        fetchAndPopulatePatientConsultations(
                            editPatientId,
                            editPrescriptionModalConsultationSelect,
                            editPrescriptionModalConsultationLoading,
                            editingPrescriptionDataFromSession.consultation_id
                        );
                    }
                }
                else if (prescriptionIdForErrorFromSession) {
                    const oldPatientIdForEdit = patientSelectField ? patientSelectField.value : (oldInput.patientId || (editingPrescriptionDataFromSession ? editingPrescriptionDataFromSession.patient_id : ''));
                    const oldConsultationIdForEdit = oldInput.consultationId;
                    if (oldPatientIdForEdit && editPrescriptionModalConsultationSelect && editPrescriptionModalConsultationLoading) {
                        fetchAndPopulatePatientConsultations(
                            oldPatientIdForEdit,
                            editPrescriptionModalConsultationSelect,
                            editPrescriptionModalConsultationLoading,
                            oldConsultationIdForEdit
                        );
                    }
                }

                const editMedContainer = form.querySelector('#edit-medication-fields-container');
                if (editMedContainer) {
                    window.editMedicationGlobalIndex = editMedContainer.querySelectorAll('.medication-item-row').length;
                } else {
                    window.editMedicationGlobalIndex = 0;
                }
            }
        }
    }

    // for adding/removing medication rows in an EDIT prescription modal
    const editPrescriptionModalForm = document.getElementById('form-edit-prescription');
    if (editPrescriptionModalForm && medicationRowTemplateEl) {
        const addEditMedBtn = editPrescriptionModalForm.querySelector('#add-edit-medication-row-btn');
        const editMedContainer = editPrescriptionModalForm.querySelector('#edit-medication-fields-container');

        if (addEditMedBtn && editMedContainer) {
            if(typeof window.editMedicationGlobalIndex === 'undefined') {
                window.editMedicationGlobalIndex = editMedContainer.querySelectorAll('.medication-item-row').length;
            }
            addEditMedBtn.addEventListener('click', function() {
                const templateContent = medicationRowTemplateEl.innerHTML.replace(/__INDEX__/g, window.editMedicationGlobalIndex);
                const newRowDiv = document.createElement('div');
                newRowDiv.innerHTML = templateContent;
                const newRowElement = newRowDiv.firstElementChild;

                newRowElement.querySelectorAll('[id*="__INDEX__"]').forEach(el => {
                    el.id = el.id.replace(/__INDEX__/g, `edit_med_${window.editMedicationGlobalIndex}`);
                });
                newRowElement.querySelectorAll('label[for*="__INDEX__"]').forEach(label => {
                    label.htmlFor = label.htmlFor.replace(/__INDEX__/g, `edit_med_${window.editMedicationGlobalIndex}`);
                });

                editMedContainer.appendChild(newRowElement);
                window.editMedicationGlobalIndex++;
            });

            editMedContainer.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-medication-row-btn')) {
                    if (editMedContainer.querySelectorAll('.medication-item-row').length > 1) {
                        e.target.closest('.medication-item-row').remove();
                    } else {
                        alert("Vous ne pouvez pas retirer le dernier médicament de l'ordonnance. Au moins un est requis.");
                    }
                }
            });
        }
    }


    // --- for "View Prescription Modal" ---
    document.querySelectorAll('.view-prescription-btn').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.dataset.url; // This URL is set by Blade
            const modal = document.getElementById('viewPrescriptionModal');
            const body = document.getElementById('viewPrescriptionModalBody');
            if (!modal || !body || !url) {
                console.error("View Prescription Modal or Body or URL not found.");
                return;
            }

            body.innerHTML = '<p class="text-center py-3">Chargement des détails...</p>';
            modal.classList.add('active');

            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(response => {
                    if (!response.ok) {
                        let errorMsg = `Erreur réseau: ${response.status}`;
                        return response.text().then(text => { throw new Error(`${errorMsg} - ${text}`); });
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data || !data.patient) { throw new Error("Données de l'ordonnance invalides ou patient manquant."); }

                    let itemsHtml = '';
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            let sentence = `Le médicament <strong>${decodeHtmlEntities(item.medication_name) || 'Non spécifié'}</strong>`;
                            if (item.dosage) sentence += ` doit être pris à une dose de ${decodeHtmlEntities(item.dosage)}`;
                            else sentence += ` doit être pris`;
                            if (item.frequency) sentence += `, ${decodeHtmlEntities(item.frequency).toLowerCase()}`; // Assuming frequency includes "/jour" or similar
                            if (item.duration) sentence += `, pendant une durée de ${decodeHtmlEntities(item.duration).toLowerCase()}`;
                            sentence += ".";
                            if (item.notes) {
                                let formattedNotes = decodeHtmlEntities(item.notes).trim();
                                if (formattedNotes) {
                                    formattedNotes = formattedNotes.charAt(0).toUpperCase() + formattedNotes.slice(1);
                                    sentence += ` ${formattedNotes}`;
                                    if (!formattedNotes.endsWith('.')) sentence += ".";
                                }
                            }
                            itemsHtml += `<p style="margin-bottom: 0.75em;">${sentence}</p>`;
                        });
                    } else {
                        itemsHtml = "<p>Aucun médicament listé pour cette ordonnance.</p>";
                    }

                    const consultationLink = data.consultation
                        ? `Consultation du ${new Date(data.consultation.consultation_date_time || data.consultation.consultation_date).toLocaleDateString('fr-FR')} (Motif: ${decodeHtmlEntities(data.consultation.reason_for_visit) ? decodeHtmlEntities(data.consultation.reason_for_visit).substring(0,30) : 'N/A'})`
                        : 'Aucune';

                    body.innerHTML = `
                        <p><strong>Patient:</strong> <span>${decodeHtmlEntities(data.patient.name)}</span></p>
                        <p><strong>Date:</strong> <span>${new Date(data.prescription_date).toLocaleDateString('fr-FR')}</span></p>
                        <p><strong>Consultation Liée:</strong> <span>${consultationLink}</span></p>
                        <p><strong>Notes Générales:</strong></p>
                        <p style="white-space:pre-wrap; margin-bottom: 1em;">${decodeHtmlEntities(data.general_notes) || 'N/A'}</p>
                        <hr>
                        <h6>Médicaments:</h6>
                        <div>${itemsHtml}</div>
                    `;
                })
                .catch(error => {
                    console.error('Error fetching or processing prescription details:', error);
                    body.innerHTML = `<p class="text-danger text-center py-3">Erreur lors du chargement. (${error.message})</p>`;
                });
        });
    });

});
