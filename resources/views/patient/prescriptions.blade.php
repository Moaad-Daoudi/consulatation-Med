<div id="patient_prescriptions_content" class="content-section">
    <div class="medical-file-section-container mb-4">
        <h2 class="section-title">Mes Ordonnances Actives</h2>
        @if(isset($activePrescriptions) && $activePrescriptions->count() > 0)
            @foreach($activePrescriptions as $prescription)
                <div class="medical-entry-card">
                    <div class="entry-header">
                        <h5>Ordonnance du {{ $prescription->prescription_date->format('d/m/Y') }}</h5>
                        @if($prescription->doctor)
                            <span class="doctor-name">Prescrite par Dr. {{ $prescription->doctor->name }}</span>
                        @endif
                    </div>

                    @if($prescription->consultation)
                        <p class="text-muted mb-2" style="font-size:0.9em;">
                            Suite à la consultation du {{ \Carbon\Carbon::parse($prescription->consultation->consultation_date)->format('d/m/Y') }}
                            (Motif: {{ Str::limit($prescription->consultation->reason_for_visit, 40) }})
                        </p>
                    @endif

                    @if($prescription->general_notes)
                        <div class="entry-detail">
                            <strong>Notes Générales:</strong>
                            <div class="detail-content">{{ $prescription->general_notes }}</div>
                        </div>
                    @endif

                    @if($prescription->items && $prescription->items->count() > 0)
                        <div class="entry-detail mt-3">
                            <strong>Médicaments:</strong>
                            <ul class="list-unstyled mt-2">
                                @foreach($prescription->items as $item)
                                    <li class="medication-list-item">
                                        <span class="med-name">{{ $item->medication_name }}</span>
                                        <span class="med-details">
                                            @if($item->dosage) Dose: {{ $item->dosage }}. @endif
                                            @if($item->frequency) Fréquence: {{ $item->frequency }}. @endif
                                            @if($item->duration) Durée: {{ $item->duration }}. @endif
                                        </span>
                                        @if($item->notes)<span class="med-notes">Instructions: {{ $item->notes }}</span>@endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p>Aucun médicament spécifique listé pour cette ordonnance (vérifiez les notes générales).</p>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-center py-3">Aucune ordonnance active pour le moment.</p>
        @endif
    </div>

    <div class="medical-file-section-container">
        <h2 class="section-title">Historique des Ordonnances Passées</h2>
        @if(isset($pastPrescriptions) && $pastPrescriptions->count() > 0)
            @foreach($pastPrescriptions as $prescription)
                <div class="medical-entry-card">
                    <div class="entry-header">
                        <h5>Ordonnance du {{ $prescription->prescription_date->format('d/m/Y') }}</h5>
                        @if($prescription->doctor)
                            <span class="doctor-name">Prescrite par Dr. {{ $prescription->doctor->name }}</span>
                        @endif
                    </div>
                     @if($prescription->consultation)
                        <p class="text-muted mb-2" style="font-size:0.9em;">
                            Suite à la consultation du {{ \Carbon\Carbon::parse($prescription->consultation->consultation_date)->format('d/m/Y') }}
                            (Motif: {{ Str::limit($prescription->consultation->reason_for_visit, 40) }})
                        </p>
                    @endif
                    @if($prescription->general_notes)
                        <div class="entry-detail">
                            <strong>Notes Générales:</strong>
                            <div class="detail-content">{{ $prescription->general_notes }}</div>
                        </div>
                    @endif
                    @if($prescription->items && $prescription->items->count() > 0)
                         <div class="entry-detail mt-3">
                            <strong>Médicaments:</strong>
                            <ul class="list-unstyled mt-2">
                                @foreach($prescription->items as $item)
                                     <li class="medication-list-item">
                                        <span class="med-name">{{ $item->medication_name }}</span>
                                        <span class="med-details">
                                            @if($item->dosage) Dose: {{ $item->dosage }}. @endif
                                            @if($item->frequency) Fréquence: {{ $item->frequency }}. @endif
                                            @if($item->duration) Durée: {{ $item->duration }}. @endif
                                        </span>
                                        @if($item->notes)<span class="med-notes">Instructions: {{ $item->notes }}</span>@endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-center py-3">Aucun historique d'ordonnances passées.</p>
        @endif
    </div>
</div>
