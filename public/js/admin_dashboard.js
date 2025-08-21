document.addEventListener('DOMContentLoaded', function () {

    // ===================================================================
    // PAGE-SPECIFIC INITIALIZERS
    // This is the core logic that prevents errors. It checks which page
    // we are on by looking for a unique element, and then runs only the
    // relevant setup function for that page.
    // ===================================================================

    // If an element unique to the User Management page exists, run its setup.
    if (document.getElementById('user-creation-form')) {
        initializeUserManagement();
    }

    // If an element unique to the Appointment Management page exists, run its setup.
    if (document.getElementById('appointmentForm')) {
        initializeAppointmentManagement();
    }

    async function apiFetch(url, options = {}) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
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
            throw error;
        }
        return response.json();
    }

    const flashMessage = document.getElementById('flash-message');
    if (flashMessage) {
        setTimeout(() => {
            flashMessage.classList.add('fade-out');
            setTimeout(() => flashMessage.remove(), 500);
        }, 8000);
    }


    // ===================================================================
    // INITIALIZER FOR THE USER MANAGEMENT PAGE
    // ===================================================================
    function initializeUserManagement() {
        console.log('Admin JS: Initializing User Management module.');

        // --- Selectors for User Management ---
        const userModal = document.getElementById('UserModal');
        const viewModal = document.getElementById('viewUserModal');
        const userForm = document.getElementById('user-creation-form');
        const viewUserContent = document.getElementById('viewUserContent');
        const doctorFields = document.getElementById('doctorFields');
        const patientFields = document.getElementById('patientFields');
        const roleSelector = document.getElementById('roles');
        const passwordInput = document.getElementById('ps');
        const passwordConfirmationInput = document.getElementById('password_confirmation');

        // --- Event Listeners for User Management ---
        if (roleSelector) {
            roleSelector.addEventListener('change', function (event) {
                const selectedRole = event.target.value;
                if (doctorFields) doctorFields.style.display = (selectedRole === 'doctor') ? 'block' : 'none';
                if (patientFields) patientFields.style.display = (selectedRole === 'patient') ? 'block' : 'none';
            });
        }
        
        // --- Functions for User Management ---
        window.openUserModal = async function(userId = null) {
            const title = userModal.querySelector('.modal-header h2');
            const button = userModal.querySelector('.modal-footer .btn-submit');
            userForm.reset();
            document.querySelectorAll('.error-message').forEach(el => el.textContent = '');
            if (roleSelector) roleSelector.dispatchEvent(new Event('change'));

            if (userId) { 
                title.innerText = 'Edit User';
                button.innerText = 'Save Changes';
                userForm.action = userForm.dataset.actionUpdateTemplate.replace(':userId', userId);
                if (!userForm.querySelector('input[name="_method"]')) {
                    userForm.insertAdjacentHTML('beforeend', '<input type="hidden" name="_method" value="PATCH">');
                }
                passwordInput.removeAttribute('required');
                passwordConfirmationInput.removeAttribute('required');
                passwordInput.placeholder = "Leave blank to keep current password";
                try {
                    const response = await fetch(`/admin/users/${userId}`);
                    if (!response.ok) throw new Error(`Server responded with status: ${response.status}`);
                    const user = await response.json();
                    userForm.name.value = user.name || '';
                    userForm.email.value = user.email || '';
                    if (userForm.gender) userForm.gender.value = user.doctor?.gender || user.patient?.gender || '';
                    if (user.role) {
                        userForm.role.value = user.role.role.toLowerCase();
                        if (user.role.role.toLowerCase() === 'doctor' && user.doctor) {
                            userForm.specialisation.value = user.doctor.specialisation || '';
                            userForm.phone_number.value = user.doctor.phone_number || '';
                            userForm.biography.value = user.doctor.biography || '';
                        } else if (user.role.role.toLowerCase() === 'patient' && user.patient) {
                            userForm.date_of_birth.value = user.patient.date_of_birth || '';
                            userForm.blood_type.value = user.patient.blood_type || '';
                        }
                    }
                    if (roleSelector) roleSelector.dispatchEvent(new Event('change'));
                } catch (error) {
                    console.error("Error fetching user for edit:", error);
                    alert("Could not load user data. Please check the browser console for errors.");
                    return;
                }
            } else {
                title.innerText = 'Create New User';
                button.innerText = 'Create Account';
                userForm.action = userForm.dataset.actionCreate;
                const methodInput = userForm.querySelector('input[name="_method"]');
                if (methodInput) methodInput.remove();
                passwordInput.setAttribute('required', 'required');
                passwordConfirmationInput.setAttribute('required', 'required');
                passwordInput.placeholder = "";
            }
            userModal.style.display = 'flex';
        };

        window.openViewModal = async function(userId) {
            if (!viewUserContent) return;
            viewUserContent.innerHTML = '<div class="loading-spinner"></div>';
            viewModal.style.display = 'flex';
            try {
                const response = await fetch(`/admin/users/${userId}`);
                if (!response.ok) throw new Error(`Network response was not ok. Status: ${response.status}`);
                const user = await response.json();
                const roleName = user.role ? user.role.role : 'N/A'; 
                const roleClass = user.role ? user.role.role.toLowerCase() : 'guest';
                const gender = user.doctor?.gender || user.patient?.gender || 'N/A';
                const avatarUrl = user.avatar_url || '/default-avatar.png';
                let detailsHtml = '';
                if (roleClass === 'doctor' && user.doctor) {
                    detailsHtml = `...`; // Your doctor details HTML
                } else if (roleClass === 'patient' && user.patient) {
                    detailsHtml = `...`; // Your patient details HTML
                }
                const profileHtml = `...`; // Your complete profile HTML structure
                viewUserContent.innerHTML = profileHtml;
            } catch (error) {
                viewUserContent.innerHTML = '<p class="error-message-full">Could not load user details.</p>';
                console.error('Error in openViewModal:', error);
            }
        };

        window.closeUserModal = () => { if(userModal) userModal.style.display = 'none'; };
        window.closeViewModal = () => { if(viewModal) viewModal.style.display = 'none'; };

        window.addEventListener('click', function(event) {
            if (event.target === userModal) closeUserModal();
            if (event.target === viewModal) closeViewModal();
        });
    }

    // ===================================================================
    // INITIALIZER FOR THE APPOINTMENT MANAGEMENT PAGE
    // ===================================================================
    function initializeAppointmentManagement() {
        console.log('Admin JS: Initializing Appointment Management module.');
        
        // --- Config & Selectors ---
        const config = window.adminAppointmentConfig || {};
        const appointmentsData = config.appointments || {};

        const appointmentModal = document.getElementById('appointmentModal');
        const appointmentForm = document.getElementById('appointmentForm');
        const appointmentModalTitle = document.getElementById('appointmentModalTitle');
        const appointmentMethodInput = document.getElementById('appointmentMethodInput');
        const appointmentSubmitButton = document.getElementById('appointmentSubmitButton');
        
        const deleteAppointmentModal = document.getElementById('deleteAppointmentModal');
        const deleteAppointmentForm = document.getElementById('deleteAppointmentForm');

        const doctorSelect = document.getElementById('admin_appointment_doctor_id');
        const dateInput = document.getElementById('admin_appointment_date');
        const timeSelect = document.getElementById('admin_appointment_time');
        const slotsLoading = document.getElementById('admin_slots_loading');
        const slotsError = document.getElementById('admin_slots_error');

        if (doctorSelect) doctorSelect.addEventListener('change', fetchAdminAvailableSlots);
        if (dateInput) dateInput.addEventListener('change', fetchAdminAvailableSlots);

        async function fetchAdminAvailableSlots() {
            const doctorId = doctorSelect.value;
            const selectedDate = dateInput.value;

            if (!doctorId || !selectedDate) {
                timeSelect.innerHTML = '<option value="">Sélectionnez un médecin et une date</option>';
                return;
            }

            timeSelect.innerHTML = '<option value="">Chargement...</option>';
            timeSelect.disabled = true;
            slotsError.style.display = 'none';
            slotsLoading.style.display = 'block';

            try {
                // This reuses the same apiFetch helper you already have
                const data = await apiFetch(config.availableSlotsUrl, {
                    method: 'POST',
                    body: { doctor_id: doctorId, date: selectedDate }
                });

                timeSelect.innerHTML = '<option value="">-- Choisissez une heure --</option>';
                if (data.slots && data.slots.length > 0) {
                    data.slots.forEach(slot => timeSelect.add(new Option(slot, slot)));
                    timeSelect.disabled = false;
                } else {
                    slotsError.textContent = data.message || 'Aucun créneau disponible.';
                    slotsError.style.display = 'block';
                }
            } catch (error) {
                slotsError.textContent = 'Erreur lors du chargement des créneaux.';
                slotsError.style.display = 'block';
                console.error('Admin slot fetch error:', error);
            } finally {
                slotsLoading.style.display = 'none';
            }
        }

        // --- Functions for Appointment Management ---
        window.openAppointmentModal = function(appointmentId = null) {
            if (!appointmentModal || !appointmentForm) return;
            appointmentForm.reset();

            if (appointmentId && appointmentsData[appointmentId]) {
                // EDIT MODE
                const appt = appointmentsData[appointmentId];
                appointmentModalTitle.textContent = 'Edit Appointment';
                appointmentSubmitButton.textContent = 'Save Changes';
                appointmentForm.action = config.updateUrlTemplate.replace(':id', appointmentId);
                appointmentMethodInput.value = 'PATCH';

                appointmentForm.querySelector('[name="patient_id"]').value = appt.patient_id;
                appointmentForm.querySelector('[name="doctor_id"]').value = appt.doctor_id;
                appointmentForm.querySelector('[name="status"]').value = appt.status;

                const time = new Date(appt.appointment_datetime).toTimeString().slice(0, 5);
                if (!timeSelect.querySelector(`option[value="${time}"]`)) {
                    timeSelect.add(new Option(`${time} (Actuel)`, time));
                }
                timeSelect.value = time;
                timeSelect.disabled = false;
            } else {
                // CREATE MODE
                appointmentModalTitle.textContent = 'Create New Appointment';
                appointmentSubmitButton.textContent = 'Save Appointment';
                appointmentForm.action = config.storeUrl;
                appointmentMethodInput.value = 'POST';
                timeSelect.innerHTML = '<option value="">Sélectionnez un médecin et une date</option>';
            }
            appointmentModal.style.display = 'flex';
        };

        window.openDeleteModal = function(appointmentId) {
            if (!deleteAppointmentModal || !deleteAppointmentForm) return;
            deleteAppointmentForm.action = config.deleteUrlTemplate.replace(':id', appointmentId);
            deleteAppointmentModal.style.display = 'flex';
        };
        
        window.closeAppointmentModal = () => { if(appointmentModal) appointmentModal.style.display = 'none'; };
        window.closeDeleteModal = () => { if(deleteAppointmentModal) deleteAppointmentModal.style.display = 'none'; };
        
        window.addEventListener('click', function(event) {
            if (event.target === appointmentModal) closeAppointmentModal();
            if (event.target === deleteAppointmentModal) closeDeleteModal();
        });
    }

});