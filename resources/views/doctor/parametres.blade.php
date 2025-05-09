<div id="parametres" class="content-section">
    <div class="ordonnance-container"> {{-- Re-using class for layout consistency --}}
        <h2 class="section-title">Profil et Paramètres</h2>
        <form class="ordonnance-form" id="form-doctor-profile"> {{-- For 2-column layout, adjust if needed --}}
            @csrf {{-- If submitting via AJAX or to a Laravel route --}}
            @method('PATCH') {{-- If updating existing profile --}}

            <div class="form-group">
                <label for="profile-nom">Nom</label>
                <input type="text" class="form-control" id="profile-nom" name="nom" value="{{ Auth::user()->name ? explode(' ', Auth::user()->name, 2)[1] ?? '' : 'Martin' }}">
            </div>
            <div class="form-group">
                <label for="profile-prenom">Prénom</label>
                <input type="text" class="form-control" id="profile-prenom" name="prenom" value="{{ Auth::user()->name ? explode(' ', Auth::user()->name, 2)[0] ?? '' : 'Richard' }}">
            </div>
            <div class="form-group">
                <label for="profile-email">Email</label>
                <input type="email" class="form-control" id="profile-email" name="email" value="{{ Auth::user()->email ?? 'dr.martin@mediconsult.fr' }}">
            </div>
            <div class="form-group">
                <label for="profile-telephone">Téléphone</label>
                <input type="tel" class="form-control" id="profile-telephone" name="telephone" value="{{-- Auth::user()->doctor->phone ?? --}}'06 12 34 56 78'">
            </div>
            <div class="form-group">
                <label for="profile-specialite">Spécialité</label>
                <input type="text" class="form-control" id="profile-specialite" name="specialite" value="{{-- Auth::user()->doctor->speciality ?? --}}'Médecine générale'">
            </div>
            <div class="form-group">
                <label for="profile-adresse">Adresse du cabinet</label>
                <input type="text" class="form-control" id="profile-adresse" name="adresse_cabinet" value="{{-- Auth::user()->doctor->address ?? --}}'15 rue de la Santé, 75014 Paris'">
            </div>
            <div class="form-group full-width">
                <label for="profile-biographie">Biographie</label>
                <textarea class="form-control" id="profile-biographie" name="biographie" rows="4">{{-- Auth::user()->doctor->biography ?? --}}Médecin généraliste depuis 15 ans, spécialisé dans la prise en charge des maladies chroniques et la médecine préventive.</textarea>
            </div>
            <div class="form-group">
                <label for="profile-mot-de-passe">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                <input type="password" class="form-control" name="password" id="profile-mot-de-passe">
            </div>
            <div class="form-group">
                <label for="profile-confirmer-mdp">Confirmer le mot de passe</label>
                <input type="password" class="form-control" name="password_confirmation" id="profile-confirmer-mdp">
            </div>
            <div class="form-actions full-width">
                <button type="button" class="btn btn-secondary">Annuler les changements</button>
                <button type="submit" class="btn">Enregistrer les modifications</button>
            </div>
        </form>
    </div>
</div>
