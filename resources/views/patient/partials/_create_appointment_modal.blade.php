<div class="modal-overlay" id="patient-create-appointment-modal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Prendre un Nouveau Rendez-vous</h3>
            <button type="button" class="modal-close">×</button>
        </div>
        <form action="{{ route('patient.appointments.store') }}" method="POST">
            @csrf
            <div class="modal-body modal-form">
                <div class="form-group">
                    <label for="modal_patient_appt_doctor_select">Médecin</label>
                    <select id="modal_patient_appt_doctor_select" name="doctor_id" class="form-control" required>
                        <option value="">Sélectionner un médecin</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                Dr. {{ $doctor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="modal_patient_appt_date_input">Date</label>
                    <input type="date" id="modal_patient_appt_date_input" name="appointment_date" class="form-control" value="{{ old('appointment_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group full-width">
                    <label for="modal_patient_appt_time_select">Heure Disponible</label>
                    <select id="modal_patient_appt_time_select" name="appointment_time" class="form-control" required>
                        <option value="">Sélectionnez d'abord un médecin et une date</option>
                    </select>
                    <div id="modal_patient_slots_loading" style="display: none; margin-top: 5px;">Chargement...</div>
                    <div id="modal_patient_slots_error" style="display: none; color: red; margin-top: 5px;"></div>
                </div>
                <div class="form-group full-width">
                    <label for="modal_patient_appt_notes_textarea">Motif / Notes (optionnel)</label>
                    <textarea id="modal_patient_appt_notes_textarea" name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" class="btn btn-primary">Créer le rendez-vous</button>
            </div>
        </form>
    </div>
</div>