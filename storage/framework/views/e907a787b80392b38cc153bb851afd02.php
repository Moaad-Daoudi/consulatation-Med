<div id="patients" class="content-section">
    <div class="patients-container">
        <div class="patients-header">
            <h2 class="section-title">Mes Patients</h2>
            
            <button type="button" class="btn" data-modal-target="add-patient-modal" id="btn-open-add-patient-modal">+ Nouveau patient</button>
        </div>

        <?php if(isset($doctorPatients) && $doctorPatients->count() > 0): ?>
            <div class="patient-cards-container mt-4">
                <?php $__currentLoopData = $doctorPatients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="patient-card">
                        <div class="patient-card-header">
                            <div class="patient-avatar-sm">
                                <?php echo e(strtoupper(substr($patient->name, 0, 2))); ?>

                            </div>
                            <h5 class="patient-name"><?php echo e($patient->name); ?></h5>
                        </div>
                        <div class="patient-card-body">
                            <p class="patient-info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value"><?php echo e($patient->email); ?></span>
                            </p>
                            
                            
                            <p class="patient-info-item">
                                <span class="info-label">Consultations avec vous:</span>
                                <span class="info-value"><?php echo e($patient->consultations_with_doctor); ?></span>
                            </p>
                            <p class="patient-info-item">
                                <span class="info-label">Ordonnances de vous:</span>
                                <span class="info-value"><?php echo e($patient->prescriptions_from_doctor); ?></span>
                            </p>
                            
                        </div>
                        <div class="patient-card-footer">
                            <button type="button" class="btn btn-sm btn-primary view-patient-dossier-btn"
                                    data-patient-id="<?php echo e($patient->id); ?>"
                                    data-modal-target="viewPatientDossierModal"> 
                                Voir Dossier Complet
                            </button>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <?php if($doctorPatients->hasPages()): ?>
                <div class="mt-4 d-flex justify-content-center">
                    <?php echo e($doctorPatients->appends(request()->except('page'))->links('pagination::bootstrap-4')); ?>

                </div>
            <?php endif; ?>
        <?php else: ?>
            <p class="mt-4 text-center">Aucun patient trouvé ayant eu des consultations ou des ordonnances avec vous.</p>
        <?php endif; ?>
    </div>
</div>


<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/doctor/patients.blade.php ENDPATH**/ ?>