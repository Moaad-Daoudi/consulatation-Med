<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Date & Heure</th>
                <th>Docteur</th>
                <th>Motif</th>
                <th class="text-center">Statut</th>
                <th class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->appointment_datetime->isoFormat('D MMM YYYY, HH:mm') }}</td>
                    <td>Dr. {{ $appointment->doctor->name ?? 'N/A' }}</td>
                    <td>{{ Str::limit($appointment->notes, 40) ?: 'Consultation' }}</td>
                    <td class="text-center">
                        <span class="appointment-status status-{{ strtolower($appointment->status) }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($appointment->status === 'scheduled' && $appointment->appointment_datetime->isFuture())
                            <form action="{{ route('patient.appointments.destroy', $appointment->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce rendez-vous ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Annuler</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center">Aucun rendez-vous à venir.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>