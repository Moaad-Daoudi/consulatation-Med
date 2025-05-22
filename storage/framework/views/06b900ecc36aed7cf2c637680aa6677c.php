<div id="patient_prescriptions_content" class="content-section">
    <div class="medical-file-section-container mb-4">
        <h2 class="section-title">Mes Ordonnances Actives</h2>
        <?php if(isset($activePrescriptions) && $activePrescriptions->count() > 0): ?>
            <?php $__currentLoopData = $activePrescriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prescription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="medical-entry-card">
                    <div class="entry-header">
                        <h5>Ordonnance du <?php echo e($prescription->prescription_date->format('d/m/Y')); ?></h5>
                        <?php if($prescription->doctor): ?>
                            <span class="doctor-name">Prescrite par Dr. <?php echo e($prescription->doctor->name); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if($prescription->consultation): ?>
                        <p class="text-muted mb-2" style="font-size:0.9em;">
                            Suite à la consultation du <?php echo e(\Carbon\Carbon::parse($prescription->consultation->consultation_date)->format('d/m/Y')); ?>

                            (Motif: <?php echo e(Str::limit($prescription->consultation->reason_for_visit, 40)); ?>)
                        </p>
                    <?php endif; ?>

                    <?php if($prescription->general_notes): ?>
                        <div class="entry-detail">
                            <strong>Notes Générales:</strong>
                            <div class="detail-content"><?php echo e($prescription->general_notes); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if($prescription->items && $prescription->items->count() > 0): ?>
                        <div class="entry-detail mt-3">
                            <strong>Médicaments:</strong>
                            <ul class="list-unstyled mt-2">
                                <?php $__currentLoopData = $prescription->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="medication-list-item">
                                        <span class="med-name"><?php echo e($item->medication_name); ?></span>
                                        <span class="med-details">
                                            <?php if($item->dosage): ?> Dose: <?php echo e($item->dosage); ?>. <?php endif; ?>
                                            <?php if($item->frequency): ?> Fréquence: <?php echo e($item->frequency); ?>. <?php endif; ?>
                                            <?php if($item->duration): ?> Durée: <?php echo e($item->duration); ?>. <?php endif; ?>
                                        </span>
                                        <?php if($item->notes): ?><span class="med-notes">Instructions: <?php echo e($item->notes); ?></span><?php endif; ?>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <p>Aucun médicament spécifique listé pour cette ordonnance (vérifiez les notes générales).</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <p class="text-center py-3">Aucune ordonnance active pour le moment.</p>
        <?php endif; ?>
    </div>

    <div class="medical-file-section-container">
        <h2 class="section-title">Historique des Ordonnances Passées</h2>
        <?php if(isset($pastPrescriptions) && $pastPrescriptions->count() > 0): ?>
            <?php $__currentLoopData = $pastPrescriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prescription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="medical-entry-card">
                    <div class="entry-header">
                        <h5>Ordonnance du <?php echo e($prescription->prescription_date->format('d/m/Y')); ?></h5>
                        <?php if($prescription->doctor): ?>
                            <span class="doctor-name">Prescrite par Dr. <?php echo e($prescription->doctor->name); ?></span>
                        <?php endif; ?>
                    </div>
                     <?php if($prescription->consultation): ?>
                        <p class="text-muted mb-2" style="font-size:0.9em;">
                            Suite à la consultation du <?php echo e(\Carbon\Carbon::parse($prescription->consultation->consultation_date)->format('d/m/Y')); ?>

                            (Motif: <?php echo e(Str::limit($prescription->consultation->reason_for_visit, 40)); ?>)
                        </p>
                    <?php endif; ?>
                    <?php if($prescription->general_notes): ?>
                        <div class="entry-detail">
                            <strong>Notes Générales:</strong>
                            <div class="detail-content"><?php echo e($prescription->general_notes); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if($prescription->items && $prescription->items->count() > 0): ?>
                         <div class="entry-detail mt-3">
                            <strong>Médicaments:</strong>
                            <ul class="list-unstyled mt-2">
                                <?php $__currentLoopData = $prescription->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                     <li class="medication-list-item">
                                        <span class="med-name"><?php echo e($item->medication_name); ?></span>
                                        <span class="med-details">
                                            <?php if($item->dosage): ?> Dose: <?php echo e($item->dosage); ?>. <?php endif; ?>
                                            <?php if($item->frequency): ?> Fréquence: <?php echo e($item->frequency); ?>. <?php endif; ?>
                                            <?php if($item->duration): ?> Durée: <?php echo e($item->duration); ?>. <?php endif; ?>
                                        </span>
                                        <?php if($item->notes): ?><span class="med-notes">Instructions: <?php echo e($item->notes); ?></span><?php endif; ?>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <p class="text-center py-3">Aucun historique d'ordonnances passées.</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/patient/prescriptions.blade.php ENDPATH**/ ?>