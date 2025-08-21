@extends('layouts.admin_dashboard')

@section('title', 'Dashboard')

@section('content')
    <div class="dashboard">
        <div class="dashboard-stat">
            <div class="card-stat card-users">
                <div class="card-icon">
                    <img src="{{ asset('sidebar/messages.png') }}" alt="icon-doctor">
                </div>
                <div class="card-content">
                    <h3>10</h3>
                    <p>Approved Users</p>
                </div>
            </div>
            <div class="card-stat card-doctors">
                <div class="card-icon">
                    <img src="{{ asset('sidebar/messages.png') }}" alt="icon-doctor">
                </div>
                <div class="card-content">
                    <h3>10</h3>
                    <p>Total Doctors</p>
                </div>
            </div>
            <div class="card-stat card-patients">
                <div class="card-icon">
                    <img src="{{ asset('sidebar/messages.png') }}" alt="icon-doctor">
                </div>
                <div class="card-content">
                    <h3>10</h3>
                    <p>Total Patients</p>
                </div>
            </div>
            <div class="card-stat card-appointments">
                <div class="card-icon">
                    <img src="{{ asset('sidebar/messages.png') }}" alt="icon-doctor">
                </div>
                <div class="card-content">
                    <h3>10</h3>
                    <p>Total appointments</p>
                </div>
            </div>
        </div>
        <div class="appointments">
            <h2 class="section-title">Recent Appointments</h2>
            <div class="users-table-container">
                <table class="tables">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>patient1</td>
                            <td>doctor1</td>
                            <td>14/07/2025</td>
                            <td class="cell-center">
                                <span class="status-badge status-scheduled">scheduled</span>
                            </td>
                        </tr>
                        <tr>
                            <td>patient2</td>
                            <td>doctor1</td>
                            <td>14/07/2025</td>
                            <td class="cell-center">
                                <span class="status-badge status-scheduled">scheduled</span>
                            </td>
                        </tr>
                        <tr>
                            <td>patient3</td>
                            <td>doctor1</td>
                            <td>14/07/2025</td>
                            <td class="cell-center">
                                <span class="status-badge status-completed">Completed</span>
                            </td>
                        </tr>
                        <tr>
                            <td>patient4</td>
                            <td>doctor2</td>
                            <td>14/07/2025</td>
                            <td class="cell-center">
                                <span class="status-badge status-scheduled">scheduled</span>
                            </td>
                        </tr>
                        <tr>
                            <td>patient5</td>
                            <td>doctor2</td>
                            <td>14/07/2025</td>
                            <td class="cell-center">
                                <span class="status-badge status-completed">Completed</span>
                            </td>
                        </tr>
                        <tr>
                            <td>patient6</td>
                            <td>doctor3</td>
                            <td>14/07/2025</td>
                            <td class="cell-center">
                                <span class="status-badge status-completed">Completed</span>
                            </td>
                        </tr>
                        <tr>
                            <td>patient6</td>
                            <td>doctor4</td>
                            <td>14/07/2025</td>
                            <td class="cell-center">
                                <span class="status-badge status-scheduled">scheduled</span>
                            </td>
                        </tr>
                        <tr>
                            <td>patient7</td>
                            <td>doctor3</td>
                            <td>14/07/2025</td>
                            <td class="cell-center">
                                <span class="status-badge status-completed">Completed</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination-appointments">
                <p>pagination</p>
            </div>
        </div>
        <div class="users">
            <h2 class="section-title">Latest Registered Users</h2>
            <div class="appointments-table-container">
                <table class="tables">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Role</th>
                            <th>Email</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>patient1</td>
                            <td class="cell-center">
                                <span class="role-badge role-patient">Patient</span>
                            </td>
                            <td>moaad@gmail.com</td>
                            <td>14/07/2025</td>
                        </tr>
                        <tr>
                            <td>doctor1</td>
                            <td class="cell-center">
                                <span class="role-badge role-doctor">Doctor</span>
                            </td>
                            <td>moaad@gmail.com</td>
                            <td>14/07/2025</td>
                        </tr>
                        <tr>
                            <td>Admin User</td>
                            <td class="cell-center">
                                <span class="role-badge role-admin">Admin</span>
                            </td>
                            <td>admin@example.com</td>
                            <td>12/07/2025</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="pagination-users">
                <p>pagination</p>
            </div>
        </div>
    </div>
@endsection