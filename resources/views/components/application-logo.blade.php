@php
    $logoStyle = request()->routeIs('dashboard')
        ? 'width: 100px; height: auto;'  // smaller on dashboard
        : 'width: 200px; height: auto;'; // default size
@endphp

<img src="{{ asset('logo.png') }}" alt="My Logo" style="{{ $logoStyle }}" {{ $attributes }}>