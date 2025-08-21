<form id="form-create-prescription" action="{{ route('doctor.prescriptions.store') }}" method="POST">
    @csrf
    <div class="modal-form"> {{-- Reusing modal-form class for consistent grid layout --}}
        <div class="form-group">
            <label for="prescription_patient_id">Patient *</label>
            <select class="form-control" id="prescription_patient_id" name="patient_id" required>
                <option value="">Sélectionner un patient</option>
                {{-- $patientsForModal comes from your DoctorBaseController --}}
                @foreach ($patientsForModal as $patient)
                    <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                        {{ $patient->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="prescription_date">Date de l'Ordonnance *</label>
            <input type="date" class="form-control" id="prescription_date" name="prescription_date" value="{{ old('prescription_date', date('Y-m-d')) }}" required>
        </div>

        <div class="form-group full-width">
            <label for="prescription_consultation_id">Lier à une Consultation (Optionnel)</label>
            <select class="form-control" id="prescription_consultation_id" name="consultation_id">
                <option value="">-- Sélectionnez d'abord un patient --</option>
            </select>
            <small id="consultation_loading" style="display:none; color: var(--primary);">Chargement des consultations...</small>
        </div>

        <div class="form-group full-width">
            <label for="prescription_general_notes">Notes Générales (Optionnel)</label>
            <textarea class="form-control" id="prescription_general_notes" name="general_notes" rows="2">{{ old('general_notes') }}</textarea>
        </div>
    </div>

    <hr class="my-4">
    <h3 class="section-subtitle mb-3">Médicaments</h3>

    <div id="medication-fields-container">
        {{-- This section will be populated by JS and handle old input --}}
        <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
            <div class="row">
                <div class="col-md-6 form-group mb-2"><label for="med_name_0">Nom du Médicament *</label><input type="text" id="med_name_0" name="medications[0][name]" class="form-control" required></div>
                <div class="col-md-6 form-group mb-2"><label for="med_dosage_0">Dosage/Posologie</label><input type="text" id="med_dosage_0" name="medications[0][dosage]" class="form-control"></div>
                <div class="col-md-4 form-group mb-2"><label for="med_freq_0">Fréquence</label><input type="text" id="med_freq_0" name="medications[0][frequency]" class="form-control"></div>
                <div class="col-md-4 form-group mb-2"><label for="med_duration_0">Durée</label><input type="text" id="med_duration_0" name="medications[0][duration]" class="form-control"></div>
                <div class="col-md-4 form-group mb-2"><label for="med_notes_0">Notes Spécifiques</label><input type="text" id="med_notes_0" name="medications[0][notes]" class="form-control"></div>
            </div>
            {{-- No remove button on the first item --}}
        </div>
    </div>

    <button type="button" class="btn btn-outline-primary mb-3" id="add-medication-row-btn">+ Ajouter un autre médicament</button>

    <div class="form-actions mt-4">
        <button type="submit" class="btn btn-primary">Enregistrer l'Ordonnance</button>
    </div>
</form>

{{-- This template is hidden and used by JavaScript to add new rows --}}
<template id="medication-row-template">
    <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
        <div class="row">
            <div class="col-md-6 form-group mb-2">
                <label for="med_name___INDEX__">Nom du Médicament *</label>
                {{-- THE NAME ATTRIBUTE MUST MATCH THE DATABASE/VALIDATION --}}
                <input type="text" id="med_name___INDEX__" name="medications[__INDEX__][medication_name]" class="form-control" required>
            </div>
            <div class="col-md-6 form-group mb-2">
                <label for="med_dosage___INDEX__">Dosage/Posologie</label>
                <input type="text" id="med_dosage___INDEX__" name="medications[__INDEX__][dosage]" class="form-control">
            </div>
            <div class="col-md-4 form-group mb-2">
                <label for="med_freq___INDEX__">Fréquence</label>
                <input type="text" id="med_freq___INDEX__" name="medications[__INDEX__][frequency]" class="form-control">
            </div>
            <div class="col-md-4 form-group mb-2">
                <label for="med_duration___INDEX__">Durée</label>
                <input type="text" id="med_duration___INDEX__" name="medications[__INDEX__][duration]" class="form-control">
            </div>
            <div class="col-md-4 form-group mb-2">
                <label for="med_notes___INDEX__">Notes Spécifiques</label>
                <input type="text" id="med_notes___INDEX__" name="medications[__INDEX__][notes]" class="form-control">
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-danger remove-medication-row-btn mt-2">Retirer</button>
    </div>
</template>