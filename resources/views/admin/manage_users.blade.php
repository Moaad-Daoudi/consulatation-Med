@extends('layouts.admin_dashboard')

@section('title', 'Manage Users')

@section('content')
    @if (session('success'))
        <div id="flash-message" class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <div class="users-managements-header">
        <h2>Users Management</h2>
        <button onclick="openUserModal()" class="btn-create">+ Create New User</button>
    </div>
    {{-- Filter Users --}}
    @include('admin.partials.filter_manage_users')

    {{-- Modal User --}}
    @include('admin.partials.modal_user')

    <!-- View User Modal -->
    @include('admin.partials.view_user_modal')

    {{-- Users Table --}}
    @include('admin.partials.users_table')

    {{-- Pagination Links --}}
    @include('layouts.pagination')
@endsection