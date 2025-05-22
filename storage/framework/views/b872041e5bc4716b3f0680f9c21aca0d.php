<div id="patient_medical_file_content" class="content-section">
    
    <div class="medical-file-section-container mb-4">
        <h2 class="section-title">Informations Personnelles</h2>
        <div class="personal-info-grid">
            <div class="info-block">
                <span class="info-label">Nom complet:</span>
                <span class="info-value"><?php echo e(Auth::user()->name ?? 'N/A'); ?></span>
            </div>
            <div class="info-block">
                <span class="info-label">Email:</span>
                <span class="info-value"><?php echo e(Auth::user()->email ?? 'N/A'); ?></span>
            </div>
            <div class="info-block">
                <span class="info-label">Téléphone:</span>
                <span class="info-value"><?php echo e(Auth::user()->phone_number ?? 'Non renseigné'); ?></span>
            </div>
            <div class="info-block">
                <span class="info-label">Date de naissance:</span>
                <span class="info-value">
                    <?php if(Auth::user()->patient && Auth::user()->patient->date_of_birth): ?>
                        <?php echo e(\Carbon\Carbon::parse(Auth::user()->patient->date_of_birth)->format('d/m/Y')); ?>

                    <?php else: ?>
                        Non renseignée
                    <?php endif; ?>
                </span>
            </div>
            <div class="info-block">
                <span class="info-label">Sexe:</span>
                <span class="info-value">
                    <?php if(Auth::user()->patient && Auth::user()->patient->gender): ?>
                        <?php if(Auth::user()->patient->gender === 'male'): ?>
                            Homme
                        <?php elseif(Auth::user()->patient->gender === 'female'): ?>
                            Femme
                        <?php elseif(Auth::user()->patient->gender === 'other'): ?>
                            Autre
                        <?php else: ?>
                            <?php echo e(ucfirst(Auth::user()->patient->gender)); ?> 
                        <?php endif; ?>
                    <?php else: ?>
                        Non renseigné
                    <?php endif; ?>
                </span>
            </div>
            <div class="info-block">
                <span class="info-label">Adresse Postale:</span>
                <span class="info-value">
                    
                    <?php echo e(optional(Auth::user()->patient)->address ?? 'Non renseignée'); ?>

                </span>
            </div>
             <div class="info-block">
                <span class="info-label">Téléphone d'urgence:</span>
                <span class="info-value">
                    <?php echo e(optional(Auth::user()->patient)->emergency_contact ?? 'Non renseigné'); ?>

                </span>
            </div>
        </div>
        <div class="mt-4 text-end">
            
            
            <button type="button" class="btn btn-sm btn-outline-primary"
                    onclick="document.querySelector('a[data-section=\'patient_settings_content\']')?.click();">
                Modifier mes informations
            </button>
        </div>
    </div>

    
    <div class="medical-file-section-container mb-4">
        <h2 class="section-title">Antécédents Médicaux et Consultations</h2>
        <?php if(isset($patientConsultations) && $patientConsultations->count() > 0): ?>
            <?php $__currentLoopData = $patientConsultations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $consultation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="medical-entry-card">
                    <div class="entry-header">
                        <h5>Consultation du <?php echo e(\Carbon\Carbon::parse($consultation->consultation_date)->format('d/m/Y H:i')); ?></h5>
                        <?php if($consultation->doctor): ?>
                            <span class="doctor-name">Avec Dr. <?php echo e($consultation->doctor->name); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="entry-detail">
                        <strong>Motif de la visite:</strong>
                        <div class="detail-content"><?php echo e($consultation->reason_for_visit ?: 'Non spécifié'); ?></div>
                    </div>
                    <?php if($consultation->symptoms): ?>
                        <div class="entry-detail">
                            <strong>Symptômes décrits:</strong>
                            <div class="detail-content"><?php echo e($consultation->symptoms); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if($consultation->notes): ?>
                        <div class="entry-detail">
                            <strong>Notes du médecin:</strong>
                            <div class="detail-content"><?php echo e($consultation->notes); ?></div>
                        </div>
                    <?php endif; ?>
                    <?php if($consultation->diagnosis): ?>
                        <div class="entry-detail">
                            <strong>Diagnostic:</strong>
                            <div class="detail-content"><?php echo e($consultation->diagnosis); ?></div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <p class="text-center py-3">Aucun antécédent de consultation enregistré.</p>
        <?php endif; ?>
    </div>

    
    <div class="medical-file-section-container">
        <h2 class="section-title">Historique des Ordonnances</h2>
        
        
        <?php
            // Combine prescriptions if they are separate, or use a single collection passed from controller
            // This is just an example; adjust based on how you pass data from the controller
            if (isset($activePrescriptions) && isset($pastPrescriptions)) {
                $allPatientPrescriptionsForFile = $activePrescriptions->merge($pastPrescriptions)->sortByDesc('prescription_date');
            } elseif (isset($allPatientPrescriptions)) { // If controller passes 'allPatientPrescriptions'
                 $allPatientPrescriptionsForFile = $allPatientPrescriptions;
            } else {
                $allPatientPrescriptionsForFile = collect();
            }
        ?>

        <?php if($allPatientPrescriptionsForFile->count() > 0): ?>
            <?php $__currentLoopData = $allPatientPrescriptionsForFile; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prescription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="medical-entry-card">
                    <div class="entry-header">
                        <h5>Ordonnance du <?php echo e(\Carbon\Carbon::parse($prescription->prescription_date)->format('d/m/Y')); ?></h5>
                        <?php if($prescription->doctor): ?>
                            <span class="doctor-name">Par Dr. <?php echo e($prescription->doctor->name); ?></span>
                        <?php endif; ?>
                    </div>

                    <?php if($prescription->consultation): ?>
                        <p class="text-muted mb-2" style="font-size:0.9em;">
                            Liée à la consultation du <?php echo e(\Carbon\Carbon::parse($prescription->consultation->consultation_date)->format('d/m/Y')); ?>

                            (Motif: <?php echo e(Str::limit($prescription->consultation->reason_for_visit, 50)); ?>)
                        </p>
                    <?php endif; ?>

                    <?php if($prescription->general_notes): ?>
                        <div class="entry-detail">
                            <strong>Notes générales de l'ordonnance:</strong>
                            <div class="detail-content"><?php echo e($prescription->general_notes); ?></div>
                        </div>
                    <?php endif; ?>

                    <?php if($prescription->items && $prescription->items->count() > 0): ?>
                        <div class="entry-detail mt-3">
                            <strong>Médicaments prescrits:</strong>
                            <ul class="list-unstyled mt-2">
                                <?php $__currentLoopData = $prescription->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="medication-list-item">
                                        <span class="med-name"><?php echo e($item->medication_name); ?></span>
                                        <span class="med-details">
                                            <?php if($item->dosage): ?> Dose: <?php echo e($item->dosage); ?>. <?php endif; ?>
                                            <?php if($item->frequency): ?> Fréquence: <?php echo e($item->frequency); ?>. <?php endif; ?>
                                            <?php if($item->duration): ?> Durée: <?php echo e($item->duration); ?>. <?php endif; ?>
                                        </span>
                                        <?php if($item->notes): ?><span class="med-notes">Notes: <?php echo e($item->notes); ?></span><?php endif; ?>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php else: ?>
                        <p>Aucun médicament listé pour cette ordonnance.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php else: ?>
            <p class="text-center py-3">Aucune ordonnance enregistrée.</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/patient/medical_file.blade.php ENDPATH**/ ?>