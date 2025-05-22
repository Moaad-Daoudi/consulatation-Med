<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MediConsult - Dashboard Médecin</title>
    <link rel="stylesheet" href="{{ asset('css/doctor_dashboard.css') }}">
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
                    <a href="#" class="menu-link active" data-section="dashboard">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/tableau_de_bord.png') }}" alt="Dashboard Icon">
                        </div>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="appointments">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/rendez_vous.png') }}" alt="Appointments Icon">
                        </div>
                        <span>Rendez-vous</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patients">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/patients.png') }}" alt="Patients Icon">
                        </div>
                        <span>Patients</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="consultations">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/consultations.png') }}" alt="Consultations Icon">
                        </div>
                        <span>Consultations</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="ordonnances">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/ordonnances.png') }}" alt="Ordonnances Icon">
                        </div>
                        <span>Ordonnances</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="parametres">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/profile.png') }}" alt="Profile Icon">
                        </div>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form-doctor-dashboard" style="display: none;">@csrf</form>
                    <a href="{{ route('logout') }}" class="menu-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form-doctor-dashboard').submit();">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/logout.png') }}" alt="Logout Icon">
                        </div>
                        <span>Déconnexion</span>
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

                @include('doctor.dashboard')
                @include('doctor.appointments')
                @include('doctor.patients')
                @include('doctor.consultations')
                @include('doctor.ordonnances')
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
                    <div class="form-group"><label for="modal_new_patient_name_field">Nom Complet</label><input type="text" class="form-control @error('name', 'addPatientModal') is-invalid @enderror" id="modal_new_patient_name_field" name="name" value="{{ old('name') }}" required> @error('name', 'addPatientModal') <span class="text-danger text-sm">{{ $message }}</span> @enderror</div>
                    <div class="form-group"><label for="modal_new_patient_email_field">Email</label><input type="email" class="form-control @error('email', 'addPatientModal') is-invalid @enderror" id="modal_new_patient_email_field" name="email" value="{{ old('email') }}" required> @error('email', 'addPatientModal') <span class="text-danger text-sm">{{ $message }}</span> @enderror</div>
                    <div class="form-group"><label for="modal_new_patient_password_field">Mot de passe</label><input type="password" class="form-control @error('password', 'addPatientModal') is-invalid @enderror" id="modal_new_patient_password_field" name="password" required> @error('password', 'addPatientModal') <span class="text-danger text-sm">{{ $message }}</span> @enderror</div>
                    <div class="form-group"><label for="modal_new_patient_password_confirmation_field">Confirmer Mot de passe</label><input type="password" class="form-control" id="modal_new_patient_password_confirmation_field" name="password_confirmation" required></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" form="form-add-new-patient-details-modal" class="btn">Enregistrer</button>
            </div>
        </div>
    </div>

    <!-- View Patient Dossier Modal -->
    <div class="modal-overlay" id="viewPatientDossierModal">
        <div class="modal modal-content" style="max-width: 900px;"> {{-- Make it wider --}}
            <div class="modal-header">
                <h5 class="modal-title">Dossier Patient: <span id="dossier_patient_name"></span></h5>
                <button type="button" class="modal-close" data-modal-dismiss="viewPatientDossierModal">×</button>
            </div>
            <div class="modal-body" id="viewPatientDossierModalBody">
                <p class="text-center">Chargement du dossier...</p>
                {{-- Content will be loaded by JavaScript --}}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="viewPatientDossierModal">Fermer</button>
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

    <!-- Create Consultation Modal (Simplified - No Appointment Linking) -->
    <div class="modal-overlay {{ $errors->hasBag('consultationCreate') && session('open_modal_on_load') === 'createConsultationModal' ? 'active' : '' }}" id="createConsultationModal">
        <div class="modal modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Consultation</h5>
                <button type="button" class="modal-close" data-modal-dismiss="createConsultationModal">×</button>
            </div>
            <form method="POST" action="{{ route('doctor.consultations.store') }}">
                @csrf
                <div class="modal-body">
                     @if($errors->hasBag('consultationCreate'))
                        <div class="alert alert-danger"><ul>@foreach($errors->consultationCreate->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @endif
                    <div class="modal-form">
                        <div class="form-group"><label for="modal_create_consult_patient_id">Patient *</label><select name="patient_id" id="modal_create_consult_patient_id" class="form-control @error('patient_id', 'consultationCreate') is-invalid @enderror" required><option value="">Sélectionner Patient</option>@foreach($patientsForModal ?? [] as $p)<option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>@endforeach</select>@error('patient_id', 'consultationCreate')<span class="text-danger text-sm">{{$message}}</span>@enderror</div>
                        <div class="form-group"><label for="modal_create_consult_consultation_date_time">Date et Heure *</label><input type="datetime-local" name="consultation_date_time" id="modal_create_consult_consultation_date_time" class="form-control @error('consultation_date_time', 'consultationCreate') is-invalid @enderror" value="{{ old('consultation_date_time', now()->format('Y-m-d\TH:i')) }}" required>@error('consultation_date_time', 'consultationCreate')<span class="text-danger text-sm">{{$message}}</span>@enderror</div>
                        <div class="form-group full-width"><label for="modal_create_consult_reason_for_visit">Motif *</label><input type="text" name="reason_for_visit" id="modal_create_consult_reason_for_visit" class="form-control @error('reason_for_visit', 'consultationCreate') is-invalid @enderror" value="{{ old('reason_for_visit') }}" required>@error('reason_for_visit', 'consultationCreate')<span class="text-danger text-sm">{{$message}}</span>@enderror</div>
                        <div class="form-group full-width"><label for="modal_create_consult_symptoms">Symptômes *</label><textarea name="symptoms" id="modal_create_consult_symptoms" class="form-control @error('symptoms', 'consultationCreate') is-invalid @enderror" rows="3">{{ old('symptoms') }}</textarea>@error('symptoms', 'consultationCreate')<span class="text-danger text-sm">{{$message}}</span>@enderror</div>
                        <div class="form-group full-width"><label for="modal_create_consult_notes">Notes Docteur</label><textarea name="notes" id="modal_create_consult_notes" class="form-control @error('notes', 'consultationCreate') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>@error('notes', 'consultationCreate')<span class="text-danger text-sm">{{$message}}</span>@enderror</div>
                        <div class="form-group full-width"><label for="modal_create_consult_diagnosis">Diagnostic</label><textarea name="diagnosis" id="modal_create_consult_diagnosis" class="form-control @error('diagnosis', 'consultationCreate') is-invalid @enderror" rows="3">{{ old('diagnosis') }}</textarea>@error('diagnosis', 'consultationCreate')<span class="text-danger text-sm">{{$message}}</span>@enderror</div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
            </form>
        </div>
    </div>

    <!-- Edit Consultation Modal (Simplified) -->
    <div class="modal-overlay {{ session('open_modal_on_load') === 'editConsultationModal' && $errors->hasBag('consultationEdit_' . session('consultation_id_for_error_bag')) ? 'active' : '' }}" id="editConsultationModal">
        <div class="modal modal-content">
            <div class="modal-header"><h5 class="modal-title">Modifier Consultation</h5><button type="button" class="modal-close" data-modal-dismiss="editConsultationModal">×</button></div>
            <form method="POST" action="" id="editConsultationForm">@csrf @method('PUT')
                <div class="modal-body">
                    @if(session('consultation_id_for_error_bag') && $errors->hasBag('consultationEdit_' . session('consultation_id_for_error_bag')))<div class="alert alert-danger"><ul>@foreach($errors->getBag('consultationEdit_' . session('consultation_id_for_error_bag'))->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
                    <div id="editConsultationErrorsGeneral" class="alert alert-danger" style="display:none;"><ul></ul></div>
                    <input type="hidden" name="consultation_id_for_error_bag_identifier" id="edit_consultation_id_for_error_bag">
                    <div class="modal-form">
                        <div class="form-group"><label>Patient</label><input type="text" class="form-control" id="edit_consult_patient_name_display" readonly></div>
                        <div class="form-group"><label for="edit_consult_consultation_date_time">Date et Heure *</label><input type="datetime-local" name="consultation_date_time" id="edit_consult_consultation_date_time" class="form-control" value="{{ old('consultation_date_time') }}" required></div>
                        <div class="form-group full-width"><label for="edit_consult_reason_for_visit">Motif *</label><textarea name="reason_for_visit" id="edit_consult_reason_for_visit" class="form-control" rows="2" required>{{ old('reason_for_visit') }}</textarea></div>
                        <div class="form-group full-width"><label for="edit_consult_symptoms">Symptômes</label><textarea name="symptoms" id="edit_consult_symptoms" class="form-control" rows="3">{{ old('symptoms') }}</textarea></div>
                        <div class="form-group full-width"><label for="edit_consult_notes">Notes</label><textarea name="notes" id="edit_consult_notes" class="form-control" rows="3">{{ old('notes') }}</textarea></div>
                        <div class="form-group full-width"><label for="edit_consult_diagnosis">Diagnostic</label><textarea name="diagnosis" id="edit_consult_diagnosis" class="form-control" rows="3">{{ old('diagnosis') }}</textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="editConsultationModal">Annuler</button><button type="submit" class="btn btn-primary">Mettre à Jour</button></div>
            </form>
        </div>
    </div>

    <!-- View Consultation Detail Modal (Simplified) -->
    <div class="modal-overlay" id="viewConsultationDetailModal">
        <div class="modal modal-content">
            <div class="modal-header"><h5 class="modal-title">Détails Consultation</h5><button type="button" class="modal-close" data-modal-dismiss="viewConsultationDetailModal">×</button></div>
            <div class="modal-body">
                <p><strong>Patient:</strong> <span id="view_consult_patient_name"></span></p>
                <p><strong>Date:</strong> <span id="view_consult_date"></span></p>
                <p><strong>RDV Associé:</strong> <span id="view_consult_appointment_info"></span></p> {{-- Will show 'Aucun' if not set --}}
                <hr>
                <p><strong>Motif:</strong></p><p id="view_consult_reason" style="white-space:pre-wrap;"></p>
                <p><strong>Symptômes:</strong></p><p id="view_consult_symptoms" style="white-space:pre-wrap;"></p>
                <p><strong>Notes Docteur:</strong></p><p id="view_consult_notes" style="white-space:pre-wrap;"></p>
                <p><strong>Diagnostic:</strong></p><p id="view_consult_diagnosis" style="white-space:pre-wrap;"></p>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="viewConsultationDetailModal">Fermer</button></div>
        </div>
    </div>

    <!-- View Prescription Modal -->
    <div class="modal-overlay" id="viewPrescriptionModal">
        <div class="modal modal-content" style="max-width: 700px;">
            <div class="modal-header"><h5 class="modal-title">Détails Ordonnance</h5><button type="button" class="modal-close" data-modal-dismiss="viewPrescriptionModal">×</button></div>
            <div class="modal-body" id="viewPrescriptionModalBody" style="font-size: 0.9rem;">
                {{-- Content populated by JS --}}
                <p>Chargement...</p>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="viewPrescriptionModal">Fermer</button></div>
        </div>
    </div>

    <!-- Edit Prescription Modal -->
    <div class="modal-overlay {{ (session('editing_prescription') || (session('prescription_id_for_error_bag') && session('open_modal_on_load') === 'editPrescriptionModal')) ? 'active' : '' }}" id="editPrescriptionModal">
        <div class="modal modal-content" style="max-width: 900px;">
            <div class="modal-header"><h5 class="modal-title">Modifier Ordonnance</h5><button type="button" class="modal-close" data-modal-dismiss="editPrescriptionModal">×</button></div>
            <form id="form-edit-prescription" method="POST" action="">@csrf @method('PUT')
                <div class="modal-body">
                    @php $editPrescriptionErrorBag = 'prescriptionEdit_' . session('prescription_id_for_error_bag'); @endphp
                    @if(session('prescription_id_for_error_bag') && $errors->hasBag($editPrescriptionErrorBag))
                        <div class="alert alert-danger"><strong>Erreurs:</strong><ul>@foreach($errors->getBag($editPrescriptionErrorBag)->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>
                    @endif
                    <div class="modal-form">
                        <div class="form-group"><label for="edit_prescription_patient_id">Patient *</label>
                            <select class="form-control" id="edit_prescription_patient_id" name="patient_id" required>
                                <option value="">Sélectionner Patient</option>
                                @foreach ($patientsForModal ?? (session('patientsForModal') ?? []) as $p)
                                <option value="{{ $p->id }}" {{ (old('patient_id', session('editing_prescription.patient_id') ?? '') == $p->id) ? 'selected' : '' }}>{{$p->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group"><label for="edit_prescription_date">Date *</label><input type="date" class="form-control" id="edit_prescription_date" name="prescription_date" value="{{ old('prescription_date', substr(session('editing_prescription.prescription_date') ?? date('Y-m-d'),0,10)) }}" required></div>
                        <div class="form-group full-width"><label for="edit_prescription_consultation_id">Consultation Liée</label>
                            <select class="form-control" id="edit_prescription_consultation_id" name="consultation_id"><option value="">-- Aucune --</option></select>
                            <small id="edit_prescription_consultation_loading" style="display:none;">Chargement...</small>
                        </div>
                        <div class="form-group full-width"><label for="edit_prescription_general_notes">Notes Générales</label><textarea class="form-control" id="edit_prescription_general_notes" name="general_notes" rows="2">{{ old('general_notes', session('editing_prescription.general_notes') ?? '') }}</textarea></div>
                    </div><hr class="my-3">
                    <h6 class="mb-2">Médicaments</h6>
                    <div id="edit-medication-fields-container">
                        @php
                            $medsToDisplay = old('medications', session('editing_prescription.items') ?? []);
                        @endphp
                        @if(!empty($medsToDisplay))
                            @foreach($medsToDisplay as $key => $med)
                            <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
                                <input type="hidden" name="medications[{{$key}}][id]" value="{{ $med['id'] ?? '' }}">
                                <div class="row gx-2">
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_name_{{$key}}">Nom *</label><input type="text" id="edit_med_name_{{$key}}" name="medications[{{$key}}][name]" class="form-control form-control-sm" value="{{ $med['medication_name'] ?? ($med['name'] ?? '') }}" required></div>
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_dosage_{{$key}}">Dosage</label><input type="text" id="edit_med_dosage_{{$key}}" name="medications[{{$key}}][dosage]" class="form-control form-control-sm" value="{{ $med['dosage'] ?? '' }}"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_freq_{{$key}}">Fréquence</label><input type="text" id="edit_med_freq_{{$key}}" name="medications[{{$key}}][frequency]" class="form-control form-control-sm" value="{{ $med['frequency'] ?? '' }}"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_duration_{{$key}}">Durée</label><input type="text" id="edit_med_duration_{{$key}}" name="medications[{{$key}}][duration]" class="form-control form-control-sm" value="{{ $med['duration'] ?? '' }}"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_notes_{{$key}}">Notes</label><input type="text" id="edit_med_notes_{{$key}}" name="medications[{{$key}}][notes]" class="form-control form-control-sm" value="{{ $med['notes'] ?? '' }}"></div>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-medication-row-btn mt-1">Retirer</button>
                            </div>
                            @endforeach
                        @elseif(empty(old('medications')) && !session('editing_prescription.items'))
                             <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
                                <input type="hidden" name="medications[0][id]" value="">
                                <div class="row gx-2">
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_name_0">Nom *</label><input type="text" id="edit_med_name_0" name="medications[0][name]" class="form-control form-control-sm" required></div>
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_dosage_0">Dosage</label><input type="text" id="edit_med_dosage_0" name="medications[0][dosage]" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_freq_0">Fréquence</label><input type="text" id="edit_med_freq_0" name="medications[0][frequency]" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_duration_0">Durée</label><input type="text" id="edit_med_duration_0" name="medications[0][duration]" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_notes_0">Notes</label><input type="text" id="edit_med_notes_0" name="medications[0][notes]" class="form-control form-control-sm"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-edit-medication-row-btn">+ Ajouter Médicament</button>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="editPrescriptionModal">Annuler</button><button type="submit" class="btn btn-primary">Mettre à Jour</button></div>
            </form>
        </div>
    </div>


    <script>
        window.doctorDashboardConfig = {
            routes: {
                logout: "{{ route('logout') }}",
                availableSlots: "{{ route('appointments.available_slots') }}",
                patientDossierBaseUrl: "{{ url('doctor/patients') }}",
                consultationsBaseUrl: "{{ url('doctor/consultations') }}",
                consultationsForLinkingBaseUrl: "{{ url('doctor/patients') }}",
                prescriptionsBaseUrl: "{{ url('doctor/prescriptions') }}"
            },
            session: {
                activeSectionOnLoad: @json(session('active_section_on_load')),
                openModalOnLoad: @json(session('open_modal_on_load')),
                consultationIdForErrorBag: @json(session('consultation_id_for_error_bag')),
                prescriptionIdForErrorBag: @json(session('prescription_id_for_error_bag')),
                editingPrescription: @json(session('editing_prescription'))
            },
            auth: {
                userName: @json(Auth::user()->name)
            },
            oldInput: {
                appointmentTime: @json(old('appointment_time', '')),
                patientId: @json(old('patient_id')),
                consultationId: @json(old('consultation_id'))
            },
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    <script src="{{ asset('js/doctor_dashboard.js') }}" defer></script>
</body>
</html>
