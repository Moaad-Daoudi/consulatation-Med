<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MediConsult - Espace Patient</title>
    <link rel="stylesheet" href="{{ asset('css/patient_dashboard.css') }}">
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
                <div class="user-avatar">
                    @if(Auth::user()->photo_path)
                        <img src="{{ asset('storage/' . Auth::user()->photo_path) }}" alt="Avatar">
                    @else
                        @php
                            $nameParts = explode(' ', Auth::user()->name, 2);
                            $initials = strtoupper(substr($nameParts[0], 0, 1));
                            if (isset($nameParts[1])) { $initials .= strtoupper(substr($nameParts[1], 0, 1)); }
                            elseif (strlen($nameParts[0]) > 1) { $initials = strtoupper(substr($nameParts[0], 0, 2)); }
                        @endphp
                        {{ $initials }}
                    @endif
                </div>
                <div class="user-name">{{ Auth::user()->name }}</div>
                @if(Auth::user()->role)
                <div class="user-role">{{ ucfirst(Auth::user()->role->name) }}</div>
                @endif
            </div>
            @endauth
            <ul class="sidebar-menu">
                <li>
                    <a href="#" class="menu-link active" data-section="patient_dashboard_content">
                        <div class="menu-icon"><img src="{{ asset('assets/sidebar/tableau_de_bord.png') }}" alt="Dashboard"></div>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_appointments_content">
                        <div class="menu-icon"><img src="{{ asset('assets/sidebar/rendez_vous.png') }}" alt="Rendez-vous"></div>
                        <span>Mes rendez-vous</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_medical_file_content">
                        <div class="menu-icon"><img src="{{ asset('assets/sidebar/dossier_medical.png') }}" alt="Dossier Médical"></div>
                        <span>Mon dossier médical</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_prescriptions_content">
                        <div class="menu-icon"><img src="{{ asset('assets/sidebar/ordonnances.png') }}" alt="Ordonnances"></div>
                        <span>Mes ordonnances</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_settings_content">
                        <div class="menu-icon"><img src="{{ asset('assets/sidebar/profile.png') }}" alt="Profil"></div>
                        <span>Profil</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form-patient-dashboard" style="display: none;">@csrf</form>
                    <a href="{{ route('logout') }}" class="menu-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form-patient-dashboard').submit();">
                        <div class="menu-icon"><img src="{{ asset('assets/sidebar/logout.png') }}" alt="Déconnexion"></div>
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
                    <div class="user-profile">
                        <div class="user-profile-img">
                                @php
                                    $namePartsTopbar = explode(' ', Auth::user()->name, 2);
                                    $initialsTopbar = strtoupper(substr($namePartsTopbar[0], 0, 1));
                                    if (isset($namePartsTopbar[1])) { $initialsTopbar .= strtoupper(substr($namePartsTopbar[1], 0, 1)); }
                                    elseif (strlen($namePartsTopbar[0]) > 1) { $initialsTopbar = strtoupper(substr($namePartsTopbar[0], 0, 2)); }
                                @endphp
                                {{ $initialsTopbar }}
                        </div>
                        <span>{{ Str::words(Auth::user()->name, 1, '') }}</span>
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
                    <div class="alert alert-danger error-alert" style="margin-bottom:15px;"><strong>Erreurs lors de la création du RDV:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @elseif($errors->any() && $openModalPatient !== 'patient-create-appointment-modal' && Auth::user()->role->name === 'patient' && session('active_section_on_load') === 'patient_settings_content' )
                    {{-- Errors specific to patient settings will be shown within patient.settings.blade.php --}}
                @elseif($errors->any() && !$openModalPatient && !(Auth::user()->role->name === 'patient' && session('active_section_on_load') === 'patient_settings_content') )
                     <div class="alert alert-danger error-alert" style="margin-bottom:15px;"><strong>Erreurs générales:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif

                {{-- Include patient specific content sections --}}
                @include('patient.dashboard', [
                    'upcomingAppointmentCount' => $upcomingAppointmentCount ?? 0,
                    'activePrescriptionsCount' => $activePrescriptionsCount ?? 0,
                    'nextAppointment' => $nextAppointment ?? null,
                    'medicationReminders' => $medicationReminders ?? collect()
                ])
                @include('patient.appointments', [
                    'upcomingAppointments' => $upcomingAppointments ?? collect(),
                    'pastAppointments' => $pastAppointments ?? collect()
                ])
                @include('patient.medical_file', [
                    'patientConsultations' => $patientConsultations ?? collect()
                ])
                @include('patient.prescriptions', [
                     'activePrescriptions' => $activePrescriptions ?? collect(),
                     'pastPrescriptions' => $pastPrescriptions ?? collect()
                ])
                @include('patient.settings') {{-- This is where patient profile form is included --}}
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
            <form id="form-patient-create-appointment-modal" action="{{ route('patient.appointments.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    {{-- Errors specific to this modal are shown directly above the form grid --}}
                    @if($errors->any() && session('open_modal_on_load') === 'patient-create-appointment-modal')
                        {{-- This div is already handled by the general error display logic above the includes --}}
                        {{-- <div class="alert alert-danger"><strong>Erreurs:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> --}}
                    @endif

                    <div class="modal-form"> {{-- Grid layout for form elements --}}
                        <div class="form-group">
                            <label for="modal_patient_appt_doctor_select">Médecin</label>
                            <select id="modal_patient_appt_doctor_select" name="doctor_id" class="form-control @error('doctor_id') is-invalid @enderror" required>
                                <option value="">Sélectionner un médecin</option>
                                @foreach ($doctors ?? [] as $doctor) {{-- $doctors should be passed from controller to patient dashboard view --}}
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>Dr. {{ $doctor->name }}</option>
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
                            <label for="modal_patient_appt_notes_textarea">Raison du RDV / Notes (optionnel)</label>
                            <textarea id="modal_patient_appt_notes_textarea" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Ex: Consultation de suivi, symptômes spécifiques...">{{ old('notes') }}</textarea>
                            @error('notes') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le rendez-vous</button>
                </div>
            </form>
        </div>
    </div>

