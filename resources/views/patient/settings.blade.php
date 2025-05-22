{{-- resources/views/patient/settings.blade.php --}}
<div id="patient_settings_content" class="content-section {{ (session('active_section_on_load') === 'patient_settings_content' || ($errors->any() && Auth::user()->role->name === 'patient')) ? 'active' : '' }}">
    <div class="content-container">
        <h2 class="section-title">Mon Profil Patient</h2>

        {{-- Success Message --}}
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">
                Votre profil a été mis à jour avec succès !
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any() && old('_token') && session('active_section_on_load') === 'patient_settings_content') {{-- Make sure this condition is correct for showing errors --}}
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <strong>Oups !</strong> Il y avait quelques problèmes avec votre saisie :
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="modal-form"
              id="form-patient-settings"
              method="POST"
              action="{{ route('profile.update') }}"
              enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <div class="form-group full-width">
                <label for="patient-setting-name">Nom Complet</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="patient-setting-name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                @error('name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="patient-setting-email">Adresse Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="patient-setting-email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                @error('email') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="patient-setting-phone_number">Numéro de Téléphone</label>
                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="patient-setting-phone_number" name="phone_number" value="{{ old('phone_number', Auth::user()->phone_number) }}">
                @error('phone_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            @if(Auth::user()->role && Auth::user()->role->name === 'patient')
                <div class="form-group">
                    <label for="patient-setting-date_of_birth">Date de Naissance</label>
                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" id="patient-setting-date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', optional(Auth::user()->patient)->date_of_birth) }}" required>
                    @error('date_of_birth') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="patient-setting-gender">Sexe</label>
                    <select class="form-control @error('gender') is-invalid @enderror" id="patient-setting-gender" name="gender" required>
                        <option value="">Sélectionner</option>
                        <option value="male" {{ old('gender', optional(Auth::user()->patient)->gender) == 'male' ? 'selected' : '' }}>Homme</option>
                        <option value="female" {{ old('gender', optional(Auth::user()->patient)->gender) == 'female' ? 'selected' : '' }}>Femme</option>
                        <option value="other" {{ old('gender', optional(Auth::user()->patient)->gender) == 'other' ? 'selected' : '' }}>Autre</option>
                    </select>
                    @error('gender') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="form-group"> {{-- This emergency_contact_phone is in your patients table migration --}}
                    <label for="patient-setting-emergency_contact">Téléphone d'urgence</label>
                    <input type="tel" class="form-control @error('emergency_contact') is-invalid @enderror" id="patient-setting-emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', optional(Auth::user()->patient)->emergency_contact) }}">
                    @error('emergency_contact') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
            @endif

            <div class="form-actions full-width" style="margin-top: 25px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('form-patient-settings').reset(); location.reload();">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer les Modifications</button>
            </div>
        </form>
    </div>
</div>
