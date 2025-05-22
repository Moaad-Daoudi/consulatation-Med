<div id="patient_appointments_content" class="content-section">
    <div class="content-container">
        <div class="section-title d-flex justify-content-between align-items-center">
            <span>Mes rendez-vous à venir</span>
            <button type="button" class="btn btn-primary btn-sm" data-modal-target="patient-create-appointment-modal">
                + Prendre un nouveau RDV
            </button>
        </div>

        <?php if(isset($upcomingAppointments) && $upcomingAppointments->count() > 0): ?>
            <div class="table-responsive">
                <table class="table patient-appointments-table">
                    <thead>
                        <tr>
                            <th class="appointment-time-header">Date & Heure</th>
                            <th class="appointment-doctor-header">Docteur</th>
                            <th class="appointment-type-header">Motif/Notes</th>
                            <th class="appointment-status-header">Statut</th>
                            <th class="appointment-actions-header">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $upcomingAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="appointment-time">
                                <?php $displayDateTime = $appointment->appointment_datetime ?? null; ?>
                                <?php echo e($displayDateTime ? \Illuminate\Support\Carbon::parse($displayDateTime)->format('d/m/Y H:i') : 'Date N/A'); ?>

                            </td>
                            <td class="appointment-doctor">
                                Dr. <?php echo e($appointment->doctor->name ?? 'N/A'); ?>

                            </td>
                            <td class="appointment-type">
                                <?php $displayNotes = $appointment->notes ?? null; ?>
                                <?php echo e($displayNotes ? Str::limit($displayNotes, 40) : 'Consultation'); ?>

                            </td>
                            <td class="appointment-status-cell">
                                <span class="appointment-status status-<?php echo e($appointment->status ?? 'default'); ?>">
                                    <?php echo e(ucfirst($appointment->status ?? 'Indéfini')); ?>

                                </span>
                            </td>
                            <td class="appointment-actions">
                                <?php
                                    $isCancellableByPatient = false;
                                    $appointmentDateFieldSource = $appointment->appointment_datetime ?? null;

                                    if ($appointmentDateFieldSource && $appointment->status) {
                                        $apptDateTime = \Illuminate\Support\Carbon::parse($appointmentDateFieldSource);
                                        $now = \Illuminate\Support\Carbon::now();
                                        $cancellableStatuses = ['scheduled'];
                                        $statusIsOkayForCancel = in_array(strtolower($appointment->status), $cancellableStatuses);
                                        $isAppointmentInFuture = $apptDateTime->isAfter($now);
                                        $twoHoursFromNow = $now->copy()->addHours(2);
                                        $isFarEnoughInFuture = $apptDateTime->gte($twoHoursFromNow);
                                        if ($statusIsOkayForCancel && $isAppointmentInFuture && $isFarEnoughInFuture) {
                                            $isCancellableByPatient = true;
                                        }
                                    }
                                ?>
                                <?php if($isCancellableByPatient): ?>
                                    <form action="<?php echo e(route('patient.appointments.destroy', $appointment->id)); ?>" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');" style="display:inline-block;">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-warning" title="Annuler ce rendez-vous" style="background-color: red;">Annuler</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>Aucun rendez-vous à venir.</p>
        <?php endif; ?>
    </div>

    <div class="content-container mt-4">
        <h2 class="section-title">Historique des rendez-vous</h2>
        <?php if(isset($pastAppointments) && $pastAppointments->count() > 0): ?>
            <div class="table-responsive">
                <table class="table patient-appointments-table">
                    <thead><tr><th class="appointment-time-header">Date & Heure</th><th class="appointment-doctor-header">Docteur</th><th class="appointment-type-header">Motif/Notes</th><th class="appointment-status-header">Statut</th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $pastAppointments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $appointment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="appointment-time"><?php $dPDT = $appointment->appointment_datetime ?? null; ?> <?php echo e($dPDT ? \Illuminate\Support\Carbon::parse($dPDT)->format('d/m/Y H:i') : 'N/A'); ?></td>
                            <td class="appointment-doctor">Dr. <?php echo e($appointment->doctor->name ?? 'N/A'); ?></td>
                            <td class="appointment-type"><?php $dPN = $appointment->notes ?? null; ?> <?php echo e($dPN ? Str::limit($dPN, 40) : 'Consultation'); ?></td>
                            <td class="appointment-status-cell"><span class="appointment-status status-<?php echo e($appointment->status??'default'); ?>"><?php if($appointment->status === 'completed'): ?>Terminé <?php elseif($appointment->status === 'cancelled'): ?>Annulé <?php else: ?><?php echo e(ucfirst($appointment->status??'Passé')); ?><?php endif; ?></span></td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php if(method_exists($pastAppointments, 'links')): ?> <div class="mt-3"><?php echo e($pastAppointments->links()); ?></div> <?php endif; ?>
        <?php else: ?>
            <p>Aucun rendez-vous dans l'historique.</p>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/patient/appointments.blade.php ENDPATH**/ ?>