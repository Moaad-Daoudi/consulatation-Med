<div id="ordonnances" class="content-section">
    
    <div class="ordonnance-container create-prescription-section mb-5">
        <h2 class="section-title">Créer une nouvelle ordonnance</h2>

        
        <?php if($errors->hasBag('prescriptionCreate') && $errors->getBag('prescriptionCreate')->any()): ?>
            <div class="alert alert-danger mb-3">
                <strong>Erreurs de validation :</strong>
                <ul>
                    <?php $__currentLoopData = $errors->getBag('prescriptionCreate')->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <?php if(session('prescription_error')): ?> 
            <div class="alert alert-danger mb-3"><?php echo e(session('prescription_error')); ?></div>
        <?php endif; ?>


        <form id="form-create-prescription" action="<?php echo e(route('doctor.prescriptions.store')); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <div class="modal-form"> 
                <div class="form-group">
                    <label for="prescription_patient_id">Patient *</label>
                    <select class="form-control <?php $__errorArgs = ['patient_id', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="prescription_patient_id" name="patient_id" required>
                        <option value="">Sélectionner un patient</option>
                        <?php $__currentLoopData = $patientsForModal ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($patient->id); ?>" <?php echo e(old('patient_id') == $patient->id ? 'selected' : ''); ?>>
                                <?php echo e($patient->name); ?> (<?php echo e($patient->email); ?>)
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <?php $__errorArgs = ['patient_id', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="prescription_date">Date de l'Ordonnance *</label>
                    <input type="date" class="form-control <?php $__errorArgs = ['prescription_date', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="prescription_date" name="prescription_date" value="<?php echo e(old('prescription_date', date('Y-m-d'))); ?>" required>
                    <?php $__errorArgs = ['prescription_date', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group full-width">
                    <label for="prescription_consultation_id">Lier à une Consultation (Optionnel)</label>
                    <select class="form-control <?php $__errorArgs = ['consultation_id', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="prescription_consultation_id" name="consultation_id">
                        <option value="">-- Sélectionnez d'abord un patient --</option>
                        
                        
                    </select>
                    <small id="prescription_consultation_loading" style="display:none;">Chargement des consultations...</small>
                    <?php $__errorArgs = ['consultation_id', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>


                <div class="form-group full-width">
                    <label for="prescription_general_notes">Notes Générales (Optionnel)</label>
                    <textarea class="form-control <?php $__errorArgs = ['general_notes', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="prescription_general_notes" name="general_notes" rows="2" placeholder="Instructions générales, allergies..."><?php echo e(old('general_notes')); ?></textarea>
                    <?php $__errorArgs = ['general_notes', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <hr class="my-4">
            <h3 class="section-subtitle mb-3">Médicaments</h3>
            <?php $__errorArgs = ['medications', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="alert alert-danger text-sm p-2"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

            <div id="medication-fields-container">
                <?php if(old('medications')): ?>
                    <?php $__currentLoopData = old('medications'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $med): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
                        <input type="hidden" name="medications[<?php echo e($key); ?>][id]" value="<?php echo e($med['id'] ?? ''); ?>"> 
                        <div class="row"> 
                            <div class="col-md-6 form-group mb-2">
                                <label for="med_name_<?php echo e($key); ?>">Nom du Médicament *</label>
                                <input type="text" id="med_name_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][name]" class="form-control <?php $__errorArgs = ['medications.'.$key.'.name', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Ex: Amoxicilline" value="<?php echo e($med['name'] ?? ''); ?>" required>
                                <?php $__errorArgs = ['medications.'.$key.'.name', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-6 form-group mb-2">
                                <label for="med_dosage_<?php echo e($key); ?>">Dosage/Posologie</label>
                                <input type="text" id="med_dosage_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][dosage]" class="form-control <?php $__errorArgs = ['medications.'.$key.'.dosage', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Ex: 500mg, 1 comprimé" value="<?php echo e($med['dosage'] ?? ''); ?>">
                                <?php $__errorArgs = ['medications.'.$key.'.dosage', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_freq_<?php echo e($key); ?>">Fréquence</label>
                                <input type="text" id="med_freq_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][frequency]" class="form-control <?php $__errorArgs = ['medications.'.$key.'.frequency', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Ex: 3 fois/jour" value="<?php echo e($med['frequency'] ?? ''); ?>">
                                <?php $__errorArgs = ['medications.'.$key.'.frequency', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_duration_<?php echo e($key); ?>">Durée</label>
                                <input type="text" id="med_duration_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][duration]" class="form-control <?php $__errorArgs = ['medications.'.$key.'.duration', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Ex: 7 jours" value="<?php echo e($med['duration'] ?? ''); ?>">
                                <?php $__errorArgs = ['medications.'.$key.'.duration', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_notes_<?php echo e($key); ?>">Notes Spécifiques</label>
                                <input type="text" id="med_notes_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][notes]" class="form-control <?php $__errorArgs = ['medications.'.$key.'.notes', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" placeholder="Ex: Après repas" value="<?php echo e($med['notes'] ?? ''); ?>">
                                <?php $__errorArgs = ['medications.'.$key.'.notes', 'prescriptionCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                        <?php if($loop->index > 0 || count(old('medications')) > 1): ?>
                            <button type="button" class="btn btn-sm btn-danger remove-medication-row-btn mt-2">Retirer ce médicament</button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    
                    <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
                        <div class="row"> 
                            <div class="col-md-6 form-group mb-2">
                                <label for="med_name_0">Nom du Médicament *</label>
                                <input type="text" id="med_name_0" name="medications[0][name]" class="form-control" placeholder="Ex: Amoxicilline" required>
                            </div>
                            <div class="col-md-6 form-group mb-2">
                                <label for="med_dosage_0">Dosage/Posologie</label>
                                <input type="text" id="med_dosage_0" name="medications[0][dosage]" class="form-control" placeholder="Ex: 500mg, 1 comprimé">
                            </div>
                        </div>
                        <div class="row"> 
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_freq_0">Fréquence</label>
                                <input type="text" id="med_freq_0" name="medications[0][frequency]" class="form-control" placeholder="Ex: 3 fois/jour">
                            </div>
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_duration_0">Durée</label>
                                <input type="text" id="med_duration_0" name="medications[0][duration]" class="form-control" placeholder="Ex: 7 jours">
                            </div>
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_notes_0">Notes Spécifiques</label>
                                <input type="text" id="med_notes_0" name="medications[0][notes]" class="form-control" placeholder="Ex: Après repas">
                            </div>
                        </div>
                        
                    </div>
                <?php endif; ?>
            </div>

            <button type="button" class="btn btn-outline-primary mb-3" id="add-medication-row-btn">
                + Ajouter un autre médicament
            </button>

            <div class="form-actions mt-4 d-flex justify-content-end">
                <button type="button" class="btn btn-secondary me-2" id="cancel-create-prescription-btn">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer l'Ordonnance</button>
            </div>
        </form>
    </div>

    <hr class="my-5">

    
    <div class="prescription-history-section">
        <h2 class="section-title">Historique des Ordonnances</h2>
        <div class="div-table prescriptions-list mt-3">
            <div class="div-table-header">
                <div class="div-table-cell">Date</div>
                <div class="div-table-cell">Patient</div>
                <div class="div-table-cell">Nb. Médicaments</div>
                <div class="div-table-cell">Actions</div>
            </div>
            <?php
                // This data should be passed from the main dashboard controller via $prescriptionsForDashboard
                // Using a fallback here if not passed, but prefer passing from controller.
                $prescriptionsToDisplay = $prescriptionsForDashboard ?? \App\Models\Prescription::where('doctor_id', Auth::id())
                                            ->with('patient')
                                            ->withCount('items')
                                            ->latest('prescription_date')
                                            ->paginate(10, ['*'], 'prescriptions_page');
            ?>
            <?php $__empty_1 = true; $__currentLoopData = $prescriptionsToDisplay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $prescription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="div-table-row prescription-item-row">
                <div class="div-table-cell"> <!-- Date -->
                    <?php echo e($prescription->prescription_date->format('d/m/Y')); ?>

                </div>
                <div class="div-table-cell"> <!-- Patient -->
                    <?php echo e($prescription->patient->name ?? 'Patient Inconnu'); ?>

                </div>
                <div class="div-table-cell"> <!-- Nb. Médicaments -->
                    <?php echo e($prescription->items_count); ?>

                </div>
                <div class="div-table-cell prescription-actions">
                    <button type="button" class="btn btn-sm btn-info view-prescription-btn"
                            data-id="<?php echo e($prescription->id); ?>"
                            data-url="<?php echo e(route('doctor.prescriptions.show', $prescription->id)); ?>">
                        Voir
                    </button>
                    <button type="button" class="btn btn-sm btn-warning edit-prescription-btn"
                            data-id="<?php echo e($prescription->id); ?>"
                            data-edit-url="<?php echo e(route('doctor.prescriptions.edit', $prescription->id)); ?>">
                        Modifier
                    </button>
                    <form action="<?php echo e(route('doctor.prescriptions.destroy', $prescription->id)); ?>" method="POST" class="d-inline-block"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ordonnance ?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-danger">Suppr</button>
                    </form>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="div-table-row">
                <div class="div-table-cell" data-empty-row="true">
                    Aucune ordonnance trouvée.
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php if($prescriptionsToDisplay->hasPages()): ?>
            <div class="mt-3 d-flex justify-content-center">
                <?php echo e($prescriptionsToDisplay->appends(request()->except('page'))->links('pagination::bootstrap-4')); ?>

            </div>
        <?php endif; ?>
    </div>
</div>


<template id="medication-row-template">
    <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
        <input type="hidden" name="medications[__INDEX__][id]" value=""> 
        <div class="row"> 
            <div class="col-md-6 form-group mb-2">
                <label for="med_name___INDEX__">Nom du Médicament *</label>
                <input type="text" id="med_name___INDEX__" name="medications[__INDEX__][name]" class="form-control" placeholder="Ex: Amoxicilline" required>
            </div>
            <div class="col-md-6 form-group mb-2">
                <label for="med_dosage___INDEX__">Dosage/Posologie</label>
                <input type="text" id="med_dosage___INDEX__" name="medications[__INDEX__][dosage]" class="form-control" placeholder="Ex: 500mg, 1 comprimé">
            </div>
        </div>
        <div class="row"> 
            <div class="col-md-4 form-group mb-2">
                <label for="med_freq___INDEX__">Fréquence</label>
                <input type="text" id="med_freq___INDEX__" name="medications[__INDEX__][frequency]" class="form-control" placeholder="Ex: 3 fois/jour">
            </div>
            <div class="col-md-4 form-group mb-2">
                <label for="med_duration___INDEX__">Durée</label>
                <input type="text" id="med_duration___INDEX__" name="medications[__INDEX__][duration]" class="form-control" placeholder="Ex: 7 jours">
            </div>
            <div class="col-md-4 form-group mb-2">
                <label for="med_notes___INDEX__">Notes Spécifiques</label>
                <input type="text" id="med_notes___INDEX__" name="medications[__INDEX__][notes]" class="form-control" placeholder="Ex: Après repas">
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-danger remove-medication-row-btn mt-2">Retirer ce médicament</button>
    </div>
</template>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/doctor/ordonnances.blade.php ENDPATH**/ ?>