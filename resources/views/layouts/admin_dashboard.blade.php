<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <title>@yield('title')</title>
</head>
<body>
    <div class="dashboard-layout">
        @include('layouts.sidebars.admin')
        <main>
            @include('layouts.topbar')
            <div class="content-wrapper">
                @yield('content')
            </div>
        </main>
    </div>
    @stack('page-config')
    <script src="{{ asset('js/admin_dashboard.js') }}" defer></script> {{-- defer : It tells the browser to download the script in parallel while the page is still parsing, but to wait to execute it until after the HTML is fully loaded --}}
</body>
</html>