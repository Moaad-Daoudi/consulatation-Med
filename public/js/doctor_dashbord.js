// public/js/doctor_dashbord.js

document.addEventListener('DOMContentLoaded', function() {
    // --- OLD NAVIGATION LOGIC - TO BE REMOVED OR COMMENTED OUT ---
    // const menuLinks = document.querySelectorAll('.menu-link');
    // const contentSections = document.querySelectorAll('.content-section');
    // const pageTitle = document.querySelector('.page-title');

    // menuLinks.forEach(link => {
    //     link.addEventListener('click', function(e) {
    //         // !!!!! THIS LINE PREVENTS SERVER-SIDE NAVIGATION !!!!!
    //         // e.preventDefault();

    //         // // Mise à jour des liens du menu - NOW HANDLED BY BLADE
    //         // menuLinks.forEach(item => item.classList.remove('active'));
    //         // this.classList.add('active');

    //         // // Affichage de la section correspondante - NOW HANDLED BY SERVER RENDERING NEW PAGE
    //         // const targetSection = this.getAttribute('data-section');
    //         // contentSections.forEach(section => {
    //         //     section.classList.remove('active');
    //         // });
    //         // document.getElementById(targetSection).classList.add('active');

    //         // // Mise à jour du titre de la page - NOW HANDLED BY BLADE
    //         // pageTitle.textContent = this.querySelector('span').textContent;
    //     });
    // });
    // --- END OF OLD NAVIGATION LOGIC ---


    // Fonctionnalité pour ajouter des médicaments dans l'ordonnance
    // This part can remain as it's specific to the ordonnances page functionality
    const addMedBtn = document.getElementById('add-med');
    const medicationList = document.querySelector('.medication-list');
    const medicamentInput = document.getElementById('medicament');
    const dosageInput = document.getElementById('dosage');
    const posologieInput = document.getElementById('posologie');
    const dureeInput = document.getElementById('duree');

    if (addMedBtn && medicationList && medicamentInput && dosageInput && posologieInput && dureeInput) {
        addMedBtn.addEventListener('click', function() {
            const medicament = medicamentInput.value.trim();
            const dosage = dosageInput.value.trim();
            const posologie = posologieInput.value.trim();
            const duree = dureeInput.value.trim(); // Duree can be optional

            if (medicament && dosage && posologie) {
                const medItem = document.createElement('div');
                medItem.className = 'medication-item';

                let medText = `<span>${medicament}</span> ${dosage} - ${posologie}`;
                if (duree) {
                    medText += ` - ${duree}`;
                }

                medItem.innerHTML = `
                    <div class="medication-info">
                        ${medText}
                    </div>
                    <button type="button" class="remove-med" aria-label="Supprimer ce médicament">❌</button>
                `;
                medicationList.appendChild(medItem);

                // Add remove functionality to the newly added button
                medItem.querySelector('.remove-med').addEventListener('click', function() {
                    this.parentElement.remove();
                });

                // Réinitialiser les champs
                medicamentInput.value = '';
                dosageInput.value = '';
                posologieInput.value = '';
                dureeInput.value = '';
                medicamentInput.focus(); // Put focus back on the first input

            } else {
                // Optionally, provide feedback if fields are missing
                alert("Veuillez remplir les champs Médicament, Dosage et Posologie.");
            }
        });

        // Add event listener for removing items that might be pre-populated
        // or if you decide to re-enable the part that adds multiple listeners.
        // This handles items already in the list on page load if any.
        // Note: This approach adds listeners multiple times if 'addMedBtn' is clicked repeatedly.
        // A better approach for dynamically added items is event delegation,
        // but for simplicity with your current structure, we add it to the new button directly.
        // The below is for any *existing* remove buttons on page load (if any).
        const existingRemoveButtons = document.querySelectorAll('.medication-list .remove-med');
        existingRemoveButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                this.parentElement.remove();
            });
        });
    }
});
