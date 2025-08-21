@extends('layouts.doctor_dashboard')

@section('title', 'Gestion des Ordonnances')

@section('content')

    @if (session('success'))
        <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Erreurs de validation:</strong>
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- The container for the creation form --}}
    <div class="ordonnance-container create-prescription-section mb-5">
        <h2 class="section-title">Créer une nouvelle ordonnance</h2>
        @include('doctor.partials._prescription_form')
    </div>

    <hr class="my-5">

    {{-- The container for the history table --}}
    <div class="prescription-history-section ordonnance-container">
        <h2 class="section-title">Historique des Ordonnances</h2>
        @include('doctor.partials._prescriptions_table', ['prescriptions' => $prescriptions])

        <div class="mt-4">
            {{ $prescriptions->links() }}
        </div>
    </div>

    @include('doctor.partials._prescription_view_modal')
    @include('doctor.partials._prescription_edit_modal')

@endsection