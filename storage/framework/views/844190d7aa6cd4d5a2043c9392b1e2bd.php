
<div id="patient_settings_content" class="content-section <?php echo e((session('active_section_on_load') === 'patient_settings_content' || ($errors->any() && Auth::user()->role->name === 'patient')) ? 'active' : ''); ?>">
    <div class="content-container">
        <h2 class="section-title">Mon Profil Patient</h2>

        
        <?php if(session('status') === 'profile-updated'): ?>
            <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">
                Votre profil a été mis à jour avec succès !
            </div>
        <?php endif; ?>

        
        <?php if($errors->any() && old('_token') && session('active_section_on_load') === 'patient_settings_content'): ?> 
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <strong>Oups !</strong> Il y avait quelques problèmes avec votre saisie :
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form class="modal-form"
              id="form-patient-settings"
              method="POST"
              action="<?php echo e(route('profile.update')); ?>"
              enctype="multipart/form-data">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>

            <div class="form-group full-width">
                <label for="patient-setting-name">Nom Complet</label>
                <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="patient-setting-name" name="name" value="<?php echo e(old('name', Auth::user()->name)); ?>" required>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label for="patient-setting-email">Adresse Email</label>
                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="patient-setting-email" name="email" value="<?php echo e(old('email', Auth::user()->email)); ?>" required>
                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="form-group">
                <label for="patient-setting-phone_number">Numéro de Téléphone</label>
                <input type="tel" class="form-control <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="patient-setting-phone_number" name="phone_number" value="<?php echo e(old('phone_number', Auth::user()->phone_number)); ?>">
                <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <?php if(Auth::user()->role && Auth::user()->role->name === 'patient'): ?>
                <div class="form-group">
                    <label for="patient-setting-date_of_birth">Date de Naissance</label>
                    <input type="date" class="form-control <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="patient-setting-date_of_birth" name="date_of_birth" value="<?php echo e(old('date_of_birth', optional(Auth::user()->patient)->date_of_birth)); ?>" required>
                    <?php $__errorArgs = ['date_of_birth'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="form-group">
                    <label for="patient-setting-gender">Sexe</label>
                    <select class="form-control <?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="patient-setting-gender" name="gender" required>
                        <option value="">Sélectionner</option>
                        <option value="male" <?php echo e(old('gender', optional(Auth::user()->patient)->gender) == 'male' ? 'selected' : ''); ?>>Homme</option>
                        <option value="female" <?php echo e(old('gender', optional(Auth::user()->patient)->gender) == 'female' ? 'selected' : ''); ?>>Femme</option>
                        <option value="other" <?php echo e(old('gender', optional(Auth::user()->patient)->gender) == 'other' ? 'selected' : ''); ?>>Autre</option>
                    </select>
                    <?php $__errorArgs = ['gender'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="form-group"> 
                    <label for="patient-setting-emergency_contact">Téléphone d'urgence</label>
                    <input type="tel" class="form-control <?php $__errorArgs = ['emergency_contact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="patient-setting-emergency_contact" name="emergency_contact" value="<?php echo e(old('emergency_contact', optional(Auth::user()->patient)->emergency_contact)); ?>">
                    <?php $__errorArgs = ['emergency_contact'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            <?php endif; ?>

            <div class="form-actions full-width" style="margin-top: 25px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('form-patient-settings').reset(); location.reload();">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer les Modifications</button>
            </div>
        </form>
    </div>
</div>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/patient/settings.blade.php ENDPATH**/ ?>