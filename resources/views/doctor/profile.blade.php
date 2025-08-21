@extends('layouts.doctor_dashboard')

@section('title', 'Mon Profil')

@section('content')

<div class="profile-container ordonnance-container"> {{-- Reusing a container style --}}

    <div class="patients-header">
        <h2 class="section-title">Mon Profil et Paramètres</h2>
    </div>

    {{-- Success Message --}}
    @if (session('success'))
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
    @endif

    {{-- Validation Errors --}}
    @if ($errors->any())
    <div class="alert alert-danger">
        <strong>Oups !</strong> Il y avait quelques problèmes avec votre saisie :
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form class="modal-form" {{-- Reusing the 2-column grid style --}} method="POST"
        action="{{ route('doctor.profile.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')

        {{-- User Information --}}
        <div class="form-group full-width">
            <label for="profile-name">Nom Complet *</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="profile-name" name="name"
                value="{{ old('name', $doctorUser->name) }}" required>
        </div>

        <div class="form-group">
            <label for="profile-email">Adresse Email *</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="profile-email"
                name="email" value="{{ old('email', $doctorUser->email) }}" required>
        </div>

        <div class="form-group">
            <label for="profile-phone_number">Numéro de Téléphone</label>
            <input type="tel" class="form-control @error('phone_number') is-invalid @enderror" id="profile-phone_number"
                name="phone_number" value="{{ old('phone_number', $doctorUser->doctor->phone_number ?? '') }}">
        </div>

        {{-- Doctor Specific Information --}}
        <div class="form-group">
            <label for="profile-specialty">Spécialité *</label>
            <input type="text" class="form-control @error('specialisation') is-invalid @enderror" id="profile-specialty"
                name="specialisation" value="{{ old('specialisation', $doctorUser->doctor->specialisation ?? '') }}"
                required>
        </div>

        <div class="form-group">
            <label for="profile-gender">Sexe *</label>
            <select class="form-control @error('gender') is-invalid @enderror" id="profile-gender" name="gender"
                required>
                <option value="">Sélectionner...</option>
                <option value="male" {{ old('gender', $doctorUser->doctor->gender ?? '') == 'male' ? 'selected' : ''
                    }}>Homme</option>
                <option value="female" {{ old('gender', $doctorUser->doctor->gender ?? '') == 'female' ? 'selected' : ''
                    }}>Femme</option>
                <option value="other" {{ old('gender', $doctorUser->doctor->gender ?? '') == 'other' ? 'selected' : ''
                    }}>Autre</option>
            </select>
        </div>

        <div class="form-group full-width">
            <label for="profile-bio">Biographie / Description</label>
            <textarea class="form-control @error('biography') is-invalid @enderror" id="profile-bio" name="biography"
                rows="4">{{ old('biography', $doctorUser->doctor->biography ?? '') }}</textarea>
        </div>

        {{-- Photo Upload --}}
        <div class="form-group full-width" style="display: flex; align-items: center; gap: 20px;">
            <div>
                <label>Photo Actuelle</label>
                {{-- Use the photo_url accessor from the User model --}}
                <img src="{{ $doctorUser->photo_url }}" alt="Avatar"
                    style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
            </div>
            <div style="flex-grow: 1;">
                <label for="profile-photo">Changer la Photo (Max 2Mo)</label>
                <input type="file" class="form-control" id="profile-photo" name="photo" accept="image/*">
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="form-actions full-width" style="margin-top: 25px;">
            <button type="submit" class="btn btn-primary">Enregistrer les Modifications</button>
        </div>
    </form>
</div>

@endsection