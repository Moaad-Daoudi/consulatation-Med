<div id="patients" class="content-section">
    <div class="patients-container">
        <div class="patients-header">
            <h2 class="section-title">Liste des patients</h2>
            <button type="button" class="btn" data-modal-target="add-patient-modal" id="btn-open-add-patient-modal">+ Nouveau patient</button>
        </div>
        <table class="patients-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Date de naissance</th>
                    <th>Téléphone</th>
                    <th>Dernière visite</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {{-- Loop through $patients --}}
                <tr>
                    <td>Sophie Dubois</td>
                    <td>12/05/1985</td>
                    <td>06 12 34 56 78</td>
                    <td>05/05/2025</td>
                    <td class="patient-actions">
                        <button class="patient-action-btn" title="Voir dossier">👁️</button>
                        <button class="patient-action-btn" title="Modifier">📝</button>
                        <button class="patient-action-btn" title="Contacter">📞</button>
                    </td>
                </tr>
                <tr>
                    <td>Jean Lefebvre</td>
                    <td>03/11/1970</td>
                    <td>07 98 76 54 32</td>
                    <td>05/05/2025</td>
                    <td class="patient-actions">
                        <button class="patient-action-btn">👁️</button>
                        <button class="patient-action-btn">📝</button>
                        <button class="patient-action-btn">📞</button>
                    </td>
                </tr>
                {{-- More rows --}}
            </tbody>
        </table>
    </div>
</div>
