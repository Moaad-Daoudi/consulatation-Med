<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Date & Heure</th>
                <th>Patient</th>
                <th>Type/Notes</th>
                <th>Statut</th>
                <th class="actions-column">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($appointments as $appointment)
                <tr>
                    <td>{{ $appointment->appointment_datetime->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="user-info-cell">
                            <div class="user-text">
                                <span class="user-name-table">{{ $appointment->patient->name ?? 'Patient Inconnu' }}</span>
                                <span class="user-email-table">{{ $appointment->patient->email ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td>{{ Str::limit($appointment->notes, 40) ?: 'Consultation' }}</td>
                    <td>
                        <span class="status-badge-table status-{{ strtolower($appointment->status) }}">
                            {{ ucfirst($appointment->status) }}
                        </span>
                    </td>
                    <td class="action-buttons">
                        @if($appointment->status === 'scheduled')
                            <form action="{{ route('doctor.appointments.complete', $appointment->id) }}" method="POST" class="action-form">
                                @csrf
                                @method('PATCH')
                                {{-- CHANGED: Icon replaced with text and a new class --}}
                                <button type="submit" class="btn-action btn-complete" title="Marquer comme terminé">Terminé</button>
                            </form>
                        @endif

                        <form action="{{ route('doctor.appointments.destroy', $appointment->id) }}" method="POST" class="action-form" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rendez-vous ?');">
                            @csrf
                            @method('DELETE')
                            {{-- CHANGED: Icon replaced with text and a new class --}}
                            <button type="submit" class="btn-action btn-delete" title="Supprimer ce RDV">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="no-results">
                        Aucun rendez-vous trouvé correspondant à vos filtres.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>