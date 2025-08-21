<aside>
    <div class="sidebar-header">
        <a href="{{ route('admin.dashboard')}}" class="logo">Medi<span>Consult</span></a>
    </div>
    @auth
    <div class="user-info">
        <div class="user-avatar">
            {{ Auth::user()->initials }}
        </div>
        <div class="user-name">
            {{ ucwords(Auth::user()->name) }}
        </div>
        <div class="user-role">
            {{ Str::ucfirst(Auth::user()->role->role) }}
        </div>
    </div>
    @endauth
    <div class="sidebar-menu">
        <ul>
            <li>
                <a href="{{ route('admin.dashboard') }}" class="menu-link @if(request()->routeIs('admin.dashboard')) active @endif">
                    <div class="menu-icon"><img src="{{ asset('sidebar/messages.png')}}" alt="1"></div>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.manage_users')}}" class="menu-link @if(request()->routeIs('admin.manage_users')) active @endif">
                    <div class="menu-icon"><img src="{{ asset('sidebar/messages.png')}}" alt="2"></div>
                    <span>Manage users</span>
                </a>
            </li>
            <li>
                <a href="{{ route('admin.appointments.index') }}" class="menu-link @if(request()->routeIs('admin.appointments.index')) active @endif">
                    <div class="menu-icon"><img src="{{ asset('sidebar/messages.png')}}" alt="3"></div>
                    <span>Manage appointments</span>
                </a>
            </li>
            <li>
                <form action="{{ route('logout')}}" method="POST" id="logout-form">
                        @csrf
                    </form>
                    <a href="{{ route('logout') }}" class="menu-link"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <div class="menu-icon"><img src="{{ asset('sidebar/logout.png')}}" alt="Logout Icon"></div>
                        <span>Logout</span>
                    </a>
            </li>
        </ul>
    </div>
</aside>