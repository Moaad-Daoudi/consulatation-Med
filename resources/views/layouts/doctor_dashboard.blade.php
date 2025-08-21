<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <link rel="stylesheet" href="{{ asset('css/doctor_dashboard.css') }}">
</head>
<body>
    <div class="dashboard-layout">
        @include('layouts.sidebars.doctor')
        <main class="main-content-area">
            @include('layouts.topbar')
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>
    <script>
        window.doctorDashboardConfig = {
            routes: {
                logout: "{{ route('logout') }}",
                availableSlots: "{{ route('appointments.available_slots') }}",
                patientDossierBaseUrl: "{{ url('doctor/patients') }}",
                consultationsBaseUrl: "{{ url('doctor/consultations') }}",
                consultationsForLinkingBaseUrl: "{{ url('doctor/patients') }}",
                prescriptionsBaseUrl: "{{ url('doctor/prescriptions') }}"
            },
            session: {
                activeSectionOnLoad: @json(session('active_section_on_load')),
                openModalOnLoad: @json(session('open_modal_on_load')),
                consultationIdForErrorBag: @json(session('consultation_id_for_error_bag')),
                prescriptionIdForErrorBag: @json(session('prescription_id_for_error_bag')),
                editingPrescription: @json(session('editing_prescription'))
            },
            auth: {
                userName: @json(Auth::user()->name)
            },
            oldInput: {
                appointmentTime: @json(old('appointment_time', '')),
                patientId: @json(old('patient_id')),
                consultationId: @json(old('consultation_id'))
            },
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    <script src="{{ asset('js/doctor_dashboard.js') }}" defer></script>

</body>
</html>