<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MediConsult - Espace Patient</title>
    <style>
        /* Variables et styles de base */
        :root {
            --primary: #1976d2; --primary-light: #4791db; --primary-dark: #115293;
            --secondary: #43a047; --secondary-light: #76d275; --secondary-dark: #2d7031;
            --danger: #e53935; --danger-dark: #c02320;
            --warning: #ffb74d; --warning-dark: #ffaa00;
            --info: #00bcd4;  --info-dark: #00acc1;
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

        /* General Element Styles (from patient_dashboard.html original) */
        .dashboard-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 20px; display: flex; align-items: center; }
        .stat-icon { width: 60px; height: 60px; border-radius: 10px; background-color: rgba(25, 118, 210, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin-right: 20px; }
        .stat-icon.appointments { background-color: rgba(67, 160, 71, 0.1); color: var(--secondary); } /* Patient specific */
        .stat-icon.prescriptions { background-color: rgba(255, 183, 77, 0.1); color: var(--warning); } /* Patient specific */
        .stat-icon.messages { background-color: rgba(229, 57, 53, 0.1); color: var(--danger); }
        .stat-info h3 { font-size: 1.8rem; margin-bottom: 5px; }
        .stat-info p { color: #777; font-size: 0.9rem; }
        .content-container { background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 25px; margin-bottom: 30px; }
        .section-title { font-size: 1.3rem; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .section-title.d-flex { display: flex; justify-content: space-between; align-items: center; } /* For button next to title */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .form-control.is-invalid { border-color: var(--danger); }
        .text-danger { color: var(--danger); font-size: 0.875em; }
        .text-sm { font-size: 0.875em; }
        textarea.form-control { min-height: 80px; resize: vertical; }
        .form-group.full-width { grid-column: span 2; } /* For modal forms */
        .btn { padding: 10px 15px; background-color: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; transition: background-color 0.3s; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .btn:hover { background-color: var(--primary-dark); }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn-sm { padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; }
        .mt-2 { margin-top: .5rem !important; }
        .mt-4 { margin-top: 1.5rem !important; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px; }
        .medication-list { margin-top: 15px; padding-left:0; list-style:none; }
        .medication-item { padding: 10px; background-color: var(--bg-light); margin-bottom: 10px; border-radius: 5px; }
        .medication-name { font-weight: bold; }
        .medication-dosage { color: #666; font-size: 0.9em; }

        /* Patient Appointment Item tyle */
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529; /* Or your --text-dark */
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6; /* Light gray border */
            text-align: left;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: var(--bg-light);
            font-weight: bold;
        }
        .table tbody + tbody { /* In case you ever have multiple tbody sections */
            border-top: 2px solid #dee2e6;
        }
        .patient-appointments-table tbody tr:hover { /* Hover effect for rows */
            background-color: #f8f9fa; /* A very light gray or your --bg-light */
        }

        /* Specific column styling examples for patient's appointment table */
        .patient-appointments-table .appointment-time-header,
        .patient-appointments-table .appointment-time {
            width: 160px; /* Adjust as needed */
            white-space: nowrap;
        }
        .patient-appointments-table .appointment-doctor-header, /* Header for doctor column */
        .patient-appointments-table .appointment-doctor {
            /* Allow this to take more space */
        }
        .patient-appointments-table .appointment-type-header, /* Header for type/reason column */
        .patient-appointments-table .appointment-type {
            color: #666;
            font-size: 0.9em;
        }
        .patient-appointments-table .appointment-status-header, /* Header for status column */
        .patient-appointments-table .appointment-status-cell { /* Cell containing the status badge */
            width: 120px; /* Adjust for status badge */
            text-align: center;
        }
        .patient-appointments-table .appointment-actions-header, /* Header for actions column */
        .patient-appointments-table .appointment-actions {
            width: 100px; /* Adjust for the cancel button */
            text-align: right;
        }

        /* Overflow handling for cells that might have long content */
        .patient-appointments-table .appointment-doctor,
        .patient-appointments-table .appointment-type {
            /* max-width: 200px; /* Example, if needed */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-danger {
            background-color: var(--danger); /* Your red color variable */
            color: white; /* Or whatever text color contrasts well */
        }
        .btn-danger:hover {
            background-color: var(--danger-dark); /* A darker shade of red for hover */
        }

        /* Status badge styles (ensure these are defined) */
        .appointment-status {
            display: inline-block;
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

        /* Table responsiveness */
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto; /* Adds horizontal scroll on small screens */
            -webkit-overflow-scrolling: touch;
        }
        /* Medical File */
        .medical-info-section { margin-bottom: 30px; }
        .medical-info-header { font-weight: bold; margin-bottom: 10px; }
        .medical-info-list { list-style: none; padding-left: 0; }
        .medical-info-item { display: flex; border-bottom: 1px solid #eee; padding: 10px 0; }
        .medical-info-item:last-child { border-bottom: none; }
        .medical-info-label { width: 200px; font-weight: 500; color: #555; }
        .medical-info-value { flex: 1; }
        /* Prescriptions (Patient View) */
        .prescription-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .prescription-header { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .prescription-doctor { font-weight: bold; }
        .prescription-date { color: #666; }
        .prescription-details { margin-bottom: 10px; }
        .prescription-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px; }
        /* Lab Results */
        .results-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .results-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .results-header > div:first-child { font-weight:bold;} .results-header > div:last-child { font-size: 0.9em; color:#666;}
        .results-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .results-table th, .results-table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .results-table th { background-color: var(--bg-light); font-weight: 500; }
        .result-normal { color: var(--secondary); }
        .result-abnormal { color: var(--danger); font-weight: bold; }
        /* Patient Messaging */
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
        /* Settings Form */
        .settings-form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .settings-form .form-group div { margin-bottom: 5px; } .settings-form .form-group div label {font-weight:normal; margin-left:5px;}
        /* Payments */
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

        /* Modals */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .modal-overlay.active { display: flex; opacity: 1; visibility: visible; }
        .modal { width: 90%; max-width: 700px; /* Slightly smaller for patient modal maybe */ background-color: var(--bg-white); border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); overflow: hidden; transform: translateY(-20px) scale(0.95); transition: transform 0.3s ease, opacity 0.3s ease; opacity: 0; }
        .modal-overlay.active .modal { transform: translateY(0) scale(1); opacity: 1; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #eee; background-color: var(--primary); color: white; }
        .modal-title { font-size: 1.2rem; font-weight: 500; }
        .modal-close { font-size: 1.4rem; cursor: pointer; background: none; border: none; color: white; }
        .modal-body { padding: 20px; max-height: 70vh; overflow-y: auto; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 15px 20px; border-top: 1px solid #eee; background-color: var(--bg-light); }
        .modal-form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .modal-form .form-group.full-width { grid-column: span 2; }
        .alert { padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; }

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
                <li><a href="#" class="menu-link active" data-section="patient_dashboard_content"><div class="menu-icon">📊</div><span>Tableau de bord</span></a></li>
                <li><a href="#" class="menu-link" data-section="patient_appointments_content"><div class="menu-icon">📅</div><span>Mes rendez-vous</span></a></li>
                {{-- Note: "Prendre rendez-vous" is now a modal triggered from patient_appointments_content --}}
                <li><a href="#" class="menu-link" data-section="patient_medical_file_content"><div class="menu-icon">📁</div><span>Mon dossier médical</span></a></li>
                <li><a href="#" class="menu-link" data-section="patient_prescriptions_content"><div class="menu-icon">💊</div><span>Mes ordonnances</span></a></li>
                <li><a href="#" class="menu-link" data-section="patient_messaging_content"><div class="menu-icon">💬</div><span>Messagerie</span></a></li>
                <li><a href="#" class="menu-link" data-section="patient_settings_content"><div class="menu-icon">⚙️</div><span>Profile</span></a></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form-patient-dashboard" style="display: none;">@csrf</form>
                    <a href="{{ route('logout') }}" class="menu-link"
                       onclick="event.preventDefault(); document.getElementById('logout-form-patient-dashboard').submit();">
                        <div class="menu-icon">🚪</div><span>Déconnexion</span>
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
                    <div class="user-profile">
                        <div class="user-profile-img">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                        <span>{{ Str::before(Auth::user()->name, ' ') }}</span>
                    </div>
                </div>
                @endauth
            </div>

            <div class="content-wrapper">
                {{-- Session Messages & Validation Errors --}}
                @if(session('success')) <div class="alert alert-success success-alert" role="alert">{{ session('success') }}</div> @endif
                @if(session('error')) <div class="alert alert-danger error-alert" role="alert">{{ session('error') }}</div> @endif
                @php $openModalPatient = session('open_modal_on_load'); @endphp
                @if($errors->any() && $openModalPatient === 'patient-create-appointment-modal' )
                    <div class="alert alert-danger error-alert"><strong>Erreurs:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @elseif($errors->any() && !$openModalPatient )
                     <div class="alert alert-danger error-alert"><strong>Erreurs:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif


                @include('patient.dashboard')
                @include('patient.appointments', [
                    'upcomingAppointments' => $upcomingAppointments ?? collect(),
                    'pastAppointments' => $pastAppointments ?? collect()
                ])
                @include('patient.medical_file')
                @include('patient.prescriptions')
                @include('patient.messaging')
                @include('patient.settings')
            </div>
        </main>
    </div>

    <!-- Modal pour que le PATIENT crée un nouveau rendez-vous -->
    <div class="modal-overlay {{ ($errors->any() && session('open_modal_on_load') === 'patient-create-appointment-modal') ? 'active' : '' }}" id="patient-create-appointment-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Prendre un Nouveau Rendez-vous</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                @if($errors->any() && session('open_modal_on_load') === 'patient-create-appointment-modal') {{-- Specific error display for this modal --}}
                    <div class="alert alert-danger">
                        <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                <form id="form-patient-create-appointment-modal" action="{{ route('patient.appointments.store') }}" method="POST" class="modal-form">
                    @csrf
                    <div class="form-group">
                        <label for="modal_patient_appt_doctor_select">Médecin</label>
                        <select id="modal_patient_appt_doctor_select" name="doctor_id" class="form-control @error('doctor_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un médecin</option>
                            @foreach ($doctors ?? [] as $doctor) {{-- $doctors is passed from the main dashboard route --}}
                                <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                            @endforeach
                        </select>
                        @error('doctor_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="modal_patient_appt_date_input">Date</label>
                        <input type="date" id="modal_patient_appt_date_input" name="appointment_date" class="form-control @error('appointment_date') is-invalid @enderror" value="{{ old('appointment_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                        @error('appointment_date') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group full-width">
                        <label for="modal_patient_appt_time_select">Heure Disponible</label>
                        <select id="modal_patient_appt_time_select" name="appointment_time" class="form-control @error('appointment_time') is-invalid @enderror" required>
                            <option value="">Sélectionnez d'abord un médecin et une date</option>
                             @if(old('appointment_time'))
                                <option value="{{ old('appointment_time') }}" selected>{{ old('appointment_time') }} (Précédemment)</option>
                            @endif
                        </select>
                        @error('appointment_time') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        <div id="modal_patient_slots_loading" style="display: none; margin-top: 5px;">Chargement des créneaux...</div>
                        <div id="modal_patient_slots_error" style="display: none; color: red; margin-top: 5px;"></div>
                    </div>

                    <div class="form-group full-width">
                        <label for="modal_patient_appt_notes_textarea">Notes (optionnel)</label>
                        <textarea id="modal_patient_appt_notes_textarea" name="reason" class="form-control @error('reason') is-invalid @enderror" rows="3" placeholder="Motif court ou informations...">{{ old('reason') }}</textarea>
                        @error('reason') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" form="form-patient-create-appointment-modal" class="btn">Confirmer Rendez-vous</button>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- SPA Navigation Logic ---
        const menuLinks = document.querySelectorAll('.sidebar-menu .menu-link');
        const contentSections = document.querySelectorAll('.content-wrapper .content-section');
        const pageTitleElement = document.getElementById('patientDynamicPageTitle'); // Ensure this ID is unique if you have multiple dashboards on one page (unlikely)

        function activateSection(sectionId) {
            contentSections.forEach(section => {
                section.classList.toggle('active', section.id === sectionId);
            });
            if(sectionId) localStorage.setItem('activePatientSection', sectionId);
        }

        menuLinks.forEach(link => {
            const isLogoutLink = link.getAttribute('href') === "{{ route('logout') }}" || (link.onclick && link.onclick.toString().includes('logout-form'));
            const isExternalProfile = link.getAttribute('href') === "{{ route('profile.edit') }}"; // Standard profile edit
            const isSettingsSPA = link.getAttribute('data-section') === 'patient_settings_content' && link.getAttribute('href') === '#'; // If 'Profile' in sidebar is SPA

            if (isLogoutLink || (isExternalProfile && !isSettingsSPA) ) return;

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
        const savedPatientSection = localStorage.getItem('activePatientSection');
        let defaultPatientActiveLink = null;
        if (savedPatientSection) defaultPatientActiveLink = document.querySelector(`.sidebar-menu .menu-link[data-section="${savedPatientSection}"]`);
        if (!defaultPatientActiveLink) defaultPatientActiveLink = document.querySelector('.sidebar-menu .menu-link.active') || (menuLinks.length > 0 ? menuLinks[0] : null);
        if (defaultPatientActiveLink) {
            menuLinks.forEach(item => item.classList.remove('active'));
            defaultPatientActiveLink.classList.add('active');
            activateSection(defaultPatientActiveLink.getAttribute('data-section'));
            if (pageTitleElement && defaultPatientActiveLink.querySelector('span')) pageTitleElement.textContent = defaultPatientActiveLink.querySelector('span').textContent;
        }

        // --- General Modal Toggle Logic ---
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-modal-target');
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('active');
                     // If opening patient create appointment modal, and date is pre-filled, fetch slots
                    if (modalId === 'patient-create-appointment-modal') {
                        const dateInput = document.getElementById('modal_patient_appt_date_input');
                        const doctorInput = document.getElementById('modal_patient_appt_doctor_select');
                        if (dateInput && dateInput.value && doctorInput && doctorInput.value) {
                            fetchPatientModalAvailableSlots();
                        }
                    }
                }
            });
        });
        document.querySelectorAll('.modal-close, .modal-close-btn').forEach(button => {
            button.addEventListener('click', () => { button.closest('.modal-overlay')?.classList.remove('active'); });
        });
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('active'); });
        });

        // --- JS for Patient Create Appointment Modal Slots ---
        const patientCreateApptModalEl = document.getElementById('patient-create-appointment-modal');
        let doctorSelectPatientModal, dateInputPatientModal, timeSelectPatientModal, slotsLoadingPatientModal, slotsErrorPatientModal;

        if(patientCreateApptModalEl){ // Ensure modal exists before selecting children
            doctorSelectPatientModal = document.getElementById('modal_patient_appt_doctor_select');
            dateInputPatientModal = document.getElementById('modal_patient_appt_date_input');
            timeSelectPatientModal = document.getElementById('modal_patient_appt_time_select');
            slotsLoadingPatientModal = document.getElementById('modal_patient_slots_loading');
            slotsErrorPatientModal = document.getElementById('modal_patient_slots_error');

            function fetchPatientModalAvailableSlots() {
                const doctorId = doctorSelectPatientModal.value;
                const selectedDate = dateInputPatientModal.value;
                const previouslySelectedTime = timeSelectPatientModal.dataset.oldTime || "{{ old('appointment_time', '') }}";

                timeSelectPatientModal.innerHTML = '<option value="">Chargement...</option>';
                timeSelectPatientModal.disabled = true;
                slotsErrorPatientModal.style.display = 'none';
                slotsErrorPatientModal.textContent = '';

                if (!doctorId || !selectedDate) { timeSelectPatientModal.innerHTML = '<option value="">Sélectionnez un médecin et une date</option>'; return; }
                if (new Date(selectedDate) < new Date(new Date().toISOString().split('T')[0])) {
                     timeSelectPatientModal.innerHTML = '<option value="">Date invalide</option>';
                     slotsErrorPatientModal.textContent = 'La date ne peut pas être dans le passé.'; slotsErrorPatientModal.style.display = 'block'; return;
                }
                slotsLoadingPatientModal.style.display = 'block';

                fetch("{{ route('appointments.available_slots') }}", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                    body: JSON.stringify({ doctor_id: doctorId, date: selectedDate })
                })
                .then(response => response.ok ? response.json() : response.json().then(err => { console.error("Server error:", err); throw err; }))
                .then(data => {
                    slotsLoadingPatientModal.style.display = 'none';
                    timeSelectPatientModal.innerHTML = '<option value="">-- Choisissez une heure --</option>';
                    if (data.slots && data.slots.length > 0) {
                        data.slots.forEach(slot => {
                            const opt = document.createElement('option'); opt.value = slot; opt.textContent = slot;
                            if (slot === previouslySelectedTime) opt.selected = true;
                            timeSelectPatientModal.appendChild(opt);
                        });
                        timeSelectPatientModal.disabled = false;
                    } else {
                        if (previouslySelectedTime) { const opt = document.createElement('option'); opt.value = previouslySelectedTime; opt.textContent = previouslySelectedTime + " (Non disponible)"; opt.disabled = true; opt.selected = true; timeSelectPatientModal.appendChild(opt); }
                        else { timeSelectPatientModal.innerHTML = '<option value="">Aucun créneau</option>'; }
                        slotsErrorPatientModal.textContent = data.message || 'Aucun créneau disponible.'; slotsErrorPatientModal.style.display = 'block';
                    }
                })
                .catch(error => {
                    slotsLoadingPatientModal.style.display = 'none'; timeSelectPatientModal.innerHTML = '<option value="">Erreur</option>';
                    console.error('Error fetching slots for patient modal:', error);
                    let errorMsg = (error && error.errors) ? Object.values(error.errors).flat().join(' ') : ((error && error.message) ? error.message : 'Erreur de chargement.');
                    slotsErrorPatientModal.textContent = `Erreur: ${errorMsg}`; slotsErrorPatientModal.style.display = 'block';
                });
            }
            if(doctorSelectPatientModal) doctorSelectPatientModal.addEventListener('change', fetchPatientModalAvailableSlots);
            if(dateInputPatientModal) dateInputPatientModal.addEventListener('change', fetchPatientModalAvailableSlots);
            if(timeSelectPatientModal && "{{old('appointment_time')}}") timeSelectPatientModal.dataset.oldTime = "{{old('appointment_time')}}";
        }

        const patientModalToOpen = "{{ session('open_modal_on_load') }}";
        if (patientModalToOpen) {
            const modalEl = document.getElementById(patientModalToOpen);
            if (modalEl) {
                modalEl.classList.add('active');
                if (patientModalToOpen === 'patient-create-appointment-modal' && dateInputPatientModal && dateInputPatientModal.value && doctorSelectPatientModal && doctorSelectPatientModal.value) {
                    fetchPatientModalAvailableSlots(); // Re-fetch if modal is opened due to error and fields have values
                }
            }
        }

        // --- Auto-hide session alerts ---
        const successAlerts = document.querySelectorAll('.alert-success.success-alert');
        successAlerts.forEach(alertEl => {
            setTimeout(() => {
                alertEl.style.transition = 'opacity 0.5s ease'; alertEl.style.opacity = '0';
                setTimeout(() => { alertEl.style.display = 'none'; }, 500);
            }, 10000);
        });
        // Add similar for .error-alert if you want them to auto-hide too
    });
    </script>
</body>
</html>
