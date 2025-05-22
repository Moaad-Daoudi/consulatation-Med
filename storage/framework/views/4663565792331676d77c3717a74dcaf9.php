<div id="patient_dashboard_content" class="content-section active">
    <div class="dashboard-stats">
        
        <div class="stat-card card-patient-appointments"> 
            <div class="stat-icon-img-only">
                
                <img src="<?php echo e(asset('assets/dashboard/appointment.png')); ?>" alt="Rendez-vous">
            </div>
            <div class="stat-info">
                <h3><?php echo e($upcomingAppointmentCount ?? 0); ?></h3>
                <p>Prochain(s) rendez-vous</p>
            </div>
        </div>

        
        <div class="stat-card card-patient-prescriptions"> 
            <div class="stat-icon-img-only">
                
                <img src="<?php echo e(asset('assets/dashboard/prescriptions_did.png')); ?>" alt="Ordonnances">
            </div>
            <div class="stat-info">
                <h3><?php echo e($activePrescriptionsCount ?? 0); ?></h3>
                <p>Ordonnance(s) active(s)</p>
            </div>
        </div>
        
    </div>

    
    <div class="content-container">
        <h2 class="section-title">Prochain rendez-vous</h2>
        <?php if($nextAppointment): ?>
            <?php
                $appointmentDateTime = \Carbon\Carbon::parse($nextAppointment->appointment_datetime);
            ?>
            <div class="appointment-item" style="padding: 15px; border: 1px solid #eee; border-radius: 5px;">
                <div class="appointment-time" style="font-weight: bold; margin-bottom: 5px;">
                    <?php echo e($appointmentDateTime->isoFormat('dddd D MMMM YYYY [à] HH[h]mm')); ?>

                    (<?php echo e($appointmentDateTime->diffForHumans()); ?>)
                </div>
                <div class="appointment-doctor" style="margin-bottom: 5px;">
                    Avec: <strong>Dr. <?php echo e($nextAppointment->doctor->name ?? 'N/A'); ?></strong>
                </div>
                <div class="appointment-type" style="font-size: 0.9em; color: #555; margin-bottom: 10px;">
                    Motif: <?php echo e($nextAppointment->notes ?? 'Non spécifié'); ?>

                </div>
                <div class="appointment-status status-<?php echo e(strtolower($nextAppointment->status ?? 'scheduled')); ?>" style="display: inline-block; padding: 3px 8px; border-radius: 15px; font-size: 0.8em; color: white;">
                    <?php echo e(ucfirst($nextAppointment->status ?? 'Prévu')); ?>

                </div>
            </div>
        <?php else: ?>
            <p>Vous n'avez aucun rendez-vous à venir.</p>
        <?php endif; ?>
    </div>

    <div class="content-container">
        <h2 class="section-title">Rappels de médicaments (Ordonnances Actives)</h2>
        <?php if($medicationReminders && $medicationReminders->count() > 0): ?>
            <div class="medication-list">
                <?php $__currentLoopData = $medicationReminders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reminder): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="medication-item">
                    <div class="medication-name"><?php echo e($reminder['name']); ?></div>
                    <div class="medication-dosage">
                        Posologie: <?php echo e($reminder['dosage'] ?? 'N/A'); ?> - <?php echo e($reminder['frequency'] ?? 'N/A'); ?>

                        <br>
                        Durée: <?php echo e($reminder['duration'] ?? 'Selon prescription'); ?>

                        <?php if($reminder['notes']): ?>
                            <br><em>Note: <?php echo e($reminder['notes']); ?></em>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p>Vous n'avez aucun rappel de médicament actif pour le moment.</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/patient/dashboard.blade.php ENDPATH**/ ?>