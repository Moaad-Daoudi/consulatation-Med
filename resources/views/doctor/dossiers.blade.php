<div id="dossiers" class="content-section">
    <div class="dossiers-container">
        <h2 class="section-title">Dossiers médicaux</h2>
        <div class="dossier-search form-group">
            <input type="text" class="form-control" placeholder="Rechercher un patient...">
        </div>
        <ul class="dossier-list">
            {{-- Loop through $dossiers or $patientsWithDossiers --}}
            <li class="dossier-item">
                <div class="dossier-info">
                    <div class="dossier-patient">Sophie Dubois</div>
                    <div class="dossier-details">Dossier #12345 - Dernière mise à jour: 05/05/2025</div>
                </div>
                <button class="btn btn-sm">Consulter</button>
            </li>
            <li class="dossier-item">
                <div class="dossier-info">
                    <div class="dossier-patient">Jean Lefebvre</div>
                    <div class="dossier-details">Dossier #12346 - Dernière mise à jour: 05/05/2025</div>
                </div>
                <button class="btn btn-sm">Consulter</button>
            </li>
            {{-- More items --}}
        </ul>
    </div>
</div>
