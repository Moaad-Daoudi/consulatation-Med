<div id="ordonnances" class="content-section">
    <div class="ordonnance-container">
        <h2 class="section-title">Créer une nouvelle ordonnance</h2>
        <form class="ordonnance-form" id="form-create-ordonnance">
            <div class="form-group">
                <label for="ordonnance-patient-select">Patient</label>
                <select class="form-control" id="ordonnance-patient-select" name="patient_id_ordonnance">
                    <option value="">Sélectionner un patient</option>
                    <option value="1">Sophie Dubois</option>
                    <option value="2">Jean Lefebvre</option>
                    {{-- Populate dynamically --}}
                </select>
            </div>
            <div class="form-group">
                <label for="date_ordonnance_creation">Date</label>
                <input type="date" class="form-control" id="date_ordonnance_creation" name="date_ordonnance" value="{{ date('Y-m-d') }}">
            </div>
            <div class="form-group full-width">
                <label for="notes_generales_ordonnance">Notes générales</label>
                <textarea class="form-control" id="notes_generales_ordonnance" name="notes_ordonnance" placeholder="Notes concernant l'ordonnance..."></textarea>
            </div>
            <div class="form-group">
                <label for="medicament_name_ord">Médicament</label>
                <input type="text" class="form-control" id="medicament_name_ord" placeholder="Nom du médicament">
            </div>
            <div class="form-group">
                <label for="dosage_ord">Dosage</label>
                <input type="text" class="form-control" id="dosage_ord" placeholder="Dosage">
            </div>
            <div class="form-group">
                <label for="frequency_ord">Fréquence</label>
                <input type="text" class="form-control" id="frequency_ord" placeholder="Fréquence de prise">
            </div>
            <div class="form-group">
                <label for="duration_ord">Durée</label>
                <input type="text" class="form-control" id="duration_ord" placeholder="Durée du traitement">
            </div>
            <div class="form-group full-width">
                <button type="button" class="btn" id="add-med-btn">+ Ajouter médicament</button>
            </div>
            <div class="form-group full-width">
                <div class="medication-list">
                    {{-- Dynamically added items will appear here --}}
                    <div class="medication-item"> {{-- Example initial item --}}
                        <div class="medication-info">
                            <span>Doliprane 1000mg</span> - 1 comprimé, 3 fois par jour pendant 5 jours
                        </div>
                        <div class="remove-med" role="button">❌</div>
                    </div>
                </div>
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-secondary">Annuler</button>
                <button type="submit" class="btn">Générer ordonnance</button>
            </div>
        </form>
    </div>
</div>
