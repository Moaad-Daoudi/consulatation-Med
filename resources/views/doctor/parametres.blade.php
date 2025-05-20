{{-- resources/views/doctor/parametres.blade.php --}}
<div id="parametres" class="content-section {{ (session('active_section_on_load') === 'parametres' || $errors->any()) ? 'active' : '' }}">
    <div class="content-container"> {{-- Or ordonnance-container if you prefer that styling --}}
        <h2 class="section-title">Mon Profil et Paramètres</h2>

        {{-- Success Message --}}
        @if (session('status') === 'profile-updated')
            <div class="alert alert-success" role="alert" style="margin-bottom: 20px;">
                Votre profil a été mis à jour avec succès !
            </div>
        @endif

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <strong>Oups !</strong> Il y avait quelques problèmes avec votre saisie :
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="modal-form" {{-- Using modal-form class for potential 2-column layout --}}
              id="form-doctor-profile"
              method="POST"
              action="{{ route('profile.update') }}"
              enctype="multipart/form-data"> {{-- IMPORTANT for file uploads --}}
            @csrf
            @method('PATCH') {{-- Use PATCH for updates --}}

            {{-- User Information --}}
            <div class="form-group full-width">
                <label for="profile-name">Nom Complet</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="profile-name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                @error('name') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="profile-email">Adresse Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="profile-email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                @error('email') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="profile-phone_number">Numéro de Téléphone</label>
                <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="profile-phone_number" name="phone_number" value="{{ old('phone_number', Auth::user()->phone_number) }}">
                @error('phone_number') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Doctor Specific Information --}}
            @if(Auth::user()->role && Auth::user()->role->name === 'doctor')
                <div class="form-group">
                    <label for="profile-specialty">Spécialité</label>
                    <input type="text" class="form-control @error('specialty') is-invalid @enderror" id="profile-specialty" name="specialty" value="{{ old('specialty', Auth::user()->doctor->specialty ?? '') }}" required>
                    @error('specialty') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="profile-practice_address">Adresse du Cabinet</label>
                    <input type="text" class="form-control @error('practice_address') is-invalid @enderror" id="profile-practice_address" name="practice_address" value="{{ old('practice_address', Auth::user()->doctor->practice_address ?? '') }}">
                    @error('practice_address') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full-width">
                    <label for="profile-bio">Biographie / Description</label>
                    <textarea class="form-control @error('bio') is-invalid @enderror" id="profile-bio" name="bio" rows="4">{{ old('bio', Auth::user()->doctor->bio ?? '') }}</textarea>
                    @error('bio') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                </div>
            @endif

            {{-- Photo Upload --}}
            <div class="form-group full-width">
                <label for="profile-photo">Photo de Profil (Max 2Mo : JPG, PNG, GIF)</label>
                @if(Auth::user()->photo_path)
                @else
                    <p><small>Aucune photo de profil actuellement.</small></p>
                @endif
                <input type="file" class="form-control-file @error('photo') is-invalid @enderror" id="profile-photo" name="photo" accept="image/png, image/jpeg, image/gif">
                @error('photo') <span class="text-danger text-sm">{{ $message }}</span> @enderror
            </div>

            {{-- Form Actions --}}
            <div class="form-actions full-width" style="margin-top: 25px;">
                <button type="button" class="btn btn-secondary" onclick="document.getElementById('form-doctor-profile').reset(); location.reload();">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer les Modifications</button>
            </div>
        </form>
    </div>
</div>
