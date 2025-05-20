<div id="consultations" class="content-section">
    <div class="consultations-container">
        <div class="patients-header"> 
            <h2 class="section-title">Consultations Médicales</h2>
            <button type="button" class="btn btn-primary" data-modal-target="createConsultationModal" id="btn-trigger-create-consultation-modal">
                + Nouvelle Consultation
            </button>
        </div>

        <div class="div-table consultations-list mt-3">
            <div class="div-table-header">
                <div class="div-table-cell">Date</div>
                <div class="div-table-cell">Patient</div>
                <div class="div-table-cell">Motif</div>
                <div class="div-table-cell">Actions</div>
            </div>
            <?php if(isset($consultations) && $consultations->count() > 0): ?>
                <?php $__currentLoopData = $consultations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $consultation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="div-table-row consultation-item-row">
                    <div class="div-table-cell consultation-date">
                        <?php echo e($consultation->consultation_date ? \Illuminate\Support\Carbon::parse($consultation->consultation_date)->format('d/m/Y H:i') : 'N/A'); ?>

                    </div>
                    <div class="div-table-cell consultation-patient">
                        <?php echo e($consultation->patient->name ?? 'Patient Inconnu'); ?>

                        <?php if($consultation->appointment_id && $consultation->appointment): ?>
                            <br><small class="text-muted">(Lié au RDV du: <?php echo e(\Illuminate\Support\Carbon::parse($consultation->appointment->appointment_datetime)->format('d/m/Y H:i')); ?>)</small>
                        <?php endif; ?>
                    </div>
                    <div class="div-table-cell consultation-reason">
                        <?php echo e(Str::limit($consultation->reason_for_visit ?? 'N/A', 50)); ?>

                    </div>
                    <div class="div-table-cell consultation-actions">
                        <button type="button" class="btn btn-sm btn-info view-consultation-details-btn"
                                data-modal-target="viewConsultationDetailModal"
                                data-consultation-details="<?php echo e(htmlspecialchars(json_encode($consultation->load(['patient', 'appointment'])), ENT_QUOTES, 'UTF-8')); ?>">
                            Voir
                        </button>
                        <button type="button" class="btn btn-sm btn-warning edit-consultation-btn"
                                data-modal-target="editConsultationModal"
                                data-id="<?php echo e($consultation->id); ?>"
                                data-patient-name="<?php echo e($consultation->patient->name ?? 'N/A'); ?>"
                                data-consultation-date="<?php echo e($consultation->consultation_date->format('Y-m-d\TH:i')); ?>"
                                data-reason-for-visit="<?php echo e(htmlspecialchars($consultation->reason_for_visit ?? '', ENT_QUOTES)); ?>"
                                data-symptoms="<?php echo e(htmlspecialchars($consultation->symptoms ?? '', ENT_QUOTES)); ?>"
                                data-notes="<?php echo e(htmlspecialchars($consultation->notes ?? '', ENT_QUOTES)); ?>"
                                data-diagnosis="<?php echo e(htmlspecialchars($consultation->diagnosis ?? '', ENT_QUOTES)); ?>">
                                
                            Modifier
                        </button>
                        <form action="<?php echo e(route('doctor.consultations.destroy', $consultation->id)); ?>" method="POST" style="display:inline-block;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette consultation ?');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn btn-sm btn-danger" title="Supprimer Consultation">supprimer</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <div class="div-table-row">
                    <div class="div-table-cell" style="text-align:center; padding: 20px; display:block; width:100%;">Aucune consultation trouvée.</div>
                </div>
            <?php endif; ?>
        </div>
        <?php if(isset($consultations) && method_exists($consultations, 'links') && $consultations->hasPages()): ?>
            <div class="mt-3">
                <?php echo e($consultations->appends(request()->except('page', 'consultations_page'))->links('pagination::bootstrap-4')); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/doctor/consultations.blade.php ENDPATH**/ ?>