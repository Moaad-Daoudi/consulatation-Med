<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>MediConsult - Espace Patient</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/patient_dashboard.css')); ?>">
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="<?php echo e(route('dashboard')); ?>" class="logo">Medi<span>Consult</span></a>
            </div>
            <?php if(auth()->guard()->check()): ?>
            <div class="user-info">
                <div class="user-avatar">
                    <?php if(Auth::user()->photo_path): ?>
                        <img src="<?php echo e(asset('storage/' . Auth::user()->photo_path)); ?>" alt="Avatar">
                    <?php else: ?>
                        <?php
                            $nameParts = explode(' ', Auth::user()->name, 2);
                            $initials = strtoupper(substr($nameParts[0], 0, 1));
                            if (isset($nameParts[1])) { $initials .= strtoupper(substr($nameParts[1], 0, 1)); }
                            elseif (strlen($nameParts[0]) > 1) { $initials = strtoupper(substr($nameParts[0], 0, 2)); }
                        ?>
                        <?php echo e($initials); ?>

                    <?php endif; ?>
                </div>
                <div class="user-name"><?php echo e(Auth::user()->name); ?></div>
                <?php if(Auth::user()->role): ?>
                <div class="user-role"><?php echo e(ucfirst(Auth::user()->role->name)); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <ul class="sidebar-menu">
                <li>
                    <a href="#" class="menu-link active" data-section="patient_dashboard_content">
                        <div class="menu-icon"><img src="<?php echo e(asset('assets/sidebar/tableau_de_bord.png')); ?>" alt="Dashboard"></div>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_appointments_content">
                        <div class="menu-icon"><img src="<?php echo e(asset('assets/sidebar/rendez_vous.png')); ?>" alt="Rendez-vous"></div>
                        <span>Mes rendez-vous</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_medical_file_content">
                        <div class="menu-icon"><img src="<?php echo e(asset('assets/sidebar/dossier_medical.png')); ?>" alt="Dossier Médical"></div>
                        <span>Mon dossier médical</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_prescriptions_content">
                        <div class="menu-icon"><img src="<?php echo e(asset('assets/sidebar/ordonnances.png')); ?>" alt="Ordonnances"></div>
                        <span>Mes ordonnances</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_settings_content">
                        <div class="menu-icon"><img src="<?php echo e(asset('assets/sidebar/profile.png')); ?>" alt="Profil"></div>
                        <span>Profil</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" id="logout-form-patient-dashboard" style="display: none;"><?php echo csrf_field(); ?></form>
                    <a href="<?php echo e(route('logout')); ?>" class="menu-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form-patient-dashboard').submit();">
                        <div class="menu-icon"><img src="<?php echo e(asset('assets/sidebar/logout.png')); ?>" alt="Déconnexion"></div>
                        <span>Déconnexion</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <h1 class="page-title" id="patientDynamicPageTitle">Tableau de bord</h1>
                <?php if(auth()->guard()->check()): ?>
                <div class="topbar-actions">
                    <div class="user-profile">
                        <div class="user-profile-img">
                                <?php
                                    $namePartsTopbar = explode(' ', Auth::user()->name, 2);
                                    $initialsTopbar = strtoupper(substr($namePartsTopbar[0], 0, 1));
                                    if (isset($namePartsTopbar[1])) { $initialsTopbar .= strtoupper(substr($namePartsTopbar[1], 0, 1)); }
                                    elseif (strlen($namePartsTopbar[0]) > 1) { $initialsTopbar = strtoupper(substr($namePartsTopbar[0], 0, 2)); }
                                ?>
                                <?php echo e($initialsTopbar); ?>

                        </div>
                        <span><?php echo e(Str::words(Auth::user()->name, 1, '')); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="content-wrapper">
                
                <?php if(session('success')): ?> <div class="alert alert-success success-alert" role="alert"><?php echo e(session('success')); ?></div> <?php endif; ?>
                <?php if(session('error')): ?> <div class="alert alert-danger error-alert" role="alert"><?php echo e(session('error')); ?></div> <?php endif; ?>

                <?php $openModalPatient = session('open_modal_on_load'); ?>
                <?php if($errors->any() && $openModalPatient === 'patient-create-appointment-modal' ): ?>
                    <div class="alert alert-danger error-alert" style="margin-bottom:15px;"><strong>Erreurs lors de la création du RDV:</strong><ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
                <?php elseif($errors->any() && $openModalPatient !== 'patient-create-appointment-modal' && Auth::user()->role->name === 'patient' && session('active_section_on_load') === 'patient_settings_content' ): ?>
                    
                <?php elseif($errors->any() && !$openModalPatient && !(Auth::user()->role->name === 'patient' && session('active_section_on_load') === 'patient_settings_content') ): ?>
                     <div class="alert alert-danger error-alert" style="margin-bottom:15px;"><strong>Erreurs générales:</strong><ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
                <?php endif; ?>

                
                <?php echo $__env->make('patient.dashboard', [
                    'upcomingAppointmentCount' => $upcomingAppointmentCount ?? 0,
                    'activePrescriptionsCount' => $activePrescriptionsCount ?? 0,
                    'nextAppointment' => $nextAppointment ?? null,
                    'medicationReminders' => $medicationReminders ?? collect()
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('patient.appointments', [
                    'upcomingAppointments' => $upcomingAppointments ?? collect(),
                    'pastAppointments' => $pastAppointments ?? collect()
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('patient.medical_file', [
                    'patientConsultations' => $patientConsultations ?? collect()
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('patient.prescriptions', [
                     'activePrescriptions' => $activePrescriptions ?? collect(),
                     'pastPrescriptions' => $pastPrescriptions ?? collect()
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('patient.settings', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> 
            </div>
        </main>
    </div>

    <!-- Modal pour que le PATIENT crée un nouveau rendez-vous -->
    <div class="modal-overlay <?php echo e(($errors->any() && session('open_modal_on_load') === 'patient-create-appointment-modal') ? 'active' : ''); ?>" id="patient-create-appointment-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Prendre un Nouveau Rendez-vous</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            <form id="form-patient-create-appointment-modal" action="<?php echo e(route('patient.appointments.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    
                    <?php if($errors->any() && session('open_modal_on_load') === 'patient-create-appointment-modal'): ?>
                        
                        
                    <?php endif; ?>

                    <div class="modal-form"> 
                        <div class="form-group">
                            <label for="modal_patient_appt_doctor_select">Médecin</label>
                            <select id="modal_patient_appt_doctor_select" name="doctor_id" class="form-control <?php $__errorArgs = ['doctor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Sélectionner un médecin</option>
                                <?php $__currentLoopData = $doctors ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?> 
                                    <option value="<?php echo e($doctor->id); ?>" <?php echo e(old('doctor_id') == $doctor->id ? 'selected' : ''); ?>>Dr. <?php echo e($doctor->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['doctor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group">
                            <label for="modal_patient_appt_date_input">Date</label>
                            <input type="date" id="modal_patient_appt_date_input" name="appointment_date" class="form-control <?php $__errorArgs = ['appointment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('appointment_date', date('Y-m-d'))); ?>" min="<?php echo e(date('Y-m-d')); ?>" required>
                            <?php $__errorArgs = ['appointment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="form-group full-width">
                            <label for="modal_patient_appt_time_select">Heure Disponible</label>
                            <select id="modal_patient_appt_time_select" name="appointment_time" class="form-control <?php $__errorArgs = ['appointment_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Sélectionnez d'abord un médecin et une date</option>
                                 <?php if(old('appointment_time')): ?>
                                    <option value="<?php echo e(old('appointment_time')); ?>" selected><?php echo e(old('appointment_time')); ?> (Précédemment)</option>
                                <?php endif; ?>
                            </select>
                            <?php $__errorArgs = ['appointment_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div id="modal_patient_slots_loading" style="display: none; margin-top: 5px;">Chargement des créneaux...</div>
                            <div id="modal_patient_slots_error" style="display: none; color: red; margin-top: 5px;"></div>
                        </div>

                        <div class="form-group full-width">
                            <label for="modal_patient_appt_notes_textarea">Raison du RDV / Notes (optionnel)</label>
                            <textarea id="modal_patient_appt_notes_textarea" name="notes" class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3" placeholder="Ex: Consultation de suivi, symptômes spécifiques..."><?php echo e(old('notes')); ?></textarea>
                            <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le rendez-vous</button>
                </div>
            </form>
        </div>
    </div>

<script>
    window.patientDashboardConfig = {
            routes: {
                logout: "<?php echo e(route('logout')); ?>",
                availableSlots: "<?php echo e(route('appointments.available_slots')); ?>"
            },
            session: {
                openModalOnLoad: <?php echo json_encode(session('open_modal_on_load'), 15, 512) ?>,
                activeSectionOnLoad: <?php echo json_encode(session('active_section_on_load'), 15, 512) ?>
            },
            errors: {
                any: <?php echo e($errors->any() ? 'true' : 'false'); ?>,
                hasProfileSettingsErrorsForPatientSettings: <?php echo e($errors->any() &&
                    Auth::user()->role->name === 'patient' &&
                    (session('active_section_on_load') === 'patient_settings_content' || old('_token')) &&
                    !empty(array_intersect(array_keys($errors->getMessages()), ['name', 'email', 'phone_number', 'photo', 'date_of_birth', 'gender', 'address', 'emergency_contact', 'emergency_contact_phone']))
                    ? 'true' : 'false'); ?>

            },
            auth: {
                roleName: <?php echo json_encode(Auth::user()->role->name ?? '', 15, 512) ?>
            },
            oldInput: {
                appointmentTime: <?php echo json_encode(old('appointment_time', ''), 512) ?>,
                csrfToken: <?php echo json_encode(csrf_token(), 15, 512) ?>
            },
            initialSectionFromServer:
                <?php if($errors->any() && session('open_modal_on_load') === 'patient-create-appointment-modal'): ?>
                    'patient_appointments_content'
                <?php elseif($errors->any() && Auth::user()->role->name === 'patient' && (session('active_section_on_load') === 'patient_settings_content' || old('_token'))): ?>
                    <?php if(!empty(array_intersect(array_keys($errors->getMessages()), ['name', 'email', 'phone_number', 'photo', 'date_of_birth', 'gender', 'address', 'emergency_contact', 'emergency_contact_phone']))): ?>
                        'patient_settings_content'
                    <?php else: ?>
                        null
                    <?php endif; ?>
                <?php else: ?>
                    null
                <?php endif; ?>
        };
    </script>
    <script src="<?php echo e(asset('js/patient_dashboard.js')); ?>" defer></script>
</body>
</html>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/layouts/patient_dashboard.blade.php ENDPATH**/ ?>