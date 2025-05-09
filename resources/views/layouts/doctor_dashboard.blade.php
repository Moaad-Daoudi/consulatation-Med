<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MediConsult - Dashboard Médecin</title>
    <style>
        /* PASTE ALL CSS from your original dashboard_medecin.html HERE */
        /* This includes :root variables, base styles, layout, sidebar, topbar, modals, etc. */
        :root {
            --primary: #1976d2; --primary-light: #4791db; --primary-dark: #115293;
            --secondary: #43a047; --secondary-light: #76d275; --secondary-dark: #2d7031;
            --danger: #e53935; --warning: #ffb74d; --text-dark: #333; --text-light: #f5f5f5;
            --bg-light: #f8f9fa; --bg-white: #ffffff; --shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); line-height: 1.6; }
        a { text-decoration: none; color: var(--primary); cursor: pointer; }
        .dashboard-layout { display: flex; height: 100vh; overflow: hidden; }

        /* Sidebar */
        .sidebar { width: 280px; background-color: var(--primary-dark); color: var(--text-light); height: 100%; overflow-y: auto; position: fixed; left: 0; top: 0; z-index: 100; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo { font-size: 24px; font-weight: bold; }
        .logo span { color: var(--secondary-light); }
        .user-info { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .user-avatar { width: 80px; height: 80px; border-radius: 50%; background-color: var(--primary-light); margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; font-size: 2rem; text-transform: uppercase; }
        .user-name { font-weight: bold; margin-bottom: 5px; }
        .user-role { font-size: 0.9rem; opacity: 0.8; text-transform: capitalize; }
        .sidebar-menu { list-style: none; padding: 20px 0; flex-grow: 1; }
        .sidebar-menu li { margin-bottom: 5px; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 12px 20px; color: var(--text-light); transition: all 0.3s ease; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background-color: rgba(255,255,255,0.1); }
        .menu-icon { margin-right: 10px; font-size: 1.2rem; }

        /* Main Content & Topbar */
        .main-content { flex: 1; margin-left: 280px; padding: 0; overflow-y: auto; height: 100vh; display: flex; flex-direction: column; }
        .topbar { display: flex; justify-content: space-between; align-items: center; background-color: var(--bg-white); box-shadow: var(--shadow); padding: 15px 20px; position: sticky; top: 0; z-index: 90; }
        .page-title { font-size: 1.5rem; font-weight: 500; }
        .topbar-actions { display: flex; gap: 15px; align-items: center; }
        .notification-bell { position: relative; cursor: pointer; width: 40px; height: 40px; background-color: var(--bg-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .notification-badge { position: absolute; top: -5px; right: -5px; background-color: var(--danger); color: var(--text-light); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; }
        .user-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .user-profile-img { width: 40px; height: 40px; border-radius: 50%; background-color: var(--primary); color: var(--text-light); display: flex; align-items: center; justify-content: center; font-weight: bold; text-transform: uppercase; }
        .content-wrapper { padding: 20px; flex-grow: 1; overflow-y: auto; }

        /* Content Sections (SPA style) */
        .content-section { display: none; }
        .content-section.active { display: block; }

        /* All other component styles from your dashboard_medecin.html */
        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 20px; display: flex; align-items: center; }
        .stat-icon { width: 60px; height: 60px; border-radius: 10px; background-color: rgba(25, 118, 210, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-right: 20px; }
        .stat-icon.patients { background-color: rgba(67, 160, 71, 0.1); color: var(--secondary); }
        .stat-icon.appointments { background-color: rgba(255, 183, 77, 0.1); color: var(--warning); }
        .stat-icon.messages { background-color: rgba(229, 57, 53, 0.1); color: var(--danger); }
        .stat-info h3 { font-size: 1.8rem; margin-bottom: 5px; }
        .stat-info p { color: #777; font-size: 0.9rem; }
        .ordonnance-container, .appointments-container, .patients-container, .consultations-container, .dossiers-container { background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 25px; margin-bottom: 30px; }
        .section-title { font-size: 1.3rem; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        textarea.form-control { min-height: 120px; resize: vertical; }
        .form-group.full-width { grid-column: span 2; }
        .btn { padding: 12px 20px; background-color: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; transition: background-color 0.3s; }
        .btn:hover { background-color: var(--primary-dark); }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn-sm { padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; }
        .btn-outline-primary { color: var(--primary); border-color: var(--primary); background-color: transparent;}
        .btn-outline-primary:hover { color: #fff; background-color: var(--primary); border-color: var(--primary); }
        .mt-2 { margin-top: .5rem !important; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px; }
        .medication-list { margin-top: 20px; }
        .medication-item { display: flex; background-color: var(--bg-light); border-radius: 5px; padding: 10px 15px; margin-bottom: 10px; justify-content: space-between; align-items: center; }
        .medication-info span { font-weight: bold; margin-right: 10px; }
        .remove-med { color: var(--danger); cursor: pointer; }
        .appointments-list { margin-top: 20px; }
        .appointment-item { display: grid; grid-template-columns: auto 1fr auto auto; gap:15px; padding: 15px; border-bottom: 1px solid #eee; align-items: center; }
        .appointment-item:last-child { border-bottom: none; }
        .appointment-time { font-weight: bold; }
        .appointment-patient { font-weight: 500; }
        .appointment-type { color: #666; }
        .appointment-status { padding: 5px 10px; border-radius: 20px; text-align: center; font-size: 0.8rem; font-weight: 500; }
        .status-scheduled { background-color: var(--primary-light); color: white; }
        .status-completed { background-color: var(--secondary-light); color: white; }
        .status-cancelled { background-color: var(--danger); color: white; }
        .patients-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .patients-table { width: 100%; border-collapse: collapse; }
        .patients-table th { text-align: left; padding: 12px 15px; border-bottom: 2px solid #ddd; background-color: var(--bg-light); }
        .patients-table td { padding: 12px 15px; border-bottom: 1px solid #eee; }
        .patient-actions { display: flex; gap: 10px; }
        .patient-action-btn { background-color: var(--bg-light); border: 1px solid #ddd; border-radius: 5px; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; cursor: pointer; }
        .patient-action-btn:hover { background-color: #e9e9e9;}
        .consultation-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .consultation-header { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .consultation-patient { font-weight: bold; }
        .consultation-date { color: #666; }
        .consultation-details { margin-bottom: 10px; }
        .consultation-actions { display: flex; justify-content: flex-end; gap: 10px; }
        .dossier-search { margin-bottom: 20px; }
        .dossier-list { list-style: none; padding-left: 0; }
        .dossier-item { padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .dossier-item:last-child { border-bottom: none; }
        .dossier-info { flex: 1; }
        .dossier-patient { font-weight: bold; margin-bottom: 5px; }
        .dossier-details { color: #666; font-size: 0.9rem; }
        .messagerie-container { display: grid; grid-template-columns: 300px 1fr; gap: 20px; height: calc(100vh - 220px); background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 25px; margin-bottom: 30px; }
        .contacts-list { border-right: 1px solid #eee; overflow-y: auto; padding-right: 10px;}
        .contact-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; }
        .contact-item:last-child { border-bottom: none; }
        .contact-item.active, .contact-item:hover { background-color: var(--bg-light); }
        .contact-name { font-weight: bold; margin-bottom: 5px; }
        .contact-preview { color: #666; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .messages-area { display: flex; flex-direction: column; height: 100%; }
        .messages-header { padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .messages-header h3 { margin:0; font-size: 1.2em; }
        .messages-content { flex: 1; overflow-y: auto; padding: 15px 0; display: flex; flex-direction: column; }
        .message { max-width: 70%; padding: 10px 15px; border-radius: 10px; margin-bottom: 10px; line-height: 1.4; }
        .message-text { word-wrap: break-word; }
        .message-received { background-color: var(--bg-light); align-self: flex-start; }
        .message-sent { background-color: var(--primary-light); color: white; align-self: flex-end; }
        .message-time { font-size: 0.8rem; text-align: right; margin-top: 5px; opacity: 0.8; }
        .message-form { display: flex; border-top: 1px solid #eee; padding-top: 15px; }
        .message-input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; margin-right: 10px; }
        .send-btn { padding: 10px 20px; }
        .ordonnance-form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }

        /* Modals */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .modal-overlay.active { display: flex; opacity: 1; visibility: visible; }
        .modal { width: 90%; max-width: 800px; background-color: var(--bg-white); border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); overflow: hidden; transform: translateY(-20px) scale(0.95); transition: transform 0.3s ease, opacity 0.3s ease; opacity: 0; }
        .modal-overlay.active .modal { transform: translateY(0) scale(1); opacity: 1; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 20px 25px; border-bottom: 1px solid #eee; background-color: var(--primary); color: white; }
        .modal-title { font-size: 1.3rem; font-weight: 500; }
        .modal-close { font-size: 1.5rem; cursor: pointer; background: none; border: none; color: white; }
        .modal-body { padding: 25px; max-height: 70vh; overflow-y: auto; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 15px 25px; border-top: 1px solid #eee; background-color: var(--bg-light); }
        .modal-form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .modal-form .form-group.full-width { grid-column: span 2; }

        /* Styles for Appointment Filters (if used) */
        .form-inline .form-group { margin-right: 10px; margin-bottom: 10px; display:inline-flex; align-items:center; }
        .form-inline .form-control-sm { height: calc(1.5em + .5rem + 2px); padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; }
        .ml-2 { margin-left: .5rem !important; }
        .mb-3 { margin-bottom: 1rem !important; }
        .alert { padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="logo">Medi<span>Consult</span></a>
            </div>
            @auth
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                <div class="user-name">{{ Auth::user()->name }}</div>
                @if(Auth::user()->role)
                <div class="user-role">{{ ucfirst(Auth::user()->role->name) }}</div>
                @endif
            </div>
            @endauth
            <ul class="sidebar-menu">
                <li><a href="#" class="menu-link active" data-section="dashboard"><div class="menu-icon">📊</div><span>Tableau de bord</span></a></li>
                <li><a href="#" class="menu-link" data-section="appointments"><div class="menu-icon">📅</div><span>Rendez-vous</span></a></li>
                <li><a href="#" class="menu-link" data-section="patients"><div class="menu-icon">👥</div><span>Patients</span></a></li>
                <li><a href="#" class="menu-link" data-section="consultations"><div class="menu-icon">🗣</div><span>Consultations</span></a></li>
                <li><a href="#" class="menu-link" data-section="dossiers"><div class="menu-icon">📁</div><span>Dossiers médicaux</span></a></li>
                <li><a href="#" class="menu-link" data-section="ordonnances"><div class="menu-icon">💊</div><span>Ordonnances</span></a></li>
                <li><a href="#" class="menu-link" data-section="messagerie"><div class="menu-icon">💬</div><span>Messagerie</span></a></li>
                <li><a href="#" class="menu-link" data-section="parametres"><div class="menu-icon">⚙️</div><span>Profile</span></a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form-doctor-dashboard" style="display: none;">@csrf</form>
                    <a href="{{ route('logout') }}" class="menu-link"
                       onclick="event.preventDefault(); document.getElementById('logout-form-doctor-dashboard').submit();">
                        <div class="menu-icon">🚪</div><span>Déconnexion</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <h1 class="page-title" id="dynamicPageTitle">Tableau de bord</h1>
                @auth
                <div class="topbar-actions">
                    <div class="notification-bell">🔔<span class="notification-badge">3</span></div>
                    <div class="user-profile">
                        <div class="user-profile-img">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                        <span>{{ Str::before(Auth::user()->name, ' ') }}</span>
                    </div>
                </div>
                @endauth
            </div>

            <div class="content-wrapper">
                {{-- Session Messages --}}
                @if(session('success'))
                    <div class="alert alert-success" role="alert">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
                @endif
                {{-- Include all content sections here --}}
                @include('doctor.dashboard')
                @include('doctor.appointments')
                @include('doctor.patients')
                @include('doctor.consultations')
                @include('doctor.dossiers')
                @include('doctor.ordonnances')
                @include('doctor.messagerie')
                @include('doctor.parametres')
            </div>
        </main>
    </div>

    {{-- MODALS (Basic placeholders as per original structure, before complex appointment logic) --}}
    <!-- Modal pour ajouter un nouveau patient -->
    <div class="modal-overlay" id="add-patient-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Ajouter un nouveau patient</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                <form class="modal-form" id="form-add-patient-modal-placeholder">
                    <p>Formulaire d'ajout de patient ici...</p>
                    {{-- Basic fields for a new patient --}}
                    <div class="form-group"><label for="placeholder-patient-nom">Nom</label><input type="text" class="form-control" id="placeholder-patient-nom"></div>
                    <div class="form-group"><label for="placeholder-patient-prenom">Prénom</label><input type="text" class="form-control" id="placeholder-patient-prenom"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="button" class="btn">Enregistrer Patient (Placeholder)</button>
            </div>
        </div>
    </div>

    <!-- Modal pour que le DOCTEUR crée un nouveau rendez-vous (Placeholder) -->
    <div class="modal-overlay" id="doctor-create-appointment-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Créer un Nouveau Rendez-vous</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                <form id="form-doctor-create-appointment-modal-placeholder" class="modal-form">
                     <p>Formulaire de création de rendez-vous par le docteur ici...</p>
                    <div class="form-group">
                        <label for="placeholder-rdv-patient">Patient</label>
                        <select class="form-control" id="placeholder-rdv-patient"><option value="">Sélectionner un patient</option></select>
                    </div>
                    <div class="form-group">
                        <label for="placeholder-rdv-date">Date</label>
                        <input type="date" class="form-control" id="placeholder-rdv-date">
                    </div>
                    <div class="form-group full-width">
                        <label for="placeholder-rdv-heure">Heure</label>
                        <select class="form-control" id="placeholder-rdv-heure"><option value="">Sélectionner heure</option></select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="button" class="btn">Créer Rendez-vous (Placeholder)</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- SPA Navigation Logic ---
        const menuLinks = document.querySelectorAll('.sidebar-menu .menu-link');
        const contentSections = document.querySelectorAll('.content-wrapper .content-section');
        const pageTitleElement = document.getElementById('dynamicPageTitle');

        function activateSection(sectionId) {
            contentSections.forEach(section => {
                section.classList.toggle('active', section.id === sectionId);
            });
        }

        menuLinks.forEach(link => {
            const isLogoutLink = link.getAttribute('href') === "{{ route('logout') }}" || (link.onclick && link.onclick.toString().includes('logout-form'));
            // For this reset, 'parametres' is an SPA section
            const isExternalProfile = link.getAttribute('data-section') === 'profile_page_link'; // If you had a specific data-section for external profile

            if (isLogoutLink || isExternalProfile ) return;

            link.addEventListener('click', function(e) {
                e.preventDefault();
                menuLinks.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
                const targetSectionId = this.getAttribute('data-section');
                activateSection(targetSectionId);
                if (pageTitleElement && this.querySelector('span')) {
                    pageTitleElement.textContent = this.querySelector('span').textContent;
                }
            });
        });
        let defaultActiveLink = document.querySelector('.sidebar-menu .menu-link.active');
        if (!defaultActiveLink && menuLinks.length > 0) { defaultActiveLink = menuLinks[0]; defaultActiveLink.classList.add('active');}
        if (defaultActiveLink) {
            activateSection(defaultActiveLink.getAttribute('data-section'));
            if (pageTitleElement && defaultActiveLink.querySelector('span')) pageTitleElement.textContent = defaultActiveLink.querySelector('span').textContent;
        }

        // --- General Modal Toggle Logic ---
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', () => {
                const modal = document.getElementById(button.getAttribute('data-modal-target'));
                if (modal) modal.classList.add('active');
            });
        });
        document.querySelectorAll('.modal-close, .modal-close-btn').forEach(button => {
            button.addEventListener('click', () => {
                const modal = button.closest('.modal-overlay');
                if (modal) modal.classList.remove('active');
            });
        });
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('active'); });
        });

        // --- Placeholder JS for specific components (to be expanded later) ---
        // Example: Add Medication
        const addMedBtn = document.getElementById('add-med-btn'); // In ordonnances.blade.php
        if (addMedBtn) {
            addMedBtn.addEventListener('click', () => {
                console.log("Add medication button clicked - implement logic.");
                // Placeholder: append a simple item, real implementation needs input fields
                const medicationList = document.querySelector('#ordonnances .medication-list');
                if(medicationList) {
                    const newItem = document.createElement('div');
                    newItem.className = 'medication-item';
                    newItem.innerHTML = '<div class="medication-info"><span>Nouveau Médicament</span> - Dosage...</div><div class="remove-med">❌</div>';
                    medicationList.appendChild(newItem);
                    newItem.querySelector('.remove-med').addEventListener('click', function() {this.parentElement.remove();});
                }
            });
        }
        document.querySelectorAll('#ordonnances .medication-list .remove-med').forEach(btn => {
             btn.addEventListener('click', function() { this.parentElement.remove(); });
        });


        // Example: Messagerie Contact Click
        const contactItems = document.querySelectorAll('#messagerie .contact-item');
        const messagesHeaderNameEl = document.querySelector('#messagerie .messages-header h3');
        const messagesContentEl = document.querySelector('#messagerie .messages-content');
        const messageForm = document.querySelector('#messagerie .message-form');

        if (contactItems.length > 0 && messagesHeaderNameEl && messagesContentEl && messageForm) {
            contactItems.forEach(item => {
                item.addEventListener('click', function() {
                    contactItems.forEach(c => c.classList.remove('active')); this.classList.add('active');
                    messagesHeaderNameEl.textContent = this.querySelector('.contact-name').textContent;
                    messagesContentEl.innerHTML = '<p style="text-align:center;color:#777;margin-top:20px;">Messages pour ' + messagesHeaderNameEl.textContent + '...</p>';
                    messageForm.style.display = 'flex';
                });
            });
            const sendBtn = document.querySelector('#messagerie .send-btn');
            const input = document.querySelector('#messagerie .message-input');
            if(sendBtn && input){
                sendBtn.addEventListener('click', () => {
                    if(input.value.trim()) {
                        console.log("Sending message:", input.value.trim());
                        input.value = '';
                        // Placeholder: Add message to UI
                    }
                });
            }
        }
    });
    </script>
</body>
</html>
