    <!-- Modal for the DOCTOR to create a new appointment -->
    <div class="modal-overlay {{ ($errors->any() && old('form_source') === 'create_appointment') ? 'active' : '' }}" id="doctor-create-appointment-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Créer un Nouveau Rendez-vous</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            
            {{-- This form will submit to the generic appointment store route --}}
            <form id="form-doctor-create-appointment-modal" action="{{ route('doctor.appointments.store') }}" method="POST">
                @csrf
                {{-- This hidden input helps identify which modal had an error --}}
                <input type="hidden" name="form_source" value="create_appointment">

                <div class="modal-body">
                    {{-- Show validation errors specifically for this form --}}
                    @if($errors->any() && old('form_source') === 'create_appointment')
                        <div class="alert alert-danger">
                            <strong>Errors:</strong>
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Form Fields --}}
                    <div class="form-group">
                        <label for="modal_doc_create_patient_select">Patient</label>
                        {{-- The $patientsForModal variable is available thanks to our View Composer --}}
                        <select id="modal_doc_create_patient_select" name="patient_id" class="form-control" required>
                            <option value="">Sélectionner un patient</option>
                            @foreach ($patientsForModal as $patient)
                                <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                    {{ $patient->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- The doctor is automatically assigned --}}
                    <input type="hidden" name="doctor_id" value="{{ Auth::id() }}">

                    <div class="form-group">
                        <label for="modal_doc_create_date_input">Date</label>
                        <input type="date" id="modal_doc_create_date_input" name="appointment_date" class="form-control" value="{{ old('appointment_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group full-width">
                        <label for="modal_doc_create_time_select">Heure Disponible</label>
                        {{-- This select is populated by your JavaScript --}}
                        <select id="modal_doc_create_time_select" name="appointment_time" class="form-control" required>
                            <option value="">Sélectionnez d'abord une date</option>
                             @if(old('appointment_time'))
                                <option value="{{ old('appointment_time') }}" selected>{{ old('appointment_time') }} (Précédemment sélectionné)</option>
                            @endif
                        </select>
                        <div id="modal_doc_slots_loading" style="display: none; margin-top: 5px;">Chargement...</div>
                        <div id="modal_doc_slots_error" style="display: none; color: red; margin-top: 5px;"></div>
                    </div>

                    <div class="form-group full-width">
                        <label for="modal_doc_create_notes_textarea">Notes (optionnel)</label>
                        <textarea id="modal_doc_create_notes_textarea" name="notes" class="form-control" rows="3" placeholder="Ex: Consultation de suivi, symptômes...">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer Rendez-vous</button>
                </div>
            </form>
        </div>
    </div>
