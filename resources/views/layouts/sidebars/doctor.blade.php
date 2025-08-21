<aside class="sidebar">
    <div class="sidebar-header">
        <a href="{{ route('doctor.dashboard') }}" class="logo">Medi<span>Consult</span></a>
    </div>
    @auth
    <div class="user-info">
        <div class="user-avatar">{{ Auth::user()->initials }}</div>
        <div class="user-name">{{ ucwords(Auth::user()->name) }}</div>
        @if(Auth::user()->role)
            <div class="user-role">{{ Str::ucfirst(Auth::user()->role->role) }}</div>
        @endif
    </div>
    @endauth
    <div class="sidebar-menu">
        <ul>
            <li>
                {{-- The 'active' class is now controlled by the server --}}
                <a href="{{ route('doctor.dashboard') }}" class="menu-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}">
                    <div class="menu-icon"><img src="{{ asset('sidebar/tableau_de_bord.png') }}" alt="Dashboard Icon"></div>
                    <span>Tableau de bord</span>
                </a>
            </li>
            <li>
                <a href="{{ route('doctor.appointments') }}" class="menu-link {{ request()->routeIs('doctor.appointments') ? 'active' : '' }}">
                    <div class="menu-icon"><img src="{{ asset('sidebar/rendez_vous.png') }}" alt="Appointments Icon"></div>
                    <span>Rendez-vous</span>
                </a>
            </li>
            <li>
                <a href="{{ route('doctor.patients') }}" class="menu-link {{ request()->routeIs('doctor.patients') ? 'active' : '' }}">
                    <div class="menu-icon"><img src="{{ asset('sidebar/patients.png') }}" alt="Patients Icon"></div>
                    <span>Patients</span>
                </a>
            </li>
            <li>
                <a href="{{ route('doctor.consultations.index') }}" class="menu-link {{ request()->routeIs('doctor.consultations.index') ? 'active' : '' }}">
                    <div class="menu-icon"><img src="{{ asset('sidebar/consultations.png') }}" alt="Consultations Icon"></div>
                    <span>Consultations</span>
                </a>
            </li>
            <li>
                {{-- Note: We assume you will create these routes in the next step --}}
                <a href="{{ route('doctor.prescriptions.index') }}" class="menu-link {{ request()->routeIs('doctor.prescriptions.index') ? 'active' : '' }}">
                    <div class="menu-icon"><img src="{{ asset('sidebar/ordonnances.png') }}" alt="Ordonnances Icon"></div>
                    <span>Ordonnances</span>
                </a>
            </li>
            <li>
                <a href="{{ route('doctor.profile.edit') }}" class="menu-link {{ request()->routeIs('doctor.profile.edit') ? 'active' : '' }}">
                    <div class="menu-icon"><img src="{{ asset('sidebar/profile.png') }}" alt="Profile Icon"></div>
                    <span>Profile</span>
                </a>
            </li>
            <li>
                {{-- The logout form remains the same, it works perfectly --}}
                <form method="POST" action="{{ route('logout') }}" id="logout-form-doctor-dashboard">@csrf</form>
                <a href="{{ route('logout') }}" class="menu-link"
                   onclick="event.preventDefault(); document.getElementById('logout-form-doctor-dashboard').submit();">
                    <div class="menu-icon"><img src="{{ asset('sidebar/logout.png') }}" alt="Logout Icon"></div>
                    <span>Déconnexion</span>
                </a>
            </li>
        </ul>
    </div>
</aside>