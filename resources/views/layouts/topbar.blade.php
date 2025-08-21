<div class="topbar">
    <h1 class="page-title">@yield('title')</h1>
    @auth
        <div class="topbar-actions">
            <div class="user-photo">
                {{ Auth::user()->initials }}
            </div>
            <div>
                {{ ucwords(Auth::user()->name) }}
            </div>
        </div>
    @endauth
</div>