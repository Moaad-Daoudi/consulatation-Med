<!-- The Modal -->
<div class="modal-user" id="UserModal">
    <div class="modal-content">

        <div class="modal-header">
            <h2>Create a New User</h2>
            <button class="close" onclick="closeModal()">×</button>
        </div>

        <div class="modal-body">
            <form id="user-creation-form" data-action-create="{{ route('admin.users.store') }}" data-action-update-template="{{ route('admin.users.update', ['user' => ':userId']) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Full Name</label>
                        {{-- FIX: Correct Blade syntax for old() --}}
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        {{-- FIX: Correct Blade syntax for old() --}}
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="ps">Password</label>
                        <input type="password" id="ps" name="password" required>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation">Confirm Password</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                        @error('password_confirmation')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        {{-- This was already correct --}}
                        <select id="gender" name="gender" required>
                            <option value="" disabled selected>-- Select Gender --</option>
                            <option value="male" @selected(old('gender') == 'male')>Male</option>
                            <option value="female" @selected(old('gender') == 'female')>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="roles">Assign Role</label>
                         {{-- This was already correct --}}
                        <select name="role" id="roles" required>
                            <option value="" disabled selected>-- Choose Role --</option>
                            <option value="doctor" @selected(old('role') == 'doctor')>Doctor</option>
                            <option value="patient" @selected(old('role') == 'patient')>Patient</option>
                        </select>
                    </div>
                </div>

                <!-- Doctor specific fields -->
                <div id="doctorFields" style="display: none;">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="specialisation">Specialisation</label>
                             {{-- FIX: Correct Blade syntax for old() --}}
                            <input type="text" id="specialisation" name="specialisation" value="{{ old('specialisation') }}">
                            @error('specialisation')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="phone_number">Phone Number</label>
                             {{-- FIX: Correct Blade syntax for old() --}}
                            <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                            @error('phone_number')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="photo">Profile Photo</label>
                         {{-- FIX: Removed value attribute entirely --}}
                        <input type="file" id="photo" name="photo" accept="image/png, image/jpeg, image/gif">
                        @error('photo')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="bio">Biography</label>
                         {{-- FIX: Textarea content goes between tags, not in value attribute --}}
                        <textarea id="bio" name="biography" placeholder="A brief introduction...">{{ old('biography') }}</textarea>
                        @error('biography')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Patient specific fields -->
                <div id="patientFields" style="display: none;">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="date">Date of Birth</label>
                             {{-- FIX: Correct Blade syntax for old() --}}
                            <input id="date" type="date" name="date_of_birth" value="{{ old('date_of_birth') }}">
                            @error('date_of_birth')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="blood">Blood Type</label>
                             {{-- This was already correct --}}
                            <select id="blood" name="blood_type">
                                <option value="" disabled selected>-- Select Blood Type --</option>
                                <option value="A+" @selected(old('blood_type') == 'A+')>A+</option>
                                <option value="A-" @selected(old('blood_type') == 'A-')>A-</option>
                                <option value="B+" @selected(old('blood_type') == 'B+')>B+</option>
                                <option value="B-" @selected(old('blood_type') == 'B-')>B-</option>
                                <option value="AB+" @selected(old('blood_type') == 'AB+')>AB+</option>
                                <option value="AB-" @selected(old('blood_type') == 'AB-')>AB-</option>
                                <option value="O+" @selected(old('blood_type') == 'O+')>O+</option>
                                <option value="O-" @selected(old('blood_type') == 'O-')>O-</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button type="submit" class="btn-submit" form="user-creation-form">Create Account</button>
        </div>
    </div>
</div>