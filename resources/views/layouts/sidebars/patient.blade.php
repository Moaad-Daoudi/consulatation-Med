<aside class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('patient.dashboard') }}" class="logo">Medi<span>Consult</span></a>
    </div>
    
    @auth
    <div class="user-info">
        <div class="user-avatar">
            @if(Auth::user()->photo_path)
                <img src="{{ Auth::user()->photo_url }}" alt="Avatar">
            @else
                {{ Auth::user()->initials }}
            @endif
        </div>
        <div class="user-name">{{ ucwords(Auth::user()->name) }}</div>
        @if(Auth::user()->role)
            {{-- THIS IS THE CORRECTED LINE BASED ON YOUR PROJECT'S STRUCTURE --}}
            <div class="user-role">{{ Str::ucfirst(Auth::user()->role->role) }}</div>
        @endif
    </div>    
    @endauth

    <ul class="sidebar-menu">
        <li>
            <a href="{{ route('patient.dashboard') }}" class="menu-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}">
                <div class="menu-icon"><img src="{{ asset('sidebar/tableau_de_bord.png') }}" alt="Dashboard Icon"></div>
                <span>Tableau de bord</span>
            </a>
        </li>
        <li>
            <a href="{{ route('patient.appointments.index') }}" class="menu-link {{ request()->routeIs('patient.appointments.index') ? 'active' : '' }}">
                <div class="menu-icon"><img src="{{ asset('sidebar/rendez_vous.png') }}" alt="Appointments Icon"></div>
                <span>Mes rendez-vous</span>
            </a>
        </li>
        <li>
            <a href="{{ route('patient.dossier_medical') }}" class="menu-link {{ request()->routeIs('patient.dossier_medical') ? 'active' : '' }}">
                <div class="menu-icon"><img src="{{ asset('sidebar/dossier_medical.png') }}" alt="Dossier Médical Icon"></div>
                <span>Dossier Médical</span>
            </a>
        </li>
        <li>
            <a href="{{ route('patient.prescriptions.index') }}" class="menu-link {{ request()->routeIs('patient.prescriptions.index') ? 'active' : '' }}">
                <div class="menu-icon"><img src="{{ asset('sidebar/ordonnances.png') }}" alt="Prescriptions Icon"></div>
                <span>Mes ordonnances</span>
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('logout') }}" id="logout-form-patient">
                @csrf
                <a href="{{ route('logout') }}" class="menu-link" onclick="event.preventDefault(); document.getElementById('logout-form-patient').submit();">
                    <div class="menu-icon"><img src="{{ asset('sidebar/logout.png') }}" alt="Logout Icon"></div>
                    <span>Déconnexion</span>
                </a>
            </form>
        </li>
    </ul>
</aside>