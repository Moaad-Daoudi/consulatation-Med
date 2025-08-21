<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Espace Patient') | MediConsult</title>
    <link rel="stylesheet" href="{{ asset('css/patient_dashboard.css') }}">
</head>

<body>
    <div class="dashboard-layout">
        @include('layouts.sidebars.patient')

        <main class="main-content-area">
            @include('layouts.topbar')
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>

    <script>
        // This provides the necessary backend data to our JavaScript file.
        window.patientConfig = {
            routes: {
                // This is needed for the "Create Appointment" modal to fetch available slots.
                availableSlots: "{{ route('appointments.available_slots') }}"
            },
            // We pass 'old' input so the form can be re-populated correctly after a validation error.
            oldInput: @json(session()->getOldInput()),
            csrfToken: "{{ csrf_token() }}"
        };
    </script>
    <script src="{{ asset('js/patient_dashboard.js') }}" defer></script>
</body>

</html>