<div id="appointments" class="content-section">
    <div class="appointments-container">
        <div class="patients-header">
            <h2 class="section-title">Gestion des Rendez-vous</h2>
            <button type="button" class="btn" data-modal-target="doctor-create-appointment-modal" id="btn-open-doctor-create-appt-modal"> + Créer un RDV</button>
        </div>

        <form method="GET" action="<?php echo e(route('dashboard')); ?>#appointments" class="mb-3 form-inline" id="filter-appointments-form">
            <div class="form-group"><label for="filter_date_doc_appt" class="sr-only">Date:</label><input type="date" name="filter_date" id="filter_date_doc_appt" class="form-control form-control-sm" value="<?php echo e(request('filter_date')); ?>"></div>
            <div class="form-group"><label for="filter_period_doc_appt" class="sr-only">Période:</label><select name="filter_period" id="filter_period_doc_appt" class="form-control form-control-sm"><option value="">Filtrer...</option><option value="today" <?php echo e(request('filter_period')=='today'?'selected':''); ?>>Aujourd'hui</option><option value="this_week" <?php echo e(request('filter_period')=='this_week'?'selected':''); ?>>Cette semaine</option><option value="this_month" <?php echo e(request('filter_period')=='this_month'?'selected':''); ?>>Ce mois</option></select></div>
            <button type="submit" class="btn btn-sm btn-primary">Filtrer</button>
            <a href="<?php echo e(route('dashboard')); ?>#appointments" class="btn btn-sm btn-secondary ml-2">Effacer</a>
        </form>

        <div class="div-table appointments-list" id="doctor-appointments-list-container">
            <div class="div-table-header appointment-item-header-row">
                <div class="div-table-cell appointment-time-header">Date & Heure</div>
                <div class="div-table-cell appointment-patient-header">Patient</div>
                <div class="div-table-cell appointment-type-header">Type/Notes</div>
                <div class="div-table-cell appointment-status-header">Statut</div>
                <div class="div-table-cell appointment-actions-header">Actions</div>
            </div>

            <?php $__empty_1 = true; $__currentLoopData = $appointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="div-table-row appointment-item-data-row">
                    <div class="div-table-cell appointment-time">
                        <?php echo e($appointment->appointment_datetime ? \Illuminate\Support\Carbon::parse($appointment->appointment_datetime)->format('d/m/Y H:i') : 'Date N/A'); ?>

                    </div>
                    <div class="div-table-cell appointment-patient">
                        <?php echo e($appointment->patient->name ?? 'Patient Inconnu'); ?>

                    </div>
                    <div class="div-table-cell appointment-type">
                        <?php echo e($appointment->notes ?? $appointment->reason ? Str::limit($appointment->notes ?? $appointment->reason, 30) : 'Consultation'); ?>

                    </div>
                    <div class="div-table-cell appointment-status-cell">
                        <span class="appointment-status <?php if($appointment->status === 'completed'): ?> status-completed <?php elseif(in_array($appointment->status, ['scheduled'])): ?> status-scheduled <?php elseif($appointment->status === 'cancelled'): ?> status-cancelled <?php else: ?> status-default <?php endif; ?>">
                            <?php if($appointment->status === 'completed'): ?> Terminé
                            <?php elseif(in_array($appointment->status, ['scheduled'])): ?> Planifié
                            <?php elseif($appointment->status === 'cancelled'): ?> Annulé
                            <?php else: ?> <?php echo e(ucfirst($appointment->status ?? 'Indéfini')); ?> <?php endif; ?>
                        </span>
                    </div>
                    <div class="div-table-cell appointment-actions">
                        <?php if(in_array($appointment->status, ['scheduled',])): ?>
                            <form action="<?php echo e(route('doctor.appointments.complete', $appointment->id)); ?>" method="POST" style="display:inline-block; margin-right: 5px;">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <button type="submit" class="btn btn-sm btn-icon btn-success-img" title="Marquer comme terminé">
                                    <img src="<?php echo e(asset('assets/dashboard/verifier.png')); ?>" alt="Terminé" class="button-img-icon">
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if(in_array($appointment->status, ['scheduled', 'cancelled'])): ?>
                            <form action="<?php echo e(route('doctor.appointments.destroy', $appointment->id)); ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Êtes-vous sûr de vouloir SUPPRIMER DÉFINITIVEMENT ce rendez-vous ? Cette action est irréversible.');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-icon btn-danger-img" title="Supprimer ce RDV (Action Irréversible)">
                                    <img src="<?php echo e(asset('assets/dashboard/annuler.png')); ?>" alt="Supprimer" class="button-img-icon">
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                 <div class="div-table-row">
                    <div class="div-table-cell" style="text-align:center; padding:20px; grid-column:1 / -1; column-span: all;">
                        Aucun rendez-vous trouvé correspondant à vos filtres.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if(isset($appointments) && method_exists($appointments, 'hasPages') && $appointments->hasPages()): ?>
            <div class="mt-3 d-flex justify-content-center">
                <?php echo e($appointments->appends(request()->query())->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/doctor/appointments.blade.php ENDPATH**/ ?>