<script>
    window.patientDashboardConfig = {
            routes: {
                logout: "{{ route('logout') }}",
                availableSlots: "{{ route('appointments.available_slots') }}"
            },
            session: {
                openModalOnLoad: @json(session('open_modal_on_load')),
                activeSectionOnLoad: @json(session('active_section_on_load'))
            },
            errors: {
                any: {{ $errors->any() ? 'true' : 'false' }},
                hasProfileSettingsErrorsForPatientSettings: {{
                    $errors->any() &&
                    Auth::user()->role->name === 'patient' &&
                    (session('active_section_on_load') === 'patient_settings_content' || old('_token')) &&
                    !empty(array_intersect(array_keys($errors->getMessages()), ['name', 'email', 'phone_number', 'photo', 'date_of_birth', 'gender', 'address', 'emergency_contact', 'emergency_contact_phone']))
                    ? 'true' : 'false'
                }}
            },
            auth: {
                roleName: @json(Auth::user()->role->name ?? '')
            },
            oldInput: {
                appointmentTime: @json(old('appointment_time', '')),
                csrfToken: @json(csrf_token())
            },
            initialSectionFromServer:
                @if($errors->any() && session('open_modal_on_load') === 'patient-create-appointment-modal')
                    'patient_appointments_content'
                @elseif($errors->any() && Auth::user()->role->name === 'patient' && (session('active_section_on_load') === 'patient_settings_content' || old('_token')))
                    @if(!empty(array_intersect(array_keys($errors->getMessages()), ['name', 'email', 'phone_number', 'photo', 'date_of_birth', 'gender', 'address', 'emergency_contact', 'emergency_contact_phone'])))
                        'patient_settings_content'
                    @else
                        null
                    @endif
                @else
                    null
                @endif
        };
    </script>
    <script src="{{ asset('js/patient_dashboard.js') }}" defer></script>
</body>
</html>
