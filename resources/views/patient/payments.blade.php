<div id="patient_payments_content" class="content-section">
    <div class="content-container">
        <h2 class="section-title">Récents paiements</h2>
        <ul class="payment-list">
            {{-- Loop through $recentPayments --}}
            <li class="payment-item">
                <div class="payment-info">
                    <div class="payment-date">01/05/2025</div>
                    <div class="payment-details">Consultation Dr. Richard Martin</div>
                </div>
                <div class="payment-amount">25,00 €</div>
                <div class="payment-status status-paid">Payé</div>
            </li>
            <li class="payment-item">
                <div class="payment-info">
                    <div class="payment-date">28/04/2025</div>
                    <div class="payment-details">Analyse sanguine - Laboratoire BioMed</div>
                </div>
                <div class="payment-amount">35,50 €</div>
                <div class="payment-status status-pending">En attente</div>
            </li>
        </ul>
    </div>

    <div class="content-container">
        <h2 class="section-title">Remboursements</h2>
        <ul class="payment-list">
            {{-- Loop through $refunds --}}
            <li class="payment-item">
                <div class="payment-info">
                    <div class="payment-date">20/04/2025</div>
                    <div class="payment-details">Sécurité sociale - Consultation du 15/04</div>
                </div>
                <div class="payment-amount">17,50 €</div>
                <div class="payment-status status-received">Reçu</div> {{-- Changed to a more appropriate class --}}
            </li>
        </ul>
    </div>
</div>
