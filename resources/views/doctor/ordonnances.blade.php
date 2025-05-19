<div id="ordonnances" class="content-section">
    {{-- Create New Prescription Form --}}
    <div class="ordonnance-container create-prescription-section mb-5">
        <h2 class="section-title">Créer une nouvelle ordonnance</h2>

        {{-- Display validation errors for prescriptionCreate bag --}}
        @if($errors->hasBag('prescriptionCreate') && $errors->getBag('prescriptionCreate')->any())
            <div class="alert alert-danger mb-3">
                <strong>Erreurs de validation :</strong>
                <ul>
                    @foreach($errors->getBag('prescriptionCreate')->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(session('prescription_error')) {{-- Custom error from controller --}}
            <div class="alert alert-danger mb-3">{{ session('prescription_error') }}</div>
        @endif


        <form id="form-create-prescription" action="{{ route('doctor.prescriptions.store') }}" method="POST">
            @csrf
            <div class="modal-form"> {{-- Using modal-form for consistent grid layout --}}
                <div class="form-group">
                    <label for="prescription_patient_id">Patient *</label>
                    <select class="form-control @error('patient_id', 'prescriptionCreate') is-invalid @enderror" id="prescription_patient_id" name="patient_id" required>
                        <option value="">Sélectionner un patient</option>
                        @foreach ($patientsForModal ?? [] as $patient)
                            <option value="{{ $patient->id }}" {{ old('patient_id') == $patient->id ? 'selected' : '' }}>
                                {{ $patient->name }} ({{ $patient->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id', 'prescriptionCreate') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="form-group">
                    <label for="prescription_date">Date de l'Ordonnance *</label>
                    <input type="date" class="form-control @error('prescription_date', 'prescriptionCreate') is-invalid @enderror" id="prescription_date" name="prescription_date" value="{{ old('prescription_date', date('Y-m-d')) }}" required>
                    @error('prescription_date', 'prescriptionCreate') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="form-group full-width">
                    <label for="prescription_consultation_id">Lier à une Consultation (Optionnel)</label>
                    <select class="form-control @error('consultation_id', 'prescriptionCreate') is-invalid @enderror" id="prescription_consultation_id" name="consultation_id">
                        <option value="">-- Sélectionnez d'abord un patient --</option>
                        {{-- This will be populated via JavaScript based on selected patient --}}
                        {{-- If old('consultation_id') exists, JS should try to re-select it after populating --}}
                    </select>
                    <small id="prescription_consultation_loading" style="display:none;">Chargement des consultations...</small>
                    @error('consultation_id', 'prescriptionCreate') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                </div>


                <div class="form-group full-width">
                    <label for="prescription_general_notes">Notes Générales (Optionnel)</label>
                    <textarea class="form-control @error('general_notes', 'prescriptionCreate') is-invalid @enderror" id="prescription_general_notes" name="general_notes" rows="2" placeholder="Instructions générales, allergies...">{{ old('general_notes') }}</textarea>
                    @error('general_notes', 'prescriptionCreate') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <hr class="my-4">
            <h3 class="section-subtitle mb-3">Médicaments</h3>
            @error('medications', 'prescriptionCreate') <div class="alert alert-danger text-sm p-2">{{ $message }}</div> @enderror

            <div id="medication-fields-container">
                @if(old('medications'))
                    @foreach(old('medications') as $key => $med)
                    <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
                        <input type="hidden" name="medications[{{ $key }}][id]" value="{{ $med['id'] ?? '' }}"> {{-- Should be empty for new prescription --}}
                        <div class="row"> {{-- medication-item-row specific grid --}}
                            <div class="col-md-6 form-group mb-2">
                                <label for="med_name_{{$key}}">Nom du Médicament *</label>
                                <input type="text" id="med_name_{{$key}}" name="medications[{{ $key }}][name]" class="form-control @error('medications.'.$key.'.name', 'prescriptionCreate') is-invalid @enderror" placeholder="Ex: Amoxicilline" value="{{ $med['name'] ?? '' }}" required>
                                @error('medications.'.$key.'.name', 'prescriptionCreate') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group mb-2">
                                <label for="med_dosage_{{$key}}">Dosage/Posologie</label>
                                <input type="text" id="med_dosage_{{$key}}" name="medications[{{ $key }}][dosage]" class="form-control @error('medications.'.$key.'.dosage', 'prescriptionCreate') is-invalid @enderror" placeholder="Ex: 500mg, 1 comprimé" value="{{ $med['dosage'] ?? '' }}">
                                @error('medications.'.$key.'.dosage', 'prescriptionCreate') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row"> {{-- medication-item-row specific grid --}}
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_freq_{{$key}}">Fréquence</label>
                                <input type="text" id="med_freq_{{$key}}" name="medications[{{ $key }}][frequency]" class="form-control @error('medications.'.$key.'.frequency', 'prescriptionCreate') is-invalid @enderror" placeholder="Ex: 3 fois/jour" value="{{ $med['frequency'] ?? '' }}">
                                @error('medications.'.$key.'.frequency', 'prescriptionCreate') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_duration_{{$key}}">Durée</label>
                                <input type="text" id="med_duration_{{$key}}" name="medications[{{ $key }}][duration]" class="form-control @error('medications.'.$key.'.duration', 'prescriptionCreate') is-invalid @enderror" placeholder="Ex: 7 jours" value="{{ $med['duration'] ?? '' }}">
                                @error('medications.'.$key.'.duration', 'prescriptionCreate') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_notes_{{$key}}">Notes Spécifiques</label>
                                <input type="text" id="med_notes_{{$key}}" name="medications[{{ $key }}][notes]" class="form-control @error('medications.'.$key.'.notes', 'prescriptionCreate') is-invalid @enderror" placeholder="Ex: Après repas" value="{{ $med['notes'] ?? '' }}">
                                @error('medications.'.$key.'.notes', 'prescriptionCreate') <span class="text-danger text-sm d-block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @if($loop->index > 0 || count(old('medications')) > 1)
                            <button type="button" class="btn btn-sm btn-danger remove-medication-row-btn mt-2">Retirer ce médicament</button>
                        @endif
                    </div>
                    @endforeach
                @else
                    {{-- Default first medication row --}}
                    <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
                        <div class="row"> {{-- medication-item-row specific grid --}}
                            <div class="col-md-6 form-group mb-2">
                                <label for="med_name_0">Nom du Médicament *</label>
                                <input type="text" id="med_name_0" name="medications[0][name]" class="form-control" placeholder="Ex: Amoxicilline" required>
                            </div>
                            <div class="col-md-6 form-group mb-2">
                                <label for="med_dosage_0">Dosage/Posologie</label>
                                <input type="text" id="med_dosage_0" name="medications[0][dosage]" class="form-control" placeholder="Ex: 500mg, 1 comprimé">
                            </div>
                        </div>
                        <div class="row"> {{-- medication-item-row specific grid --}}
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_freq_0">Fréquence</label>
                                <input type="text" id="med_freq_0" name="medications[0][frequency]" class="form-control" placeholder="Ex: 3 fois/jour">
                            </div>
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_duration_0">Durée</label>
                                <input type="text" id="med_duration_0" name="medications[0][duration]" class="form-control" placeholder="Ex: 7 jours">
                            </div>
                            <div class="col-md-4 form-group mb-2">
                                <label for="med_notes_0">Notes Spécifiques</label>
                                <input type="text" id="med_notes_0" name="medications[0][notes]" class="form-control" placeholder="Ex: Après repas">
                            </div>
                        </div>
                        {{-- No remove button for the very first default item initially --}}
                    </div>
                @endif
            </div>

            <button type="button" class="btn btn-outline-primary mb-3" id="add-medication-row-btn">
                + Ajouter un autre médicament
            </button>

            <div class="form-actions mt-4 d-flex justify-content-end">
                <button type="button" class="btn btn-secondary me-2" id="cancel-create-prescription-btn">Annuler</button>
                <button type="submit" class="btn btn-primary">Enregistrer l'Ordonnance</button>
            </div>
        </form>
    </div>

    <hr class="my-5">

    {{-- Prescription History List --}}
    <div class="prescription-history-section">
        <h2 class="section-title">Historique des Ordonnances</h2>
        <div class="div-table prescriptions-list mt-3">
            <div class="div-table-header">
                <div class="div-table-cell">Date</div>
                <div class="div-table-cell">Patient</div>
                <div class="div-table-cell">Nb. Médicaments</div>
                <div class="div-table-cell">Actions</div>
            </div>
            @php
                // This data should be passed from the main dashboard controller via $prescriptionsForDashboard
                // Using a fallback here if not passed, but prefer passing from controller.
                $prescriptionsToDisplay = $prescriptionsForDashboard ?? \App\Models\Prescription::where('doctor_id', Auth::id())
                                            ->with('patient')
                                            ->withCount('items')
                                            ->latest('prescription_date')
                                            ->paginate(10, ['*'], 'prescriptions_page');
            @endphp
            @forelse($prescriptionsToDisplay as $prescription)
            <div class="div-table-row prescription-item-row">
                <div class="div-table-cell"> <!-- Date -->
                    {{ $prescription->prescription_date->format('d/m/Y') }}
                </div>
                <div class="div-table-cell"> <!-- Patient -->
                    {{ $prescription->patient->name ?? 'Patient Inconnu' }}
                </div>
                <div class="div-table-cell"> <!-- Nb. Médicaments -->
                    {{ $prescription->items_count }}
                </div>
                <div class="div-table-cell prescription-actions">
                    <button type="button" class="btn btn-sm btn-info view-prescription-btn"
                            data-id="{{ $prescription->id }}"
                            data-url="{{ route('doctor.prescriptions.show', $prescription->id) }}">
                        Voir
                    </button>
                    <button type="button" class="btn btn-sm btn-warning edit-prescription-btn"
                            data-id="{{ $prescription->id }}"
                            data-edit-url="{{ route('doctor.prescriptions.edit', $prescription->id) }}">
                        Modifier
                    </button>
                    <form action="{{ route('doctor.prescriptions.destroy', $prescription->id) }}" method="POST" class="d-inline-block"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ordonnance ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Suppr</button>
                    </form>
                </div>
            </div>
            @empty
            <div class="div-table-row">
                <div class="div-table-cell" data-empty-row="true">
                    Aucune ordonnance trouvée.
                </div>
            </div>
            @endforelse
        </div>
        @if($prescriptionsToDisplay->hasPages())
            <div class="mt-3 d-flex justify-content-center">
                {{ $prescriptionsToDisplay->appends(request()->except('page'))->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>

{{-- Hidden template for medication rows (for JS cloning in create/edit forms) --}}
<template id="medication-row-template">
    <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
        <input type="hidden" name="medications[__INDEX__][id]" value=""> {{-- For existing items during edit --}}
        <div class="row"> {{-- medication-item-row specific grid --}}
            <div class="col-md-6 form-group mb-2">
                <label for="med_name___INDEX__">Nom du Médicament *</label>
                <input type="text" id="med_name___INDEX__" name="medications[__INDEX__][name]" class="form-control" placeholder="Ex: Amoxicilline" required>
            </div>
            <div class="col-md-6 form-group mb-2">
                <label for="med_dosage___INDEX__">Dosage/Posologie</label>
                <input type="text" id="med_dosage___INDEX__" name="medications[__INDEX__][dosage]" class="form-control" placeholder="Ex: 500mg, 1 comprimé">
            </div>
        </div>
        <div class="row"> {{-- medication-item-row specific grid --}}
            <div class="col-md-4 form-group mb-2">
                <label for="med_freq___INDEX__">Fréquence</label>
                <input type="text" id="med_freq___INDEX__" name="medications[__INDEX__][frequency]" class="form-control" placeholder="Ex: 3 fois/jour">
            </div>
            <div class="col-md-4 form-group mb-2">
                <label for="med_duration___INDEX__">Durée</label>
                <input type="text" id="med_duration___INDEX__" name="medications[__INDEX__][duration]" class="form-control" placeholder="Ex: 7 jours">
            </div>
            <div class="col-md-4 form-group mb-2">
                <label for="med_notes___INDEX__">Notes Spécifiques</label>
                <input type="text" id="med_notes___INDEX__" name="medications[__INDEX__][notes]" class="form-control" placeholder="Ex: Après repas">
            </div>
        </div>
        <button type="button" class="btn btn-sm btn-danger remove-medication-row-btn mt-2">Retirer ce médicament</button>
    </div>
</template>
