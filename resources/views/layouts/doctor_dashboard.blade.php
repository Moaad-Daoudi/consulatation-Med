<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MediConsult - Dashboard Médecin</title>
    <style>
        /* Variables et styles de base */
        :root {
            --primary: #1976d2; --primary-light: #4791db; --primary-dark: #115293;
            --secondary: #43a047; --secondary-light: #76d275; --secondary-dark: #2d7031; /* Green for completed */
            --danger: #e53935; --danger-dark: #c02320; /* Red for cancelled */
            --warning: #ffb74d; --warning-dark: #ffaa00;
            --info: #00bcd4;  --info-dark: #00acc1;/* Example for info buttons */
            --text-dark: #333; --text-light: #f5f5f5;
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

        /* General Element Styles */
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
        .form-control.is-invalid { border-color: var(--danger); }
        .text-danger { color: var(--danger); font-size: 0.875em; }
        .text-sm { font-size: 0.875em; } /* For smaller text, like error messages */
        textarea.form-control { min-height: 120px; resize: vertical; }
        .form-group.full-width { grid-column: span 2; } /* For modal forms */
        .btn { padding: 10px 15px; background-color: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; transition: background-color 0.3s; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .btn:hover { background-color: var(--primary-dark); }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn-success { background-color: var(--secondary); }
        .btn-success:hover { background-color: var(--secondary-dark); }
        .btn-warning { background-color: var(--warning); color: var(--text-dark); } /* Ensure contrast for warning */
        .btn-warning:hover { background-color: var(--warning-dark); }
        .btn-info { background-color: var(--info); }
        .btn-info:hover { background-color: var(--info-dark); }
        .btn-danger { background-color: var(--danger); }
        .btn-danger:hover { background-color: var(--danger-dark); }
        .btn-sm { padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; }
        .btn-outline-primary { color: var(--primary); border: 1px solid var(--primary); background-color: transparent;}
        .btn-outline-primary:hover { color: #fff; background-color: var(--primary); border-color: var(--primary); }
        .mt-2 { margin-top: .5rem !important; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px; }
        .medication-list { margin-top: 20px; }
        .medication-item { display: flex; background-color: var(--bg-light); border-radius: 5px; padding: 10px 15px; margin-bottom: 10px; justify-content: space-between; align-items: center; }
        .medication-info span { font-weight: bold; margin-right: 10px; }
        .remove-med { color: var(--danger); cursor: pointer; }
        .appointments-list { margin-top: 20px; }

        /* DIV TABLE STYLES FOR APPOINTMENTS */
        .div-table {
            display: table;
            width: 100%;
            border-collapse: collapse; /* Behaves like table border-collapse */
            margin-top: 20px;
        }
        .div-table-header, .div-table-row {
            display: table-row;
            border-bottom: 1px solid #eee; /* Apply border to rows like in a table */
        }
        .div-table-header {
            font-weight: bold;
            background-color: var(--bg-light); /* Light background for header */
        }
        .div-table-header:hover, .div-table-row:hover {
             background-color: #f9f9f9; /* Slight hover effect for rows */
        }
        .div-table-header:last-child, .div-table-row:last-child {
            border-bottom: none;
        }

        .div-table-cell {
            display: table-cell;
            padding: 12px 10px; /* Padding for cells */
            vertical-align: middle; /* Align content vertically */
            text-align: left; /* Default text alignment */
        }

        /* Specific column styling for the div table */
        .div-table-cell.appointment-time-header,
        .div-table-cell.appointment-time {
            width: 160px; /* Adjust width as needed */
            white-space: nowrap;
        }
        .div-table-cell.appointment-patient-header,
        .div-table-cell.appointment-patient {
            /* Let this take up more space, but can still be controlled */
            /* width: 25%; /* Example */
        }
        .div-table-cell.appointment-type-header,
        .div-table-cell.appointment-type {
            /* width: 25%; /* Example */
            color: #666;
            font-size: 0.9em;
        }
        .div-table-cell.appointment-status-header,
        .div-table-cell.appointment-status-cell { /* Renamed cell class */
            width: 120px; /* Adjust for status badge */
            text-align: center;
        }
        .div-table-cell.appointment-actions-header,
        .div-table-cell.appointment-actions {
            width: 100px; /* Adjust for action buttons */
            text-align: right;
            white-space: nowrap;
        }

        /* Ensure content within cells with specific classes also handles overflow if needed */
        .div-table-cell.appointment-patient,
        .div-table-cell.appointment-type {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap; /* To make ellipsis work */
            /* max-width: 200px; /* You might need to set a max-width if content is very long */
        }

        .appointment-status {
            /* These styles are for the badge itself, which is now inside a .div-table-cell */
            display: inline-block; /* To allow padding and border-radius to work well */
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
            white-space: nowrap;
        }
        .status-scheduled { background-color: var(--primary-light); }
        .status-completed { background-color: var(--secondary); }
        .status-cancelled { background-color: var(--danger); }
        .status-default { background-color: #6c757d; }

        /* Ensure action buttons are spaced nicely if there are multiple */
        .div-table-cell.appointment-actions form {
            margin-left: 5px; /* Add space between form buttons */
        }
        .div-table-cell.appointment-actions form:first-child {
            margin-left: 0;
        }

        /* Appointment Status Badges */
        .appointment-status { padding: 5px 10px; border-radius: 20px; text-align: center; font-size: 0.8rem; font-weight: 500; color: white; white-space: nowrap; }
        .status-scheduled { background-color: var(--primary-light); } /* Blue */
        .status-completed { background-color: var(--secondary); }    /* Green */
        .status-cancelled { background-color: var(--danger); }      /* Red */
        .status-default { background-color: #6c757d; } /* Fallback gray */


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

        /* Styles for Appointment Filters */
        .form-inline { display: flex; flex-wrap: wrap; align-items: center; margin-bottom: 1rem; }
        .form-inline .form-group { margin-right: 10px; margin-bottom: 10px; } /* Allow wrapping */
        .form-inline .form-control-sm { height: calc(1.5em + .5rem + 2px); padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; }
        .ml-2 { margin-left: .5rem !important; }
        .alert { padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; } /* For accessible hidden labels */

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
                {{-- Session Messages & Validation Errors --}}
                @if(session('success')) <div class="alert alert-success" role="alert">{{ session('success') }}</div> @endif
                @if(session('error')) <div class="alert alert-danger" role="alert">{{ session('error') }}</div> @endif
                @php $openModal = session('open_modal_on_load'); @endphp
                @if($errors->any() && $openModal)
                    @php $errorBagName = ($openModal === 'add-patient-modal') ? 'addPatientModal' : 'default'; @endphp
                    @if($errors->hasBag($errorBagName) && $errors->getBag($errorBagName)->any())
                    <div class="alert alert-danger"><strong>Erreurs:</strong><ul>@foreach($errors->getBag($errorBagName)->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @elseif($errors->any() && $errorBagName === 'default') {{-- Show default bag for other modal errors --}}
                    <div class="alert alert-danger"><strong>Erreurs:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @endif
                @elseif($errors->any() && !$openModal) {{-- General validation errors not tied to a specific modal that should reopen --}}
                     <div class="alert alert-danger"><strong>Erreurs:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif

                @include('doctor.dashboard')
                @include('doctor.appointments')
                @include('doctor.patients')
                @include('doctor.consultations')
                @include('doctor.ordonnances')
                @include('doctor.messagerie')
                @include('doctor.parametres')
            </div>
        </main>
    </div>

    <!-- Modal pour ajouter un nouveau patient -->
    <div class="modal-overlay {{ $errors->hasBag('addPatientModal') || session('open_modal_on_load') === 'add-patient-modal' ? 'active' : '' }}" id="add-patient-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Ajouter un nouveau patient</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                @if($errors->hasBag('addPatientModal') && $errors->getBag('addPatientModal')->any())
                    <div class="alert alert-danger">
                        <ul>@foreach($errors->getBag('addPatientModal')->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <form class="modal-form" id="form-add-new-patient-details-modal" action="{{ route('doctor.patients.store_from_modal') }}" method="POST">
                    @csrf
                    <div class="form-group"><label for="modal_new_patient_name_field">Nom Complet du Patient</label><input type="text" class="form-control @error('name', 'addPatientModal') is-invalid @enderror" id="modal_new_patient_name_field" name="name" value="{{ old('name') }}" required> @error('name', 'addPatientModal') <span class="text-danger text-sm">{{ $message }}</span> @enderror</div>
                    <div class="form-group"><label for="modal_new_patient_email_field">Email Patient</label><input type="email" class="form-control @error('email', 'addPatientModal') is-invalid @enderror" id="modal_new_patient_email_field" name="email" value="{{ old('email') }}" required> @error('email', 'addPatientModal') <span class="text-danger text-sm">{{ $message }}</span> @enderror</div>
                    <div class="form-group"><label for="modal_new_patient_password_field">Mot de passe</label><input type="password" class="form-control @error('password', 'addPatientModal') is-invalid @enderror" id="modal_new_patient_password_field" name="password" required> @error('password', 'addPatientModal') <span class="text-danger text-sm">{{ $message }}</span> @enderror</div>
                    <div class="form-group"><label for="modal_new_patient_password_confirmation_field">Confirmer Mot de passe</label><input type="password" class="form-control" id="modal_new_patient_password_confirmation_field" name="password_confirmation" required></div>
                    <div class="form-group"><label for="modal_new_patient_phone_field">Téléphone (Optionnel)</label><input type="tel" class="form-control @error('phone', 'addPatientModal') is-invalid @enderror" id="modal_new_patient_phone_field" name="phone" value="{{ old('phone') }}"> @error('phone', 'addPatientModal') <span class="text-danger text-sm">{{ $message }}</span> @enderror</div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" form="form-add-new-patient-details-modal" class="btn">Enregistrer Patient</button>
            </div>
        </div>
    </div>

    <!-- Modal pour que le DOCTEUR crée un nouveau rendez-vous -->
    <div class="modal-overlay {{ ($errors->any() && !$errors->hasBag('addPatientModal')) || session('open_modal_on_load') === 'doctor-create-appointment-modal' ? 'active' : '' }}" id="doctor-create-appointment-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Créer un Nouveau Rendez-vous</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                 @if($errors->any() && !$errors->hasBag('addPatientModal')) {{-- Show default bag errors --}}
                    <div class="alert alert-danger">
                        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <form id="form-doctor-create-appointment-modal" action="{{ route('doctor.appointments.store') }}" method="POST" class="modal-form">
                    @csrf
                    <div class="form-group">
                        <label for="modal_doc_create_patient_select">Patient</label>
                        <select id="modal_doc_create_patient_select" name="patient_id" class="form-control @error('patient_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un patient</option>
                            @foreach ($patientsForModal ?? [] as $patient_user)
                                <option value="{{ $patient_user->id }}" {{ old('patient_id') == $patient_user->id ? 'selected' : '' }}>{{ $patient_user->name }}</option>
                            @endforeach
                        </select>
                        @error('patient_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-modal-target="add-patient-modal" style="font-size: 0.8em; padding: 0.25rem 0.5rem;">
                            + Ajouter un nouveau patient
                        </button>
                    </div>

                    @if(Auth::check() && Auth::user()->role->name !== 'doctor')
                        <div class="form-group">
                            <label for="modal_doc_assign_doctor_select">Assigner au Docteur</label>
                            <select id="modal_doc_assign_doctor_select" name="doctor_id" class="form-control @error('doctor_id') is-invalid @enderror" required>
                                <option value="">Sélectionner un docteur</option>
                                @foreach ($doctorsForModal ?? [] as $doc_user)
                                    <option value="{{ $doc_user->id }}" {{ old('doctor_id') == $doc_user->id ? 'selected' : '' }}>{{ $doc_user->name }}</option>
                                @endforeach
                            </select>
                             @error('doctor_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                    @else
                        <input type="hidden" name="doctor_id" value="{{ Auth::id() }}">
                         <div class="form-group">
                            <label>Docteur</label>
                            <input type="text" class="form-control" value="{{ Auth::user()->name }}" readonly>
                        </div>
                    @endif

                    <div class="form-group">
                        <label for="modal_doc_create_date_input">Date</label>
                        <input type="date" id="modal_doc_create_date_input" name="appointment_date" class="form-control @error('appointment_date') is-invalid @enderror" value="{{ old('appointment_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                        @error('appointment_date') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="modal_doc_create_time_select">Heure Disponible</label>
                        <select id="modal_doc_create_time_select" name="appointment_time" class="form-control @error('appointment_time') is-invalid @enderror" required>
                            <option value="">Sélectionnez d'abord un médecin et une date</option>
                            @if(old('appointment_time'))
                                <option value="{{ old('appointment_time') }}" selected>{{ old('appointment_time') }} (Précédemment)</option>
                            @endif
                        </select>
                        @error('appointment_time') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        <div id="modal_doc_slots_loading" style="display: none; margin-top: 5px;">Chargement...</div>
                        <div id="modal_doc_slots_error" style="display: none; color: red; margin-top: 5px;"></div>
                    </div>

                    <div class="form-group full-width">
                        <label for="modal_doc_create_notes_textarea">Notes (optionnel)</label>
                        <textarea id="modal_doc_create_notes_textarea" name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3">{{ old('reason') }}</textarea>
                        @error('reason') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" form="form-doctor-create-appointment-modal" class="btn">Créer Rendez-vous</button>
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
            if (sectionId) localStorage.setItem('activeDoctorSection', sectionId);
        }

        menuLinks.forEach(link => {
            const isLogoutLink = link.getAttribute('href') === "{{ route('logout') }}" || (link.onclick && link.onclick.toString().includes('logout-form'));
            const isExternalProfile = link.getAttribute('href') === "{{ route('profile.edit') }}";
            const isParametresSPA = link.getAttribute('data-section') === 'parametres' && link.getAttribute('href') === '#';

            if (isLogoutLink || (isExternalProfile && !isParametresSPA) ) return;

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

        const savedSection = localStorage.getItem('activeDoctorSection');
        let defaultActiveLink = null;
        if (savedSection) {
            defaultActiveLink = document.querySelector(`.sidebar-menu .menu-link[data-section="${savedSection}"]`);
        }
        if (!defaultActiveLink) {
            defaultActiveLink = document.querySelector('.sidebar-menu .menu-link.active') || (menuLinks.length > 0 ? menuLinks[0] : null);
        }
        if (defaultActiveLink) {
            menuLinks.forEach(item => item.classList.remove('active'));
            defaultActiveLink.classList.add('active');
            activateSection(defaultActiveLink.getAttribute('data-section'));
            if (pageTitleElement && defaultActiveLink.querySelector('span')) {
                pageTitleElement.textContent = defaultActiveLink.querySelector('span').textContent;
            }
        }

        const successAlert = document.querySelector('.alert-success');
        const errorAlert = document.querySelector('.alert-danger'); // You might want to hide error alerts too, or keep them longer

        if (successAlert) {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';
                setTimeout(() => {
                    successAlert.style.display = 'none';
                    // If you want to remove it from the DOM completely after fade out:
                    // successAlert.remove();
                }, 500); // Wait for fade out transition to complete
            }, 10000); // 10000 milliseconds = 10 seconds
        }

        // --- General Modal Toggle Logic ---
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-modal-target');
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('active');
                    if (modalId === 'doctor-create-appointment-modal') {
                        const dateInput = document.getElementById('modal_doc_create_date_input');
                        if (dateInput && dateInput.value) {
                            fetchDoctorModalAvailableSlots();
                        }
                    }
                }
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

        // --- JS for Doctor Create Appointment Modal Slots ---
        const doctorCreateApptModalEl = document.getElementById('doctor-create-appointment-modal');
        let doctorSelectInModal, doctorHiddenInputInModal, dateInputDocModal, timeSelectDocModal, slotsLoadingDocModal, slotsErrorDocModal;

        if (doctorCreateApptModalEl) {
            doctorSelectInModal = document.getElementById('modal_doc_assign_doctor_select');
            doctorHiddenInputInModal = doctorCreateApptModalEl.querySelector('input[name="doctor_id"]');
            dateInputDocModal = document.getElementById('modal_doc_create_date_input');
            timeSelectDocModal = document.getElementById('modal_doc_create_time_select');
            slotsLoadingDocModal = document.getElementById('modal_doc_slots_loading');
            slotsErrorDocModal = document.getElementById('modal_doc_slots_error');

            function fetchDoctorModalAvailableSlots() {
                const doctorId = (doctorSelectInModal && doctorSelectInModal.offsetParent !== null && doctorSelectInModal.value) ? doctorSelectInModal.value : (doctorHiddenInputInModal ? doctorHiddenInputInModal.value : null);
                const selectedDate = dateInputDocModal.value;
                const previouslySelectedTime = timeSelectDocModal.dataset.oldTime || "{{ old('appointment_time', '') }}";

                timeSelectDocModal.innerHTML = '<option value="">Chargement...</option>';
                timeSelectDocModal.disabled = true;
                slotsErrorDocModal.style.display = 'none';

                if (!doctorId || !selectedDate) {
                    timeSelectDocModal.innerHTML = '<option value="">Sélectionnez un médecin et une date</option>'; return;
                }
                if (new Date(selectedDate) < new Date(new Date().toISOString().split('T')[0])) {
                     timeSelectDocModal.innerHTML = '<option value="">Date invalide</option>';
                     slotsErrorDocModal.textContent = 'La date ne peut pas être dans le passé.';
                     slotsErrorDocModal.style.display = 'block'; return;
                }
                slotsLoadingDocModal.style.display = 'block';

                fetch("{{ route('appointments.available_slots') }}", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                    body: JSON.stringify({ doctor_id: doctorId, date: selectedDate })
                })
                .then(response => response.ok ? response.json() : response.json().then(err => { console.error("Server error:", err); throw err; }))
                .then(data => {
                    slotsLoadingDocModal.style.display = 'none';
                    timeSelectDocModal.innerHTML = '<option value="">-- Choisissez une heure --</option>';
                    if (data.slots && data.slots.length > 0) {
                        data.slots.forEach(slot => {
                            const option = document.createElement('option'); option.value = slot; option.textContent = slot;
                            if (slot === previouslySelectedTime) option.selected = true;
                            timeSelectDocModal.appendChild(option);
                        });
                        timeSelectDocModal.disabled = false;
                    } else {
                        if (previouslySelectedTime) {
                             const option = document.createElement('option');
                             option.value = previouslySelectedTime; option.textContent = previouslySelectedTime + " (Non disponible)";
                             option.disabled = true; option.selected = true;
                             timeSelectDocModal.appendChild(option);
                        } else {
                            timeSelectDocModal.innerHTML = '<option value="">Aucun créneau</option>';
                        }
                        slotsErrorDocModal.textContent = data.message || 'Aucun créneau disponible.';
                        slotsErrorDocModal.style.display = 'block';
                    }
                })
                .catch(error => {
                    slotsLoadingDocModal.style.display = 'none';
                    timeSelectDocModal.innerHTML = '<option value="">Erreur</option>';
                    console.error('Error fetching slots for doctor modal:', error);
                    let errorMsg = 'Erreur de chargement des créneaux.';
                    if(error && error.errors) { errorMsg = Object.values(error.errors).flat().join(' '); }
                    else if (error && error.message) { errorMsg = error.message; }
                    slotsErrorDocModal.textContent = `Erreur: ${errorMsg}`;
                    slotsErrorDocModal.style.display = 'block';
                });
            }
            if(doctorSelectInModal) doctorSelectInModal.addEventListener('change', fetchDoctorModalAvailableSlots);
            if(dateInputDocModal) dateInputDocModal.addEventListener('change', fetchDoctorModalAvailableSlots);
            if(timeSelectDocModal && "{{old('appointment_time')}}") timeSelectDocModal.dataset.oldTime = "{{old('appointment_time')}}";
        }

        // Re-open modal if there were validation errors (from session) and pre-fetch slots
        const modalToOpenFromSession = "{{ session('open_modal_on_load') }}";
        if (modalToOpenFromSession) {
            const modalElement = document.getElementById(modalToOpenFromSession);
            if (modalElement) {
                modalElement.classList.add('active');
                if (modalToOpenFromSession === 'doctor-create-appointment-modal' && dateInputDocModal && dateInputDocModal.value) {
                    fetchDoctorModalAvailableSlots();
                }
            }
        }

        // --- START: JS for Ordonnances Section ---
        const addMedBtn = document.getElementById('add-med-btn');
        const medicationListContainer = document.querySelector('#ordonnances .medication-list');

        if (addMedBtn && medicationListContainer) {
            addMedBtn.addEventListener('click', () => {
                const medicamentInput = document.getElementById('medicament_name_ord');
                const dosageInput = document.getElementById('dosage_ord');
                const frequencyInput = document.getElementById('frequency_ord');
                const durationInput = document.getElementById('duration_ord');

                if (!medicamentInput || !medicamentInput.value.trim() || !dosageInput || !dosageInput.value.trim()) {
                    alert('Veuillez entrer au moins le nom du médicament et le dosage.'); return;
                }
                const medItem = document.createElement('div');
                medItem.className = 'medication-item';
                medItem.innerHTML = `
                    <div class="medication-info">
                        <span>${medicamentInput.value.trim()} ${dosageInput.value.trim()}</span>
                        - ${frequencyInput.value.trim() || 'N/A'}, pendant ${durationInput.value.trim() || 'N/A'}
                    </div>
                    <div class="remove-med" role="button" tabindex="0" aria-label="Supprimer médicament">❌</div>`;
                medicationListContainer.appendChild(medItem);
                medItem.querySelector('.remove-med').addEventListener('click', function() { this.parentElement.remove(); });
                medicamentInput.value = ''; dosageInput.value = ''; frequencyInput.value = ''; durationInput.value = '';
                medicamentInput.focus();
            });
        }
        document.querySelectorAll('#ordonnances .medication-list .remove-med').forEach(btn => {
            btn.addEventListener('click', function() { this.parentElement.remove(); });
        });
        // --- END: JS for Ordonnances Section ---


        // --- START: JS for Messagerie Section ---
        const contactItems = document.querySelectorAll('#messagerie .contact-item');
        const messagesHeaderNameEl = document.querySelector('#messagerie .messages-header h3');
        const messagesContentEl = document.querySelector('#messagerie .messages-content');
        const messageFormEl = document.querySelector('#messagerie .message-form');
        const messageSendBtn = document.querySelector('#messagerie .send-btn');
        const messageInputEl = document.querySelector('#messagerie .message-input');

        if (contactItems.length > 0 && messagesHeaderNameEl && messagesContentEl && messageFormEl) {
            contactItems.forEach(item => {
                item.addEventListener('click', function() {
                    contactItems.forEach(contact => contact.classList.remove('active'));
                    this.classList.add('active');
                    const contactName = this.querySelector('.contact-name').textContent;
                    messagesHeaderNameEl.textContent = contactName;
                    messagesContentEl.innerHTML = '<p style="text-align:center; color: #777; margin-top:20px;">Chargement des messages pour ' + contactName + '...</p>';
                    messageFormEl.style.display = 'flex';
                });
            });
            if(messageSendBtn && messageInputEl){
                function sendMessageFromDoctorUI(){
                    const messageText = messageInputEl.value.trim();
                    if(messageText){
                        const now = new Date();
                        const hours = now.getHours().toString().padStart(2, '0');
                        const minutes = now.getMinutes().toString().padStart(2, '0');
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'message message-sent';
                        messageDiv.innerHTML = `<div class="message-text">${messageText.replace(/\n/g, "<br>")}</div><div class="message-time">${hours}:${minutes}</div>`;
                        const placeholder = messagesContentEl.querySelector('p');
                        if(placeholder && placeholder.textContent.includes('Chargement')) placeholder.remove();
                        messagesContentEl.appendChild(messageDiv);
                        messageInputEl.value = '';
                        messagesContentEl.scrollTop = messagesContentEl.scrollHeight;
                        messageInputEl.focus();
                    }
                }
                messageSendBtn.addEventListener('click', sendMessageFromDoctorUI);
                messageInputEl.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessageFromDoctorUI(); }
                });
            }
        }
        // --- END: JS for Messagerie Section ---
    });
    </script>
</body>
</html>
