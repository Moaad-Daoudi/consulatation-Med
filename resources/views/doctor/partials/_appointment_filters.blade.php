{{-- File: resources/views/doctor/partials/_appointment_filters.blade.php --}}

<form method="GET" action="{{ route('doctor.appointments') }}" class="mb-3 form-inline" id="filter-appointments-form">
    <div class="form-group">
        <label for="filter_date_doc_appt" class="sr-only">Date:</label>
        {{-- The value attribute retains the selected filter after submission --}}
        <input type="date" name="filter_date" id="filter_date_doc_appt" class="form-control form-control-sm" value="{{ request('filter_date') }}">
    </div>
    <div class="form-group">
        <label for="filter_period_doc_appt" class="sr-only">Période:</label>
        <select name="filter_period" id="filter_period_doc_appt" class="form-control form-control-sm">
            <option value="">Filtrer par période...</option>
            <option value="today" {{ request('filter_period') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
            <option value="this_week" {{ request('filter_period') == 'this_week' ? 'selected' : '' }}>Cette semaine</option>
            <option value="this_month" {{ request('filter_period') == 'this_month' ? 'selected' : '' }}>Ce mois</option>
        </select>
    </div>
    <button type="submit" class="btn btn-sm btn-primary">Filtrer</button>
    <a href="{{ route('doctor.appointments') }}" class="btn btn-sm btn-secondary ml-2">Effacer</a>
</form>