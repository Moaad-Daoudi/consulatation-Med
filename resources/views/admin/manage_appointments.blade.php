@extends('layouts.admin_dashboard')

@section('title', 'Appointment Management')

@section('content')
    @if (session('success'))
        <div class="alert alert-success" role="alert" id="flash-message">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger" id="flash-message">
            @foreach ($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    {{-- Page Header --}}
    <div class="appointments-managements-header">
        <h2>Appointment Management</h2>
        <button onclick="openAppointmentModal()" class="btn-create">+ Create Appointment</button>
    </div>

    {{-- Filter and Search Bar --}}
    @include('admin.partials._filters')

    {{-- Appointments Table --}}
    @include('admin.partials._table')

    {{-- Pagination Links --}}
    <div class="pagination-container mt-4">
        {{ $appointments->appends(request()->query())->links() }}
    </div>

    {{-- Include Modals --}}
    @include('admin.partials._appointment_modal')
    @include('admin.partials._delete_modal')
@endsection

@push('page-config')
    <script>
        window.adminAppointmentConfig = {
            appointments: @json($appointments->keyBy('id')),
            storeUrl: "{{ route('admin.appointments.store') }}",
            updateUrlTemplate: "{{ route('admin.appointments.update', ['appointment' => ':id']) }}",
            deleteUrlTemplate: "{{ route('admin.appointments.destroy', ['appointment' => ':id']) }}",
            availableSlotsUrl: "{{ route('appointments.available_slots') }}"
        };
    </script>
@endpush
