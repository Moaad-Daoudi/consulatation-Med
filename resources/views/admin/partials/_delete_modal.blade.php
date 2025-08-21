<div class="modal-user" id="deleteAppointmentModal">
    <div class="modal-content modal-small">
         <div class="modal-header">
            <h2>Confirm Deletion</h2>
            <button class="close" onclick="closeDeleteModal()">×</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this appointment? This action cannot be undone.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
            {{-- The form's action will be set by JavaScript --}}
            <form id="deleteAppointmentForm" method="POST" action="">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-submit btn-danger">Delete</button>
            </form>
        </div>
    </div>
</div>