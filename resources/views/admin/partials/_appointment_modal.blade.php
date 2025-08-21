<div class="modal-user" id="appointmentModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="appointmentModalTitle">Create New Appointment</h2>
            <button class="close" onclick="closeAppointmentModal()">×</button>
        </div>
        
        <form id="appointmentForm" method="POST" action=""> 
            @csrf
            <input type="hidden" name="_method" id="appointmentMethodInput" value="POST">

            <div class="modal-body modal-form">
                <div class="form-group">
                    <label for="patient_id">Patient *</label>
                    <select name="patient_id" id="patient_id" class="form-control" required>
                        <option value="">Select Patient...</option>
                        @foreach($patients as $patient)
                            <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="doctor_id">Doctor *</label>
                    <select name="doctor_id" id="admin_appointment_doctor_id" class="form-control" required>
                        <option value="">Select Doctor...</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">Dr. {{ $doctor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="appointment_date">Date *</label>
                    <input type="date" name="appointment_date" id="admin_appointment_date" class="form-control" min="{{ date('Y-m-d') }}" required>
                </div>

                <div class="form-group">
                    <label for="appointment_time">Heure Disponible *</label>
                    <select name="appointment_time" id="admin_appointment_time" class="form-control" required>
                        <option value="">Sélectionnez un médecin et une date</option>
                    </select>
                    <div id="admin_slots_loading" style="display: none; margin-top: 5px; color: var(--primary);">Chargement...</div>
                    <div id="admin_slots_error" style="display: none; margin-top: 5px; color: var(--danger);"></div>
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select name="status" id="status" class="form-control" required>
                        <option value="scheduled">Scheduled</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeAppointmentModal()">Cancel</button>
                <button type="submit" class="btn-submit" id="appointmentSubmitButton">Save Appointment</button>
            </div>
        </form>
    </div>
</div>