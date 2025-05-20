
<div id="dashboard" class="content-section active">
    <div class="dashboard-stats">
        
        <div class="stat-card card-appointments">
            <div class="stat-icon-img-only">
                <img src="<?php echo e(asset('assets/dashboard/appointment.png')); ?>" alt="Rendez-vous">
            </div>
            <div class="stat-info">
                <h3><?php echo e($appointmentsTodayCount ?? 0); ?></h3>
                <p>Rendez-vous aujourd'hui</p>
            </div>
        </div>

        
        <div class="stat-card card-patients">
            <div class="stat-icon-img-only">
                <img src="<?php echo e(asset('assets/dashboard/patients.png')); ?>" alt="Patients">
            </div>
            <div class="stat-info">
                <h3><?php echo e($totalUniquePatientsCount ?? 0); ?></h3>
                <p>Patients uniques (consultés)</p>
            </div>
        </div>

        
        <div class="stat-card card-prescriptions">
            <div class="stat-icon-img-only">
                <img src="<?php echo e(asset('assets/dashboard/prescriptions_did.png')); ?>" alt="Ordonnances">
            </div>
            <div class="stat-info">
                <h3><?php echo e($prescriptionsThisMonthCount ?? 0); ?></h3>
                <p>Ordonnances ce mois</p>
            </div>
        </div>

        
        <div class="stat-card card-messages">
            <div class="stat-icon-img-only">
                <img src="<?php echo e(asset('assets/dashboard/messages.png')); ?>" alt="Messages">
            </div>
            <div class="stat-info">
                <h3><?php echo e($newMessagesCount ?? 0); ?></h3> 
                <p>Nouveaux messages</p>
            </div>
        </div>
    </div>

    
    <div class="content-container recent-activities-container mt-4"> 
        <h2 class="section-title">Activités Récentes</h2>
        <?php if(isset($recentActivities) && $recentActivities->count() > 0): ?>
            <div class="div-table recent-activities-list">
                <div class="div-table-header">
                    <div class="div-table-cell activity-date-col">Date</div>
                    <div class="div-table-cell activity-type-col">Type</div>
                    <div class="div-table-cell activity-patient-col">Patient</div>
                    <div class="div-table-cell activity-desc-col">Description</div>
                    <div class="div-table-cell activity-status-col">Statut</div>
                </div>
                <?php $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="div-table-row">
                        <div class="div-table-cell activity-date-col">
                            <?php if(isset($activity['activity_date'])): ?>
                                <?php echo e(\Carbon\Carbon::parse($activity['activity_date'])->format('d/m/Y H:i')); ?>

                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                        <div class="div-table-cell activity-type-col">
                            <?php if(isset($activity['type'])): ?>
                                <span class="badge activity-type-<?php echo e(Str::slug($activity['type'])); ?>"><?php echo e($activity['type']); ?></span>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                        <div class="div-table-cell activity-patient-col">
                            <?php echo e($activity['patient_name'] ?? 'N/A'); ?>

                        </div>
                        <div class="div-table-cell activity-desc-col">
                            <?php echo e(isset($activity['description']) ? Str::limit($activity['description'], 70) : 'N/A'); ?>

                        </div>
                        <div class="div-table-cell activity-status-col">
                            <?php if(isset($activity['status'])): ?>
                                <span class="appointment-status status-<?php echo e(Str::slug($activity['status'], '-')); ?>">
                                    <?php echo e($activity['status']); ?>

                                </span>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php else: ?>
            <p class="text-center py-3">Aucune activité récente à afficher.</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/doctor/dashboard.blade.php ENDPATH**/ ?>