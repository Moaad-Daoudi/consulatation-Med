<div id="patient_settings_content" class="content-section">
    <div class="content-container">
        <h2 class="section-title">Paramètres du compte</h2>
        {{-- This form should submit to a patient profile update route --}}
        <form class="settings-form" id="form-patient-settings">
            @csrf {{-- If submitting to a Laravel route --}}
            @method('PATCH') {{-- For updates --}}

            <div class="form-group">
                <label for="patient-setting-email">Email</label>
                <input type="email" id="patient-setting-email" name="email" class="form-control" value="{{ Auth::user()->email ?? 'sophie.dubois@email.com' }}">
            </div>
            <div class="form-group">
                <label for="patient-setting-phone">Téléphone</label>
                <input type="tel" id="patient-setting-phone" name="phone" class="form-control" value="{{-- $patientData->phone ?? --}}'06 12 34 56 78'">
            </div>
            <div class="form-group">
                <label for="patient-setting-password">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" id="patient-setting-password" name="password" class="form-control">
            </div>
            <div class="form-group">
                <label for="patient-setting-password-confirm">Confirmer le mot de passe</label>
                <input type="password" id="patient-setting-password-confirm" name="password_confirmation" class="form-control">
            </div>
            <div class="form-group full-width">
                <label for="patient-setting-address">Adresse</label>
                <input type="text" id="patient-setting-address" name="address" class="form-control" value="{{-- $patientData->address ?? --}}'42 rue des Lilas, 75011 Paris'">
            </div>
            <div class="form-group">
                <label for="patient-setting-emergency-contact">Contact d'urgence</label>
                <input type="text" id="patient-setting-emergency-contact" name="emergency_contact_name" class="form-control" value="{{-- $patientData->emergency_contact_name ?? --}}'Pierre Dubois'">
            </div>
            <div class="form-group">
                <label for="patient-setting-emergency-phone">Téléphone d'urgence</label>
                <input type="tel" id="patient-setting-emergency-phone" name="emergency_contact_phone" class="form-control" value="{{-- $patientData->emergency_contact_phone ?? --}}'06 87 65 43 21'">
            </div>
            <div class="form-group full-width">
                <label>Préférences de notification</label>
                <div>
                    <input type="checkbox" id="patient-notif-email" name="notifications[email]" checked>
                    <label for="patient-notif-email">Email</label>
                </div>
                <div>
                    <input type="checkbox" id="patient-notif-sms" name="notifications[sms]" checked>
                    <label for="patient-notif-sms">SMS</label>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary">Annuler</button>
                <button type="submit" class="btn">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>
