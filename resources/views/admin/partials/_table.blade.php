<div class="data-table-container">
    <table class="data-table">
        <thead>
            <tr>
                <th>Patient</th>
                <th>Doctor</th>
                <th>Date & Time</th>
                <th class="text-center">Status</th>
                <th class="text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($appointments as $appointment)
            <tr>
                <td>{{ $appointment->patient->name ?? 'N/A' }}</td>
                <td>Dr. {{ $appointment->doctor->name ?? 'N/A' }}</td>
                <td>{{ $appointment->appointment_datetime->format('d M, Y - H:i') }}</td>
                <td class="text-center"><span class="status-badge-table status-{{ strtolower($appointment->status) }}">{{ ucfirst($appointment->status) }}</span></td>
                <td class="text-right">
                    <div class="action-buttons">
                        <button onclick="openAppointmentModal({{ $appointment->id }})" class="btn-action btn-edit" title="Edit">Edit</button>
                        <button onclick="openDeleteModal({{ $appointment->id }})" class="btn-action btn-delete" title="Delete">Delete</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="no-results">No appointments found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>