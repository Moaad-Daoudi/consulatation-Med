// Ensure patientDashboardConfig and its properties are available, providing defaults if not
const config = window.patientDashboardConfig || {};
const routes = config.routes || { logout: '#', availableSlots: '#' };
const session = config.session || { openModalOnLoad: null, activeSectionOnLoad: null };
const errors = config.errors || { any: false, hasProfileSettingsForPatientSettings: false };
const auth = config.auth || { roleName: '' };
const oldInput = config.oldInput || { appointmentTime: '', csrfToken: '' };
const initialSectionFromServer = config.initialSectionFromServer || null;

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content') || oldInput.csrfToken;

document.addEventListener('DOMContentLoaded', function() {
    // --- SPA Navigation Logic ---
    const menuLinks = document.querySelectorAll('.sidebar-menu .menu-link');
    const contentSections = document.querySelectorAll('.content-wrapper > .content-section'); // Be more specific with selector
    const pageTitleElement = document.getElementById('patientDynamicPageTitle');

    function activateSection(sectionId) {
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
            localStorage.setItem('activePatientSection', sectionId);
        } else if (contentSections.length > 0 && !document.querySelector('.content-wrapper > .content-section.active')) {
            // Fallback if no section found or activated, activate the first one
            if (contentSections[0]) {
                contentSections[0].classList.add('active');
                localStorage.setItem('activePatientSection', contentSections[0].id);
                 if (pageTitleElement) {
                    const defaultLink = document.querySelector(`.sidebar-menu .menu-link[data-section="${contentSections[0].id}"]`);
                    if (defaultLink && defaultLink.querySelector('span')) {
                         pageTitleElement.textContent = defaultLink.querySelector('span').textContent;
                    }
                }
            }
        }
    }

    menuLinks.forEach(link => {
        const href = link.getAttribute('href');
        const dataSection = link.getAttribute('data-section');

        // Use the route from config for logout comparison
        const isLogoutLink = (href === routes.logout) ||
                                (link.onclick && link.onclick.toString().includes('logout-form-patient-dashboard'));

        const isSPALink = dataSection && (href === '#' || !href);

        if (isLogoutLink) {
            // This is a logout link, let its default action (or onclick) proceed
            return;
        }

        if (isSPALink) {
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
        }
        // For non-SPA links that aren't logout, let them navigate normally
    });

    // Determine initial section to activate
    const savedPatientSection = localStorage.getItem('activePatientSection');
    let initialSectionIdToActivate = 'patient_dashboard_content'; // Default

    // Priority 1: Server-side instruction due to errors or specific state
    if (initialSectionFromServer && document.getElementById(initialSectionFromServer)) {
        initialSectionIdToActivate = initialSectionFromServer;
    }
    // Priority 2: User's last saved section (if no server instruction)
    else if (savedPatientSection && document.getElementById(savedPatientSection)) {
        initialSectionIdToActivate = savedPatientSection;
    }

    let activeLinkForPageLoad = document.querySelector(`.sidebar-menu .menu-link[data-section="${initialSectionIdToActivate}"]`);

    // Fallback if the determined section/link isn't found, default to dashboard
    if (!activeLinkForPageLoad) {
        initialSectionIdToActivate = 'patient_dashboard_content';
        activeLinkForPageLoad = document.querySelector(`.sidebar-menu .menu-link[data-section="${initialSectionIdToActivate}"]`);
    }

    if (activeLinkForPageLoad) {
        menuLinks.forEach(item => item.classList.remove('active'));
        activeLinkForPageLoad.classList.add('active');
        if (pageTitleElement && activeLinkForPageLoad.querySelector('span')) {
            pageTitleElement.textContent = activeLinkForPageLoad.querySelector('span').textContent;
        }
    } else if (menuLinks.length > 0 && menuLinks[0].dataset.section) {
        // Fallback to the very first link in the sidebar if absolutely nothing else is found
        initialSectionIdToActivate = menuLinks[0].dataset.section;
        menuLinks[0].classList.add('active');
        if (pageTitleElement && menuLinks[0].querySelector('span')) {
            pageTitleElement.textContent = menuLinks[0].querySelector('span').textContent;
        }
    }
    activateSection(initialSectionIdToActivate);


    // --- Modal Trigger Logic ---
    document.querySelectorAll('[data-modal-target]').forEach(button => {
        button.addEventListener('click', () => {
            const modalId = button.getAttribute('data-modal-target');
            const modal = document.getElementById(modalId);
            if (modal) {
                // Close other active modals first
                document.querySelectorAll('.modal-overlay.active').forEach(activeModal => {
                    if (activeModal.id !== modalId) activeModal.classList.remove('active');
                });
                modal.classList.add('active');
                // If opening the create appointment modal, and doctor/date are pre-filled, fetch slots
                if (modalId === 'patient-create-appointment-modal') {
                    const dateInput = document.getElementById('modal_patient_appt_date_input');
                    const doctorInput = document.getElementById('modal_patient_appt_doctor_select');
                    if (dateInput && dateInput.value && doctorInput && doctorInput.value) {
                        fetchPatientModalAvailableSlots();
                    }
                }
            }
        });
    });

    // --- Modal Close Logic ---
    document.querySelectorAll('.modal-close, .modal-close-btn').forEach(button => {
        button.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent event bubbling to overlay if button is inside
            button.closest('.modal-overlay')?.classList.remove('active');
        });
    });

    // Close modal when clicking on the overlay itself
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) { // Only if the click is directly on the overlay
                this.classList.remove('active');
            }
        });
    });


    // --- for Patient Create Appointment Modal Slots ---
    const patientCreateApptModalEl = document.getElementById('patient-create-appointment-modal');
    let doctorSelectPatientModal, dateInputPatientModal, timeSelectPatientModal, slotsLoadingPatientModal, slotsErrorPatientModal;

    function fetchPatientModalAvailableSlots() {
        if (!doctorSelectPatientModal) doctorSelectPatientModal = document.getElementById('modal_patient_appt_doctor_select');
        if (!dateInputPatientModal) dateInputPatientModal = document.getElementById('modal_patient_appt_date_input');
        if (!timeSelectPatientModal) timeSelectPatientModal = document.getElementById('modal_patient_appt_time_select');
        if (!slotsLoadingPatientModal) slotsLoadingPatientModal = document.getElementById('modal_patient_slots_loading');
        if (!slotsErrorPatientModal) slotsErrorPatientModal = document.getElementById('modal_patient_slots_error');

        const doctorId = doctorSelectPatientModal.value;
        const selectedDate = dateInputPatientModal.value;
        const previouslySelectedTime = timeSelectPatientModal.dataset.oldTime || oldInput.appointmentTime;

        timeSelectPatientModal.innerHTML = '<option value="">Chargement...</option>';
        timeSelectPatientModal.disabled = true;
        if(slotsErrorPatientModal) { slotsErrorPatientModal.style.display = 'none'; slotsErrorPatientModal.textContent = ''; }
        if(slotsLoadingPatientModal) slotsLoadingPatientModal.style.display = 'block';


        if (!doctorId || !selectedDate) {
            timeSelectPatientModal.innerHTML = '<option value="">Sélectionnez un médecin et une date</option>';
            if(slotsLoadingPatientModal) slotsLoadingPatientModal.style.display = 'none';
            return;
        }

        // Validate date isn't in the past (client-side check, server should re-validate)
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Normalize today's date to start of day
        const inputDate = new Date(selectedDate);
        inputDate.setHours(0, 0, 0, 0); // Normalize input date

        if (inputDate < today) {
            timeSelectPatientModal.innerHTML = '<option value="">Date invalide</option>';
            if(slotsErrorPatientModal) { slotsErrorPatientModal.textContent = 'La date ne peut pas être dans le passé.'; slotsErrorPatientModal.style.display = 'block'; }
            if(slotsLoadingPatientModal) slotsLoadingPatientModal.style.display = 'none';
            return;
        }

        fetch(routes.availableSlots, { // Use route from config
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken, // Use csrfToken from meta tag or config
                'Accept': 'application/json'
            },
            body: JSON.stringify({ doctor_id: doctorId, date: selectedDate })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => {
                    console.error("Server error:", err);
                    throw err; // Re-throw to be caught by .catch
                });
            }
            return response.json();
        })
        .then(data => {
            if(slotsLoadingPatientModal) slotsLoadingPatientModal.style.display = 'none';
            timeSelectPatientModal.innerHTML = '<option value="">-- Choisissez une heure --</option>';
            if (data.slots && data.slots.length > 0) {
                data.slots.forEach(slot => {
                    const opt = document.createElement('option');
                    opt.value = slot;
                    opt.textContent = slot;
                    if (slot === previouslySelectedTime) { // Check against previously selected/old input
                        opt.selected = true;
                    }
                    timeSelectPatientModal.appendChild(opt);
                });
                timeSelectPatientModal.disabled = false;
            } else {
                // If there was an old time, show it as unavailable
                if (previouslySelectedTime) {
                    const opt = document.createElement('option');
                    opt.value = previouslySelectedTime;
                    opt.textContent = previouslySelectedTime + " (Non disponible)";
                    opt.disabled = true;
                    opt.selected = true;
                    timeSelectPatientModal.appendChild(opt);
                } else {
                    timeSelectPatientModal.innerHTML = '<option value="">Aucun créneau disponible</option>';
                }
                if(slotsErrorPatientModal) {
                    slotsErrorPatientModal.textContent = data.message || 'Aucun créneau disponible pour cette date.';
                    slotsErrorPatientModal.style.display = 'block';
                }
            }
        })
        .catch(error => {
            if(slotsLoadingPatientModal) slotsLoadingPatientModal.style.display = 'none';
            timeSelectPatientModal.innerHTML = '<option value="">Erreur de chargement</option>';
            console.error('Error fetching slots for patient modal:', error);
            let errorMsg = 'Une erreur est survenue lors du chargement des créneaux.';
            if (error && error.message && typeof error.message === 'string') {
                errorMsg = error.message;
            } else if (error && error.errors && typeof error.errors === 'object') {
                errorMsg = Object.values(error.errors).flat().join(' ');
            }
            if(slotsErrorPatientModal) {
                slotsErrorPatientModal.textContent = `Erreur: ${errorMsg}`;
                slotsErrorPatientModal.style.display = 'block';
            }
        });
    }

    if(patientCreateApptModalEl){
        doctorSelectPatientModal = document.getElementById('modal_patient_appt_doctor_select');
        dateInputPatientModal = document.getElementById('modal_patient_appt_date_input');
        timeSelectPatientModal = document.getElementById('modal_patient_appt_time_select'); // Ensure this is defined here too

        if(doctorSelectPatientModal) doctorSelectPatientModal.addEventListener('change', fetchPatientModalAvailableSlots);
        if(dateInputPatientModal) dateInputPatientModal.addEventListener('change', fetchPatientModalAvailableSlots);

        // If there's an old appointment time, set it as a data attribute for fetchPatientModalAvailableSlots
        if(timeSelectPatientModal && oldInput.appointmentTime) {
            timeSelectPatientModal.dataset.oldTime = oldInput.appointmentTime;
        }
    }

    // --- Re-open modal if validation errors based on session data from config ---
    const patientModalToOpen = session.openModalOnLoad; // Use session data from config
    if (patientModalToOpen) {
        const modalEl = document.getElementById(patientModalToOpen);
        if (modalEl && !modalEl.classList.contains('active')) {
            modalEl.classList.add('active');
            // If it's the appointment creation modal and relevant fields have values (e.g., from old input after error)
            if (patientModalToOpen === 'patient-create-appointment-modal') {
                const doctorInput = document.getElementById('modal_patient_appt_doctor_select');
                const dateInput = document.getElementById('modal_patient_appt_date_input');
                if (doctorInput && doctorInput.value && dateInput && dateInput.value) {
                    fetchPatientModalAvailableSlots(); // Fetch slots based on old input
                }
            }
        }
    }

    // --- Auto-hide session alerts ---
    document.querySelectorAll('.alert-success.success-alert').forEach(alertEl => {
        setTimeout(() => {
            alertEl.style.transition = 'opacity 0.5s ease';
            alertEl.style.opacity = '0';
            setTimeout(() => { alertEl.style.display = 'none'; }, 500); // Remove after fade out
        }, 7000); // 7 seconds
    });

    document.querySelectorAll('.alert-danger.error-alert').forEach(alertEl => {
        // Only auto-hide general page errors, not those inside an active modal
        if (!alertEl.closest('.modal-overlay.active')) {
            setTimeout(() => {
                alertEl.style.transition = 'opacity 0.5s ease';
                alertEl.style.opacity = '0';
                setTimeout(() => { alertEl.style.display = 'none'; }, 500);
            }, 15000); // 15 seconds for error messages
        }
    });
});
