<div id="patient_lab_results_content" class="content-section">
    <div class="content-container">
        <h2 class="section-title">Résultats récents</h2>
        {{-- Loop through $recentLabResults --}}
        <div class="results-card">
            <div class="results-header">
                <div>
                    <strong>Analyse sanguine complète</strong>
                    <div>Laboratoire BioMed Paris</div>
                </div>
                <div>Date: 28/04/2025</div>
            </div>
            <div>
                <strong>Statut:</strong> En attente de consultation
            </div>
            <table class="results-table">
                <thead>
                    <tr><th>Test</th><th>Résultat</th><th>Unité</th><th>Référence</th><th>Statut</th></tr>
                </thead>
                <tbody>
                    <tr><td>Hémoglobine</td><td>13.8</td><td>g/dL</td><td>12.0 - 15.5</td><td class="result-normal">Normal</td></tr>
                    <tr><td>Globules blancs</td><td>10.5</td><td>10^3/µL</td><td>4.5 - 10.0</td><td class="result-abnormal">Élevé</td></tr>
                    {{-- More results --}}
                </tbody>
            </table>
            <div class="prescription-actions"> {{-- Re-using class for button alignment --}}
                <button class="btn btn-sm btn-secondary">Télécharger PDF</button>
            </div>
        </div>
    </div>

    <div class="content-container">
        <h2 class="section-title">Historique des analyses</h2>
        {{-- Loop through $pastLabResults --}}
        <div class="results-card">
            <div class="results-header">
                <div><strong>Tests allergiques cutanés</strong><div>Cabinet Dr. Emma Laurent</div></div>
                <div>Date: 22/03/2025</div>
            </div>
            <div><strong>Résultat:</strong> Réaction positive aux pollens de graminées et d'arbres.</div>
            <div class="prescription-actions">
                <button class="btn btn-sm btn-secondary">Télécharger PDF</button>
            </div>
        </div>
    </div>
</div>
