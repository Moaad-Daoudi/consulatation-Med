<div class="users-table-container">
    <table class="users-table">
        <thead>
            <tr>
                <th>User</th>
                <th>Role</th>
                <th>Joined On</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($users as $user)
                <tr>
                    <td>
                        <div class="user-info-cell">
                                <img src="{{ $user->photo_url }}" alt="User Avatar" class="user-avatar-table">
                                <span class="user-name-table">{{ $user->name }}</span>
                                <span class="user-email-table">{{ $user->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="role-badge {{ $user->role_class }}">{{ $user->role->role }}</span>
                    <td>{{ $user->created_at->format('d M, Y') }}</td>
                    <td>
                        <div class="action-buttons">
                            <a href="#" class="btn-action btn-view" onclick="openViewModal({{ $user->id }})" title="View Details">view</a>
                            <button onclick="openUserModal({{ $user->id }})" class="btn-action btn-edit" title="Edit User">edit</button>
                            <button class="btn-action btn-delete" onclick="confirmDelete({{ $user->id }})" title="Delete User">delete</button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="no-results">No users found matching your criteria.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
