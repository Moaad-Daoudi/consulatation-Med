<div class="filter-bar">
    <form action="{{ route('admin.manage_users') }}" method="GET">

        {{-- Search Input --}}
        <div class="filter-group">
            <label for="search">Search</label>
            <input type="text" id="search" name="search" placeholder="By name or email..." class="search-input" value="{{ request('search') }}">
        </div>
        
        {{-- Role Filter Dropdown --}}
        <div class="filter-group">
            <label for="role">Role</label>
            <select id="role" name="role">
                <option value="">All Roles</option>
                <option value="1" @selected(request('role') == '1')>Admin</option>
                <option value="2" @selected(request('role') == '2')>Doctor</option>
                <option value="3" @selected(request('role') == '3')>Patient</option>
            </select>
        </div>

        {{-- Submit Button --}}
        <button type="submit" class="btn-filter">Apply Filters</button>
        
        {{-- A link to clear all filters --}}
        <a href="{{ route('admin.manage_users') }}" class="btn-clear-filter">Clear</a>
    </form>
</div>