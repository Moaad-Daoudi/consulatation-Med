<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>MediConsult - Dashboard Médecin</title>
    <link rel="stylesheet" href="<?php echo e(asset('css/doctor_dashboard.css')); ?>">
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
                <div class="user-avatar"><?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?></div>
                <div class="user-name"><?php echo e(Auth::user()->name); ?></div>
                <?php if(Auth::user()->role): ?>
                    <div class="user-role"><?php echo e(ucfirst(Auth::user()->role->name)); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <ul class="sidebar-menu">
                <li>
                    <a href="#" class="menu-link active" data-section="dashboard">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/tableau_de_bord.png')); ?>" alt="Dashboard Icon">
                        </div>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="appointments">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/rendez_vous.png')); ?>" alt="Appointments Icon">
                        </div>
                        <span>Rendez-vous</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patients">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/patients.png')); ?>" alt="Patients Icon">
                        </div>
                        <span>Patients</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="consultations">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/consultations.png')); ?>" alt="Consultations Icon">
                        </div>
                        <span>Consultations</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="ordonnances">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/ordonnances.png')); ?>" alt="Ordonnances Icon">
                        </div>
                        <span>Ordonnances</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="parametres">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/profile.png')); ?>" alt="Profile Icon">
                        </div>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" id="logout-form-doctor-dashboard" style="display: none;"><?php echo csrf_field(); ?></form>
                    <a href="<?php echo e(route('logout')); ?>" class="menu-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form-doctor-dashboard').submit();">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/logout.png')); ?>" alt="Logout Icon">
                        </div>
                        <span>Déconnexion</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <h1 class="page-title" id="dynamicPageTitle">Tableau de bord</h1>
                <?php if(auth()->guard()->check()): ?>
                <div class="topbar-actions">
                    <div class="user-profile">
                        <div class="user-profile-img"><?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?></div>
                        <span><?php echo e(Str::before(Auth::user()->name, ' ')); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="content-wrapper">
                
                <?php if(session('success')): ?>
                    <div class="alert alert-success" role="alert"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="alert alert-danger" role="alert"><?php echo e(session('error')); ?></div>
                <?php endif; ?>

                <?php echo $__env->make('doctor.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.appointments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.patients', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.consultations', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.ordonnances', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.parametres', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </main>
    </div>

    <!-- Modal pour ajouter un nouveau patient -->
    <div class="modal-overlay <?php echo e($errors->hasBag('addPatientModal') || session('open_modal_on_load') === 'add-patient-modal' ? 'active' : ''); ?>" id="add-patient-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Ajouter un nouveau patient</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                <?php if($errors->hasBag('addPatientModal') && $errors->getBag('addPatientModal')->any()): ?>
                    <div class="alert alert-danger">
                        <ul><?php $__currentLoopData = $errors->getBag('addPatientModal')->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                    </div>
                <?php endif; ?>
                <form class="modal-form" id="form-add-new-patient-details-modal" action="<?php echo e(route('doctor.patients.store_from_modal')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group"><label for="modal_new_patient_name_field">Nom Complet</label><input type="text" class="form-control <?php $__errorArgs = ['name', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="modal_new_patient_name_field" name="name" value="<?php echo e(old('name')); ?>" required> <?php $__errorArgs = ['name', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    <div class="form-group"><label for="modal_new_patient_email_field">Email</label><input type="email" class="form-control <?php $__errorArgs = ['email', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="modal_new_patient_email_field" name="email" value="<?php echo e(old('email')); ?>" required> <?php $__errorArgs = ['email', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    <div class="form-group"><label for="modal_new_patient_password_field">Mot de passe</label><input type="password" class="form-control <?php $__errorArgs = ['password', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="modal_new_patient_password_field" name="password" required> <?php $__errorArgs = ['password', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    <div class="form-group"><label for="modal_new_patient_password_confirmation_field">Confirmer Mot de passe</label><input type="password" class="form-control" id="modal_new_patient_password_confirmation_field" name="password_confirmation" required></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" form="form-add-new-patient-details-modal" class="btn">Enregistrer</button>
            </div>
        </div>
    </div>

    <!-- View Patient Dossier Modal -->
    <div class="modal-overlay" id="viewPatientDossierModal">
        <div class="modal modal-content" style="max-width: 900px;"> 
            <div class="modal-header">
                <h5 class="modal-title">Dossier Patient: <span id="dossier_patient_name"></span></h5>
                <button type="button" class="modal-close" data-modal-dismiss="viewPatientDossierModal">×</button>
            </div>
            <div class="modal-body" id="viewPatientDossierModalBody">
                <p class="text-center">Chargement du dossier...</p>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="viewPatientDossierModal">Fermer</button>
            </div>
        </div>
    </div>

    <!-- Modal pour que le DOCTEUR crée un nouveau rendez-vous -->
    <div class="modal-overlay <?php echo e(($errors->any() && !$errors->hasBag('addPatientModal')) || session('open_modal_on_load') === 'doctor-create-appointment-modal' ? 'active' : ''); ?>" id="doctor-create-appointment-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Créer un Nouveau Rendez-vous</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                 <?php if($errors->any() && !$errors->hasBag('addPatientModal')): ?> 
                    <div class="alert alert-danger">
                        <ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                    </div>
                <?php endif; ?>
                <form id="form-doctor-create-appointment-modal" action="<?php echo e(route('doctor.appointments.store')); ?>" method="POST" class="modal-form">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="modal_doc_create_patient_select">Patient</label>
                        <select id="modal_doc_create_patient_select" name="patient_id" class="form-control <?php $__errorArgs = ['patient_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Sélectionner un patient</option>
                            <?php $__currentLoopData = $patientsForModal ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient_user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($patient_user->id); ?>" <?php echo e(old('patient_id') == $patient_user->id ? 'selected' : ''); ?>><?php echo e($patient_user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['patient_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-modal-target="add-patient-modal" style="font-size: 0.8em; padding: 0.25rem 0.5rem;">
                            + Ajouter un nouveau patient
                        </button>
                    </div>

                    <?php if(Auth::check() && Auth::user()->role->name !== 'doctor'): ?>
                        <div class="form-group">
                            <label for="modal_doc_assign_doctor_select">Assigner au Docteur</label>
                            <select id="modal_doc_assign_doctor_select" name="doctor_id" class="form-control <?php $__errorArgs = ['doctor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Sélectionner un docteur</option>
                                <?php $__currentLoopData = $doctorsForModal ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc_user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($doc_user->id); ?>" <?php echo e(old('doctor_id') == $doc_user->id ? 'selected' : ''); ?>><?php echo e($doc_user->name); ?></option>
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
                    <?php else: ?>
                        <input type="hidden" name="doctor_id" value="<?php echo e(Auth::id()); ?>">
                         <div class="form-group">
                            <label>Docteur</label>
                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->name); ?>" readonly>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="modal_doc_create_date_input">Date</label>
                        <input type="date" id="modal_doc_create_date_input" name="appointment_date" class="form-control <?php $__errorArgs = ['appointment_date'];
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
                        <label for="modal_doc_create_time_select">Heure Disponible</label>
                        <select id="modal_doc_create_time_select" name="appointment_time" class="form-control <?php $__errorArgs = ['appointment_time'];
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
                        <div id="modal_doc_slots_loading" style="display: none; margin-top: 5px;">Chargement...</div>
                        <div id="modal_doc_slots_error" style="display: none; color: red; margin-top: 5px;"></div>
                    </div>

                    <div class="form-group full-width">
                        <label for="modal_doc_create_notes_textarea">Notes (optionnel)</label>
                        <textarea id="modal_doc_create_notes_textarea" name="reason" class="form-control <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('reason')); ?></textarea>
                        <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" form="form-doctor-create-appointment-modal" class="btn">Créer Rendez-vous</button>
            </div>
        </div>
    </div>

    <!-- Create Consultation Modal (Simplified - No Appointment Linking) -->
    <div class="modal-overlay <?php echo e($errors->hasBag('consultationCreate') && session('open_modal_on_load') === 'createConsultationModal' ? 'active' : ''); ?>" id="createConsultationModal">
        <div class="modal modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Consultation</h5>
                <button type="button" class="modal-close" data-modal-dismiss="createConsultationModal">×</button>
            </div>
            <form method="POST" action="<?php echo e(route('doctor.consultations.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                     <?php if($errors->hasBag('consultationCreate')): ?>
                        <div class="alert alert-danger"><ul><?php $__currentLoopData = $errors->consultationCreate->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
                    <?php endif; ?>
                    <div class="modal-form">
                        <div class="form-group"><label for="modal_create_consult_patient_id">Patient *</label><select name="patient_id" id="modal_create_consult_patient_id" class="form-control <?php $__errorArgs = ['patient_id', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><option value="">Sélectionner Patient</option><?php $__currentLoopData = $patientsForModal ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>" <?php echo e(old('patient_id') == $p->id ? 'selected' : ''); ?>><?php echo e($p->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><?php $__errorArgs = ['patient_id', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                        <div class="form-group"><label for="modal_create_consult_consultation_date_time">Date et Heure *</label><input type="datetime-local" name="consultation_date_time" id="modal_create_consult_consultation_date_time" class="form-control <?php $__errorArgs = ['consultation_date_time', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('consultation_date_time', now()->format('Y-m-d\TH:i'))); ?>" required><?php $__errorArgs = ['consultation_date_time', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                        <div class="form-group full-width"><label for="modal_create_consult_reason_for_visit">Motif *</label><input type="text" name="reason_for_visit" id="modal_create_consult_reason_for_visit" class="form-control <?php $__errorArgs = ['reason_for_visit', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('reason_for_visit')); ?>" required><?php $__errorArgs = ['reason_for_visit', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                        <div class="form-group full-width"><label for="modal_create_consult_symptoms">Symptômes *</label><textarea name="symptoms" id="modal_create_consult_symptoms" class="form-control <?php $__errorArgs = ['symptoms', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('symptoms')); ?></textarea><?php $__errorArgs = ['symptoms', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                        <div class="form-group full-width"><label for="modal_create_consult_notes">Notes Docteur</label><textarea name="notes" id="modal_create_consult_notes" class="form-control <?php $__errorArgs = ['notes', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('notes')); ?></textarea><?php $__errorArgs = ['notes', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                        <div class="form-group full-width"><label for="modal_create_consult_diagnosis">Diagnostic</label><textarea name="diagnosis" id="modal_create_consult_diagnosis" class="form-control <?php $__errorArgs = ['diagnosis', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('diagnosis')); ?></textarea><?php $__errorArgs = ['diagnosis', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
            </form>
        </div>
    </div>

    <!-- Edit Consultation Modal (Simplified) -->
    <div class="modal-overlay <?php echo e(session('open_modal_on_load') === 'editConsultationModal' && $errors->hasBag('consultationEdit_' . session('consultation_id_for_error_bag')) ? 'active' : ''); ?>" id="editConsultationModal">
        <div class="modal modal-content">
            <div class="modal-header"><h5 class="modal-title">Modifier Consultation</h5><button type="button" class="modal-close" data-modal-dismiss="editConsultationModal">×</button></div>
            <form method="POST" action="" id="editConsultationForm"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <?php if(session('consultation_id_for_error_bag') && $errors->hasBag('consultationEdit_' . session('consultation_id_for_error_bag'))): ?><div class="alert alert-danger"><ul><?php $__currentLoopData = $errors->getBag('consultationEdit_' . session('consultation_id_for_error_bag'))->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
                    <div id="editConsultationErrorsGeneral" class="alert alert-danger" style="display:none;"><ul></ul></div>
                    <input type="hidden" name="consultation_id_for_error_bag_identifier" id="edit_consultation_id_for_error_bag">
                    <div class="modal-form">
                        <div class="form-group"><label>Patient</label><input type="text" class="form-control" id="edit_consult_patient_name_display" readonly></div>
                        <div class="form-group"><label for="edit_consult_consultation_date_time">Date et Heure *</label><input type="datetime-local" name="consultation_date_time" id="edit_consult_consultation_date_time" class="form-control" value="<?php echo e(old('consultation_date_time')); ?>" required></div>
                        <div class="form-group full-width"><label for="edit_consult_reason_for_visit">Motif *</label><textarea name="reason_for_visit" id="edit_consult_reason_for_visit" class="form-control" rows="2" required><?php echo e(old('reason_for_visit')); ?></textarea></div>
                        <div class="form-group full-width"><label for="edit_consult_symptoms">Symptômes</label><textarea name="symptoms" id="edit_consult_symptoms" class="form-control" rows="3"><?php echo e(old('symptoms')); ?></textarea></div>
                        <div class="form-group full-width"><label for="edit_consult_notes">Notes</label><textarea name="notes" id="edit_consult_notes" class="form-control" rows="3"><?php echo e(old('notes')); ?></textarea></div>
                        <div class="form-group full-width"><label for="edit_consult_diagnosis">Diagnostic</label><textarea name="diagnosis" id="edit_consult_diagnosis" class="form-control" rows="3"><?php echo e(old('diagnosis')); ?></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="editConsultationModal">Annuler</button><button type="submit" class="btn btn-primary">Mettre à Jour</button></div>
            </form>
        </div>
    </div>

    <!-- View Consultation Detail Modal (Simplified) -->
    <div class="modal-overlay" id="viewConsultationDetailModal">
        <div class="modal modal-content">
            <div class="modal-header"><h5 class="modal-title">Détails Consultation</h5><button type="button" class="modal-close" data-modal-dismiss="viewConsultationDetailModal">×</button></div>
            <div class="modal-body">
                <p><strong>Patient:</strong> <span id="view_consult_patient_name"></span></p>
                <p><strong>Date:</strong> <span id="view_consult_date"></span></p>
                <p><strong>RDV Associé:</strong> <span id="view_consult_appointment_info"></span></p> 
                <hr>
                <p><strong>Motif:</strong></p><p id="view_consult_reason" style="white-space:pre-wrap;"></p>
                <p><strong>Symptômes:</strong></p><p id="view_consult_symptoms" style="white-space:pre-wrap;"></p>
                <p><strong>Notes Docteur:</strong></p><p id="view_consult_notes" style="white-space:pre-wrap;"></p>
                <p><strong>Diagnostic:</strong></p><p id="view_consult_diagnosis" style="white-space:pre-wrap;"></p>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="viewConsultationDetailModal">Fermer</button></div>
        </div>
    </div>

    <!-- View Prescription Modal -->
    <div class="modal-overlay" id="viewPrescriptionModal">
        <div class="modal modal-content" style="max-width: 700px;">
            <div class="modal-header"><h5 class="modal-title">Détails Ordonnance</h5><button type="button" class="modal-close" data-modal-dismiss="viewPrescriptionModal">×</button></div>
            <div class="modal-body" id="viewPrescriptionModalBody" style="font-size: 0.9rem;">
                
                <p>Chargement...</p>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="viewPrescriptionModal">Fermer</button></div>
        </div>
    </div>

    <!-- Edit Prescription Modal -->
    <div class="modal-overlay <?php echo e((session('editing_prescription') || (session('prescription_id_for_error_bag') && session('open_modal_on_load') === 'editPrescriptionModal')) ? 'active' : ''); ?>" id="editPrescriptionModal">
        <div class="modal modal-content" style="max-width: 900px;">
            <div class="modal-header"><h5 class="modal-title">Modifier Ordonnance</h5><button type="button" class="modal-close" data-modal-dismiss="editPrescriptionModal">×</button></div>
            <form id="form-edit-prescription" method="POST" action=""><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <?php $editPrescriptionErrorBag = 'prescriptionEdit_' . session('prescription_id_for_error_bag'); ?>
                    <?php if(session('prescription_id_for_error_bag') && $errors->hasBag($editPrescriptionErrorBag)): ?>
                        <div class="alert alert-danger"><strong>Erreurs:</strong><ul><?php $__currentLoopData = $errors->getBag($editPrescriptionErrorBag)->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
                    <?php endif; ?>
                    <div class="modal-form">
                        <div class="form-group"><label for="edit_prescription_patient_id">Patient *</label>
                            <select class="form-control" id="edit_prescription_patient_id" name="patient_id" required>
                                <option value="">Sélectionner Patient</option>
                                <?php $__currentLoopData = $patientsForModal ?? (session('patientsForModal') ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>" <?php echo e((old('patient_id', session('editing_prescription.patient_id') ?? '') == $p->id) ? 'selected' : ''); ?>><?php echo e($p->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group"><label for="edit_prescription_date">Date *</label><input type="date" class="form-control" id="edit_prescription_date" name="prescription_date" value="<?php echo e(old('prescription_date', substr(session('editing_prescription.prescription_date') ?? date('Y-m-d'),0,10))); ?>" required></div>
                        <div class="form-group full-width"><label for="edit_prescription_consultation_id">Consultation Liée</label>
                            <select class="form-control" id="edit_prescription_consultation_id" name="consultation_id"><option value="">-- Aucune --</option></select>
                            <small id="edit_prescription_consultation_loading" style="display:none;">Chargement...</small>
                        </div>
                        <div class="form-group full-width"><label for="edit_prescription_general_notes">Notes Générales</label><textarea class="form-control" id="edit_prescription_general_notes" name="general_notes" rows="2"><?php echo e(old('general_notes', session('editing_prescription.general_notes') ?? '')); ?></textarea></div>
                    </div><hr class="my-3">
                    <h6 class="mb-2">Médicaments</h6>
                    <div id="edit-medication-fields-container">
                        <?php
                            $medsToDisplay = old('medications', session('editing_prescription.items') ?? []);
                        ?>
                        <?php if(!empty($medsToDisplay)): ?>
                            <?php $__currentLoopData = $medsToDisplay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $med): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
                                <input type="hidden" name="medications[<?php echo e($key); ?>][id]" value="<?php echo e($med['id'] ?? ''); ?>">
                                <div class="row gx-2">
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_name_<?php echo e($key); ?>">Nom *</label><input type="text" id="edit_med_name_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][name]" class="form-control form-control-sm" value="<?php echo e($med['medication_name'] ?? ($med['name'] ?? '')); ?>" required></div>
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_dosage_<?php echo e($key); ?>">Dosage</label><input type="text" id="edit_med_dosage_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][dosage]" class="form-control form-control-sm" value="<?php echo e($med['dosage'] ?? ''); ?>"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_freq_<?php echo e($key); ?>">Fréquence</label><input type="text" id="edit_med_freq_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][frequency]" class="form-control form-control-sm" value="<?php echo e($med['frequency'] ?? ''); ?>"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_duration_<?php echo e($key); ?>">Durée</label><input type="text" id="edit_med_duration_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][duration]" class="form-control form-control-sm" value="<?php echo e($med['duration'] ?? ''); ?>"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_notes_<?php echo e($key); ?>">Notes</label><input type="text" id="edit_med_notes_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][notes]" class="form-control form-control-sm" value="<?php echo e($med['notes'] ?? ''); ?>"></div>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-medication-row-btn mt-1">Retirer</button>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php elseif(empty(old('medications')) && !session('editing_prescription.items')): ?>
                             <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
                                <input type="hidden" name="medications[0][id]" value="">
                                <div class="row gx-2">
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_name_0">Nom *</label><input type="text" id="edit_med_name_0" name="medications[0][name]" class="form-control form-control-sm" required></div>
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_dosage_0">Dosage</label><input type="text" id="edit_med_dosage_0" name="medications[0][dosage]" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_freq_0">Fréquence</label><input type="text" id="edit_med_freq_0" name="medications[0][frequency]" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_duration_0">Durée</label><input type="text" id="edit_med_duration_0" name="medications[0][duration]" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_notes_0">Notes</label><input type="text" id="edit_med_notes_0" name="medications[0][notes]" class="form-control form-control-sm"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-edit-medication-row-btn">+ Ajouter Médicament</button>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="editPrescriptionModal">Annuler</button><button type="submit" class="btn btn-primary">Mettre à Jour</button></div>
            </form>
        </div>
    </div>


    <script>
        window.doctorDashboardConfig = {
            routes: {
                logout: "<?php echo e(route('logout')); ?>",
                availableSlots: "<?php echo e(route('appointments.available_slots')); ?>",
                patientDossierBaseUrl: "<?php echo e(url('doctor/patients')); ?>",
                consultationsBaseUrl: "<?php echo e(url('doctor/consultations')); ?>",
                consultationsForLinkingBaseUrl: "<?php echo e(url('doctor/patients')); ?>",
                prescriptionsBaseUrl: "<?php echo e(url('doctor/prescriptions')); ?>"
            },
            session: {
                activeSectionOnLoad: <?php echo json_encode(session('active_section_on_load'), 15, 512) ?>,
                openModalOnLoad: <?php echo json_encode(session('open_modal_on_load'), 15, 512) ?>,
                consultationIdForErrorBag: <?php echo json_encode(session('consultation_id_for_error_bag'), 15, 512) ?>,
                prescriptionIdForErrorBag: <?php echo json_encode(session('prescription_id_for_error_bag'), 15, 512) ?>,
                editingPrescription: <?php echo json_encode(session('editing_prescription'), 15, 512) ?>
            },
            auth: {
                userName: <?php echo json_encode(Auth::user()->name, 15, 512) ?>
            },
            oldInput: {
                appointmentTime: <?php echo json_encode(old('appointment_time', ''), 512) ?>,
                patientId: <?php echo json_encode(old('patient_id'), 15, 512) ?>,
                consultationId: <?php echo json_encode(old('consultation_id'), 15, 512) ?>
            },
            csrfToken: "<?php echo e(csrf_token()); ?>"
        };
    </script>
    <script src="<?php echo e(asset('js/doctor_dashboard.js')); ?>" defer></script>
</body>
</html>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/layouts/doctor_dashboard.blade.php ENDPATH**/ ?>