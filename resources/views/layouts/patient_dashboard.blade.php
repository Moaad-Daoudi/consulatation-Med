<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MediConsult - Espace Patient</title>
    <style>
        /* PASTE ALL CSS from your original dashboard_patient.html HERE */
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
        .search-bar { position: relative; }
        .search-bar input { padding: 10px 15px 10px 40px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; width: 250px; }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; }
        .notification-bell { position: relative; cursor: pointer; width: 40px; height: 40px; background-color: var(--bg-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .notification-badge { position: absolute; top: -5px; right: -5px; background-color: var(--danger); color: var(--text-light); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; }
        .user-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .user-profile-img { width: 40px; height: 40px; border-radius: 50%; background-color: var(--primary); color: var(--text-light); display: flex; align-items: center; justify-content: center; font-weight: bold; text-transform: uppercase; }
        .content-wrapper { padding: 20px; flex-grow: 1; overflow-y: auto; }

        /* Content Sections (SPA style) */
        .content-section { display: none; }
        .content-section.active { display: block; }

        /* All other component styles from your dashboard_patient.html */
        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 20px; display: flex; align-items: center; }
        .stat-icon { width: 60px; height: 60px; border-radius: 10px; background-color: rgba(25, 118, 210, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-right: 20px; }
        .stat-icon.appointments { background-color: rgba(67, 160, 71, 0.1); color: var(--secondary); }
        .stat-icon.prescriptions { background-color: rgba(255, 183, 77, 0.1); color: var(--warning); }
        .stat-icon.messages { background-color: rgba(229, 57, 53, 0.1); color: var(--danger); }
        .stat-info h3 { font-size: 1.8rem; margin-bottom: 5px; }
        .stat-info p { color: #777; font-size: 0.9rem; }
        .content-container { background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 25px; margin-bottom: 30px; }
        .section-title { font-size: 1.3rem; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .appointment-item { display: grid; grid-template-columns: auto 1fr auto auto; gap:15px; padding: 15px; border-bottom: 1px solid #eee; align-items: center; }
        .appointment-item:last-child { border-bottom:none; }
        .appointment-time { font-weight: bold; }
        .appointment-doctor { font-weight: 500; }
        .appointment-type { color: #666; }
        .appointment-status { padding: 5px 10px; border-radius: 20px; text-align: center; font-size: 0.8rem; font-weight: 500; }
        .status-scheduled { background-color: var(--primary-light); color: white; }
        .status-completed { background-color: var(--secondary-light); color: white; }
        .status-cancelled { background-color: var(--danger); color: white; }
        .new-appointment-form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        textarea.form-control { min-height: 80px; resize: vertical; }
        .form-group.full-width { grid-column: span 2; }
        .btn { padding: 12px 20px; background-color: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; transition: background-color 0.3s; }
        .btn:hover { background-color: var(--primary-dark); }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px; }
        .medical-info-section { margin-bottom: 30px; }
        .medical-info-header { font-weight: bold; margin-bottom: 10px; }
        .medical-info-list { list-style: none; padding-left: 0; }
        .medical-info-item { display: flex; border-bottom: 1px solid #eee; padding: 10px 0; }
        .medical-info-item:last-child { border-bottom: none; }
        .medical-info-label { width: 200px; font-weight: 500; color: #555; }
        .medical-info-value { flex: 1; }
        .prescription-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .prescription-header { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .prescription-doctor { font-weight: bold; }
        .prescription-date { color: #666; }
        .prescription-details { margin-bottom: 10px; }
        .prescription-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px; }
        .medication-list { margin-top: 15px; padding-left:0; list-style:none; }
        .medication-item { padding: 10px; background-color: var(--bg-light); margin-bottom: 10px; border-radius: 5px; }
        .medication-name { font-weight: bold; }
        .medication-dosage { color: #666; font-size: 0.9em; }
        .results-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .results-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .results-header > div:first-child { font-weight:bold;} .results-header > div:last-child { font-size: 0.9em; color:#666;}
        .results-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .results-table th, .results-table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .results-table th { background-color: var(--bg-light); font-weight: 500; }
        .result-normal { color: var(--secondary); }
        .result-abnormal { color: var(--danger); font-weight: bold; }
        .messagerie-container { background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 25px; display: grid; grid-template-columns: 300px 1fr; gap: 20px; height: calc(100vh - 220px); margin-bottom: 30px; }
        .contacts-list { border-right: 1px solid #eee; overflow-y: auto; padding-right: 10px; }
        .contact-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; }
        .contact-item:last-child { border-bottom:none; }
        .contact-item.active, .contact-item:hover { background-color: var(--bg-light); }
        .contact-name { font-weight: bold; margin-bottom: 5px; }
        .contact-preview { color: #666; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .messages-area { display: flex; flex-direction: column; height: 100%; }
        .messages-header { padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .messages-header h3 { margin:0; font-size: 1.2em;}
        .messages-content { flex: 1; overflow-y: auto; padding: 15px 0; display: flex; flex-direction: column; }
        .message { max-width: 70%; padding: 10px 15px; border-radius: 10px; margin-bottom: 10px; line-height: 1.4;}
        .message-text { word-wrap: break-word;}
        .message-received { background-color: var(--bg-light); align-self: flex-start; }
        .message-sent { background-color: var(--primary-light); color: white; align-self: flex-end; }
        .message-time { font-size: 0.8rem; text-align: right; margin-top: 5px; opacity: 0.8; }
        .message-form { display: flex; border-top: 1px solid #eee; padding-top: 15px; }
        .message-input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; margin-right: 10px; }
        .send-btn { padding: 10px 20px; }
        .settings-form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .settings-form .form-group div { margin-bottom: 5px; } .settings-form .form-group div label {font-weight:normal; margin-left:5px;}
        .payment-list { list-style: none; padding-left: 0; }
        .payment-item { display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid #eee; align-items: center; }
        .payment-item:last-child { border-bottom:none; }
        .payment-info { flex: 1; }
        .payment-date { font-weight: 500; }
        .payment-details { color: #666; font-size: 0.9rem; }
        .payment-amount { font-weight: bold; min-width: 100px; text-align: right; }
        .payment-status { padding: 5px 10px; border-radius: 20px; text-align: center; font-size: 0.8rem; font-weight: 500; margin-left: 15px; min-width: 100px; }
        .status-paid { background-color: var(--secondary-light); color: white; }
        .status-pending { background-color: var(--warning); color: var(--text-dark); }
        .status-received { background-color: var(--primary-light); color: white; }
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
                <li>
                    <a href="#" class="menu-link active" data-section="patient_dashboard_content">
                        <div class="menu-icon">📊</div>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_appointments_content">
                        <div class="menu-icon">📅</div>
                        <span>Mes rendez-vous</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_new_appointment_content">
                        <div class="menu-icon">➕</div>
                        <span>Prendre rendez-vous</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_medical_file_content">
                        <div class="menu-icon">📁</div>
                        <span>Mon dossier médical</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_prescriptions_content">
                        <div class="menu-icon">💊</div>
                        <span>Mes ordonnances</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_lab_results_content">
                        <div class="menu-icon">🔬</div>
                        <span>Résultats d'analyses</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_messaging_content">
                        <div class="menu-icon">💬</div>
                        <span>Messagerie</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_payments_content">
                        <div class="menu-icon">💰</div>
                        <span>Paiements</span>
                    </a>
                </li>
                <li>
                     {{-- For SPA style, settings can be a section. Or link to Laravel's profile page --}}
                    <a href="#" class="menu-link" data-section="patient_settings_content">
                    {{-- <a href="{{ route('profile.edit') }}" class="menu-link"> --}}
                        <div class="menu-icon">⚙️</div>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form-patient-dashboard" style="display: none;">@csrf</form>
                    <a href="{{ route('logout') }}" class="menu-link"
                       onclick="event.preventDefault(); document.getElementById('logout-form-patient-dashboard').submit();">
                        <div class="menu-icon">🚪</div>
                        <span>Déconnexion</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <h1 class="page-title" id="patientDynamicPageTitle">Tableau de bord</h1>
                @auth
                <div class="topbar-actions">
                    <div class="search-bar">
                        <span class="search-icon">🔍</span>
                        <input type="text" placeholder="Rechercher...">
                    </div>
                    <div class="notification-bell">
                        🔔
                        <span class="notification-badge">{{-- Dynamic count --}}2</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-profile-img">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                        <span>{{ Str::before(Auth::user()->name, ' ') }}</span>
                    </div>
                </div>
                @endauth
            </div>

            <div class="content-wrapper">
                {{-- Include all patient content sections here --}}
                @include('patient.dashboard')
                @include('patient.appointments')
                @include('patient.medical_file')
                @include('patient.prescriptions')
                @include('patient.lab_results')
                @include('patient.messaging')
                @include('patient.payments')
                @include('patient.settings')
            </div>
        </main>
    </div>

    {{-- Patient specific modals could go here if any --}}
    {{-- @include('patient.partials.modals') --}}

    <script>
        // Main navigation and SPA-like behavior script for Patient Dashboard
        document.addEventListener('DOMContentLoaded', function() {
            const menuLinks = document.querySelectorAll('.sidebar-menu .menu-link');
            const contentSections = document.querySelectorAll('.content-wrapper .content-section');
            const pageTitleElement = document.getElementById('patientDynamicPageTitle');

            function activateSection(sectionId) {
                contentSections.forEach(section => {
                    if (section.id === sectionId) {
                        section.classList.add('active');
                    } else {
                        section.classList.remove('active');
                    }
                });
            }

            menuLinks.forEach(link => {
                const isLogoutLink = link.getAttribute('href') === "{{ route('logout') }}" || (link.onclick && link.onclick.toString().includes('logout-form'));
                const isExternalProfile = link.getAttribute('href') === "{{ route('profile.edit') }}";

                if (isLogoutLink || isExternalProfile) {
                    return; // Allow default behavior
                }

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

            // Activate default section
            let defaultActiveLink = document.querySelector('.sidebar-menu .menu-link.active');
            if (!defaultActiveLink && menuLinks.length > 0) {
                defaultActiveLink = menuLinks[0];
                defaultActiveLink.classList.add('active');
            }
            if (defaultActiveLink) {
                const defaultSectionId = defaultActiveLink.getAttribute('data-section');
                activateSection(defaultSectionId);
                if (pageTitleElement && defaultActiveLink.querySelector('span')) {
                    pageTitleElement.textContent = defaultActiveLink.querySelector('span').textContent;
                }
            }

            // Patient-specific JS (e.g., for messaging) - simplified example
            const patientContactItems = document.querySelectorAll('#patient_messaging_content .contact-item'); // Scoped ID
            const patientMessagesHeader = document.querySelector('#patient_messaging_content .messages-header h3');
            const patientMessagesContent = document.querySelector('#patient_messaging_content .messages-content');
            const patientMessageForm = document.querySelector('#patient_messaging_content .message-form');


            if (patientContactItems.length > 0 && patientMessagesHeader && patientMessagesContent && patientMessageForm) {
                patientContactItems.forEach(item => {
                    item.addEventListener('click', function() {
                        patientContactItems.forEach(contact => contact.classList.remove('active'));
                        this.classList.add('active');
                        patientMessagesHeader.textContent = this.querySelector('.contact-name').textContent;
                        patientMessagesContent.innerHTML = '<p style="text-align:center; color: #777; margin-top:20px;">Chargement des messages...</p>';
                        patientMessageForm.style.display = 'flex';
                        // TODO: AJAX to load messages
                    });
                });

                const sendBtn = document.querySelector('#patient_messaging_content .send-btn');
                const input = document.querySelector('#patient_messaging_content .message-input');
                if(sendBtn && input){
                     function sendPatientMsg(){
                        const text = input.value.trim();
                        if(text){
                            const now = new Date();
                            const time = `${now.getHours().toString().padStart(2,'0')}:${now.getMinutes().toString().padStart(2,'0')}`;
                            const msgDiv = document.createElement('div');
                            msgDiv.className = 'message message-sent';
                            msgDiv.innerHTML = `<div class="message-text">${text.replace(/\n/g,"<br>")}</div><div class="message-time">${time}</div>`;
                            const placeholder = patientMessagesContent.querySelector('p');
                            if(placeholder && placeholder.textContent.includes('Chargement')) placeholder.remove();
                            patientMessagesContent.appendChild(msgDiv);
                            input.value = '';
                            patientMessagesContent.scrollTop = patientMessagesContent.scrollHeight;
                            input.focus();
                            // TODO: AJAX to send patient message
                        }
                    }
                    sendBtn.addEventListener('click', sendPatientMsg);
                    input.addEventListener('keypress', e => { if(e.key==='Enter' && !e.shiftKey) {e.preventDefault(); sendPatientMsg();} });
                }
            }
        });
    </script>
</body>
</html>
