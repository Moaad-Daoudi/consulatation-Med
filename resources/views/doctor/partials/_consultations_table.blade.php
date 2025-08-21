{{-- File: resources/views/doctor/partials/_consultations_table.blade.php --}}

{{-- Using a wrapper div for responsiveness is a standard and good practice --}}
<div class="table-responsive mt-3">
    <table class="table consultations-table">
        <thead class="table-header">
            <tr>
                <th class="consultation-date-header">Date & Heure</th>
                <th class="consultation-patient-header">Patient</th>
                <th class="consultation-reason-header">Motif</th>
                <th class="consultation-actions-header">Actions</th>
            </tr>
        </thead>
        <tbody>
            {{--
              The @forelse directive is perfect here. It loops through the collection
              and provides a fallback @empty state if the collection is empty.
            --}}
            @forelse($consultations as $consultation)
                <tr class="consultation-item-row">
                    <td class="consultation-date">
                        {{ $consultation->consultation_date->format('d/m/Y H:i') }}
                    </td>
                    <td class="consultation-patient">
                        {{ $consultation->patient->name ?? 'N/A' }}
                    </td>
                    <td class="consultation-reason">
                        {{ Str::limit($consultation->reason_for_visit, 40) }}
                    </td>
                    <td class="consultation-actions">
                        {{-- The buttons and forms remain exactly the same --}}
                        <button type="button" class="btn btn-sm btn-info view-consultation-btn" 
                                data-details='@json($consultation)'>Voir</button>
                        
                        <button type="button" class="btn btn-sm btn-warning edit-consultation-btn"
                                data-details='@json($consultation)'>Modifier</button>
                        
                        <form action="{{ route('doctor.consultations.destroy', $consultation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette consultation ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Suppr.</button>
                        </form>
                    </td>
                </tr>
            @empty
                {{-- This is the content for the @empty state --}}
                <tr>
                    {{-- The colspan="4" makes this single cell span all 4 columns of the table --}}
                    <td colspan="4" class="text-center p-3">
                        Aucune consultation trouvée.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>