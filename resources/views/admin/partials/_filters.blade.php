<div class="filter-bar">
    <form action="{{ route('admin.appointments.index') }}" method="GET">
        
        {{-- Search Input --}}
        <input type="text" name="patient_search" placeholder="Search by patient..." value="{{ request('patient_search') }}" class="search-input">
        
        {{-- Doctor Select --}}
        <select name="doctor_id">
            <option value="">All Doctors</option>
            @foreach($doctors as $doctor)
                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>Dr. {{ $doctor->name }}</option>
            @endforeach
        </select>
        
        {{-- Date Input --}}
        <input type="date" name="appointment_date" value="{{ request('appointment_date') }}" class="date-input">
        
        {{-- Status Select --}}
        <select name="status">
            <option value="">All Statuses</option>
            <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="missed" {{ request('status') == 'missed' ? 'selected' : '' }}>Missed</option>
        </select>
        
        {{-- Action Buttons --}}
        <button type="submit" class="btn-filter">Apply</button>
        <a href="{{ route('admin.appointments.index') }}" class="btn-cancel">Clear</a>
    </form>
</div>