<div class="table-responsive mt-3">
    <table class="table prescriptions-table">
        <thead>
            <tr>
                {{-- Column widths can be omitted if you want them to be automatic --}}
                <th style="width: 15%;">Date</th>
                <th style="width: 35%;">Patient</th>
                <th class="text-center" style="width: 20%;">Nb. Médicaments</th>
                <th class="text-center" style="width: 30%;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($prescriptions as $prescription)
                <tr>
                    <td>{{ $prescription->prescription_date->format('d/m/Y') }}</td>
                    <td>{{ $prescription->patient->name ?? 'N/A' }}</td>
                    <td class="text-center">{{ $prescription->items_count }}</td>
                    {{-- This cell now uses the prescription-actions class for flexbox alignment --}}
                    <td class="prescription-actions">
                        <button type="button" class="btn btn-sm btn-info view-prescription-btn"
                                data-url="{{ route('doctor.prescriptions.show', $prescription->id) }}">
                            Voir
                        </button>
                        
                        <button type="button" class="btn btn-sm btn-warning edit-prescription-btn"
                                data-edit-url="{{ route('doctor.prescriptions.edit', $prescription->id) }}">
                            Modifier
                        </button>
                        
                        <form action="{{ route('doctor.prescriptions.destroy', $prescription->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette ordonnance ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Suppr.</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center p-3">Aucune ordonnance trouvée.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>