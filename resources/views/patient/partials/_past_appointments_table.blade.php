<div class="table-responsive">
    <table class="table">
        <thead>
            <tr>
                <th>Date & Heure</th>
                <th>Docteur</th>
                <th>Motif</th>
                <th class="text-center">Statut</th>
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
                </tr>
            @empty
                <tr><td colspan="4" class="text-center p-3">Aucun rendez-vous dans l'historique.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>