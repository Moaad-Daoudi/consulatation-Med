<div id="messagerie" class="content-section">
    <div class="messagerie-container">
        <div class="contacts-list">
            {{-- Loop through $conversations --}}
            <div class="contact-item active">
                <div class="contact-name">Dr. Philippe Dupont</div>
                <div class="contact-preview">Je vous transfère le dossier du patient...</div>
            </div>
            <div class="contact-item">
                <div class="contact-name">Dr. Marie Laurent</div>
                <div class="contact-preview">Merci pour votre avis sur ce cas...</div>
            </div>
            {{-- More items --}}
        </div>
        <div class="messages-area">
            <div class="messages-header">
                <h3>Dr. Philippe Dupont</h3> {{-- To be updated by JS --}}
            </div>
            <div class="messages-content">
                {{-- Loop through messages for selected contact --}}
                <div class="message message-received">
                    <div class="message-text">
                        Bonjour Dr. Martin, j'aimerais avoir votre avis sur un patient que je suis actuellement pour des douleurs lombaires chroniques. Seriez-vous disponible pour une consultation?
                    </div>
                    <div class="message-time">10:23</div>
                </div>
                <div class="message message-sent">
                     <div class="message-text">
                        Bonjour Philippe, oui bien sûr. Pouvez-vous me transférer son dossier médical afin que je puisse l'examiner avant notre discussion?
                    </div>
                    <div class="message-time">10:45</div>
                </div>
                {{-- More messages --}}
            </div>
            <div class="message-form">
                <textarea class="message-input" placeholder="Écrivez votre message..."></textarea>
                <button type="button" class="btn send-btn">Envoyer</button>
            </div>
        </div>
    </div>
</div>
