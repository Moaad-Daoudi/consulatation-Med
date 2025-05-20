<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>MediConsult - Dashboard Médecin</title>
    <style>
        /* Variables et styles de base */
        :root {
            --primary: #1976d2; --primary-light: #4791db; --primary-dark: #115293;
            --secondary: #43a047; --secondary-light: #76d275; --secondary-dark: #2d7031; /* Green for completed */
            --danger: #e53935; --danger-dark: #c02320; /* Red for cancelled */
            --warning: #ffb74d; --warning-dark: #ffaa00;
            --info: #00bcd4;  --info-dark: #00acc1;/* Example for info buttons */
            --text-dark: #333; --text-light: #f5f5f5;
            --bg-light: #f8f9fa; --bg-white: #ffffff; --shadow: 0 2px 5px rgba(0,0,0,0.1);

            /* Colors for Stat Card Borders & Titles (Adjust as needed) */
            --color-appointments: #FFA500;
            --color-patients: #4CAF50;
            --color-prescriptions: #2196F3;
            --color-messages: #F44336;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); line-height: 1.6; }
        a { text-decoration: none; color: var(--primary); cursor: pointer; }
        .dashboard-layout { display: flex; height: 100vh; overflow: hidden; }

        /* Sidebar */
        .sidebar { width: 280px; background-color: var(--primary-dark); color: var(--text-light); height: 100%; overflow-y: auto; position: fixed; left: 0; top: 0; z-index: 100; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu li a {
            display: flex;
            align-items: center; /* This will help vertically align icon and text */
            padding: 12px 20px;
            color: var(--text-light);
            transition: all 0.3s ease;
        }

        .menu-icon {
            margin-right: 10px;
            /* font-size: 1.2rem; -- This was for emoji font size, might not be needed or can be adjusted */
            display: flex; /* To help center the image if its container is larger */
            align-items: center;
            justify-content: center;
            width: 24px;  /* Set a fixed width for the icon container */
            height: 24px; /* Set a fixed height for the icon container */
            flex-shrink: 0; /* Prevent the icon container from shrinking */
        }

        .menu-icon img {
            max-width: 100%;  /* Ensures image scales down to fit container */
            max-height: 100%; /* Ensures image scales down to fit container */
            object-fit: contain; /* Scales image while maintaining aspect ratio, fitting within bounds */
            /* Or use:
            object-fit: cover; -- If you want image to fill bounds, potentially cropping
            width: 20px; -- If you want a fixed image size smaller than container
            height: 20px; -- If you want a fixed image size smaller than container
            */
            display: block; /* Removes any extra space below the image if it's treated as inline */
        }

        .logo { font-size: 24px; font-weight: bold; }
        .logo span { color: var(--secondary-light); }
        .user-info { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .user-avatar { width: 80px; height: 80px; border-radius: 50%; background-color: var(--primary-light); margin: 0 auto 10px; display: flex; align-items: center; justify-content: center; font-size: 2rem; text-transform: uppercase; }
        .user-name { font-weight: bold; margin-bottom: 5px; }
        .user-role { font-size: 0.9rem; opacity: 0.8; text-transform: capitalize; }
        .sidebar-menu { list-style: none; padding: 20px 0; flex-grow: 1; }
        .sidebar-menu li { margin-bottom: 5px; }
        .sidebar-menu li a { display: flex; align-items: center; padding: 12px 20px; color: var(--text-light); transition: all 0.3s ease; }
        .sidebar-menu li a:hover, .sidebar-menu li a.active { background-color: rgba(255,255,255,0.1); }
        .menu-icon { margin-right: 10px; font-size: 1.2rem; }

        /* Main Content & Topbar */
        .main-content { flex: 1; margin-left: 280px; padding: 0; overflow-y: auto; height: 100vh; display: flex; flex-direction: column; }
        .topbar { display: flex; justify-content: space-between; align-items: center; background-color: var(--bg-white); box-shadow: var(--shadow); padding: 15px 20px; position: sticky; top: 0; z-index: 90; }
        .page-title { font-size: 1.5rem; font-weight: 500; }
        .topbar-actions { display: flex; gap: 15px; align-items: center; }
        .notification-bell { position: relative; cursor: pointer; width: 40px; height: 40px; background-color: var(--bg-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .notification-badge { position: absolute; top: -5px; right: -5px; background-color: var(--danger); color: var(--text-light); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; }
        .user-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .user-profile-img { width: 40px; height: 40px; border-radius: 50%; background-color: var(--primary); color: var(--text-light); display: flex; align-items: center; justify-content: center; font-weight: bold; text-transform: uppercase; }
        .content-wrapper { padding: 20px; flex-grow: 1; overflow-y: auto; }

        /* Content Sections (SPA style) */
        .content-section { display: none; }
        .content-section.active { display: block; }

        /* --- NEW Stat Card Styles (Dashboard) --- */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: var(--bg-white);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            padding: 18px 20px;
            display: flex;
            align-items: center;
            border-left: 5px solid transparent;
            transition: box-shadow 0.2s ease-in-out, transform 0.2s ease-in-out;
        }
        .stat-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .stat-icon-img-only {
            width: 38px;
            height: 38px;
            margin-right: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon-img-only img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .stat-info {
            flex-grow: 1;
            line-height: 1.3;
        }

        .stat-info h3 {
            font-size: 1.9rem;
            font-weight: 700;
            margin-bottom: 0px;
        }

        .stat-info p {
            color: #555;
            font-size: 0.85rem;
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .stat-card.card-appointments { border-left-color: var(--color-appointments); }
        .stat-card.card-appointments .stat-info h3 { color: var(--color-appointments); }

        .stat-card.card-patients { border-left-color: var(--color-patients); }
        .stat-card.card-patients .stat-info h3 { color: var(--color-patients); }

        .stat-card.card-prescriptions { border-left-color: var(--color-prescriptions); }
        .stat-card.card-prescriptions .stat-info h3 { color: var(--color-prescriptions); }

        .stat-card.card-messages { border-left-color: var(--color-messages); }
        .stat-card.card-messages .stat-info h3 { color: var(--color-messages); }
        /* --- End NEW Stat Card Styles --- */

        /* General Container & Form Styles */
        .ordonnance-container, .appointments-container, .patients-container, .consultations-container, .dossiers-container, .content-container {
            background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 25px; margin-bottom: 30px;
        }
        .patients-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .section-title { font-size: 1.3rem; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .patients-header .section-title {
            margin-bottom: 0;
            border-bottom: none;
            padding-bottom: 0;
        }
        .section-subtitle {font-size: 1.1rem; margin-bottom: 1rem; font-weight: 500;}
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .form-control.is-invalid { border-color: var(--danger); }
        .text-danger { color: var(--danger); font-size: 0.875em; }
        .text-sm { font-size: 0.875em; }
        textarea.form-control { min-height: 100px; resize: vertical; }
        .form-group.full-width { grid-column: span 2; }
        .btn { padding: 10px 15px; background-color: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; transition: background-color 0.3s; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .btn:hover { background-color: var(--primary-dark); }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn-success { background-color: var(--secondary); }
        .btn-success:hover { background-color: var(--secondary-dark); }
        .btn-warning { background-color: var(--warning); color: var(--text-dark); }
        .btn-warning:hover { background-color: var(--warning-dark); }
        .btn-info { background-color: var(--info); }
        .btn-info:hover { background-color: var(--info-dark); }
        .btn-danger { background-color: var(--danger); }
        .btn-danger:hover { background-color: var(--danger-dark); }
        .btn-sm { padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; }
        .btn-outline-primary { color: var(--primary); border: 1px solid var(--primary); background-color: transparent;}
        .btn-outline-primary:hover { color: #fff; background-color: var(--primary); border-color: var(--primary); }

        /* Button with image icon styles */
        .btn-icon { padding: 0; width: 32px; height: 32px; border-radius: 5px; line-height: 0; overflow: hidden; }
        .button-img-icon { width: 28px; height: 28px; object-fit: contain; vertical-align: middle; }
        .button-img-icon-with-text { width: 18px; height: 18px; margin-right: 7px; object-fit: contain; vertical-align: middle; }
        .btn-success-img, .btn-danger-img { background-color: transparent !important; border: none !important; box-shadow: none !important; }
        .btn-success-img:hover, .btn-danger-img:hover { background-color: rgba(0,0,0,0.05) !important; }

        .mt-2 { margin-top: .5rem !important; } .mt-3 { margin-top: 1rem !important; } .mt-4 { margin-top: 1.5rem !important; }
        .mb-3 { margin-bottom: 1rem !important; }
        .my-4 { margin-top: 1.5rem !important; margin-bottom: 1.5rem !important; }
        .my-5 { margin-top: 3rem !important; margin-bottom: 3rem !important; }
        .me-2 { margin-right: .5rem !important; } .ml-2 { margin-left: .5rem !important; } /* Note: ms- and me- classes from Bootstrap 5 might be preferred if using it */
        .d-flex { display: flex !important; }
        .justify-content-between { justify-content: space-between !important; }
        .justify-content-end { justify-content: flex-end !important; }
        .justify-content-center { justify-content: center !important; }
        .align-items-center { align-items: center !important; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px; }
        .medication-list { margin-top: 20px; }
        .medication-item { display: flex; background-color: var(--bg-light); border-radius: 5px; padding: 10px 15px; margin-bottom: 10px; justify-content: space-between; align-items: center; }
        .medication-info span { font-weight: bold; margin-right: 10px; }
        .remove-med { color: var(--danger); cursor: pointer; }
        .row { display: flex; flex-wrap: wrap; margin-right: -10px; margin-left: -10px; } /* Standard row for grid */
        .col-md-6, .col-md-4 { padding-right: 10px; padding-left: 10px; margin-bottom: 10px; } /* Standard col for grid */
        .col-md-6 { flex: 0 0 50%; max-width: 50%; }
        .col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; }
        .bg-light { background-color: var(--bg-light) !important; }
        .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
        .p-3 { padding: 1rem !important; }
        .rounded { border-radius: .25rem !important; }
        .border { border: 1px solid #dee2e6 !important; }

        /* Div Table Styles */
        .div-table { display: table; width: 100%; border-collapse: collapse; margin-top: 20px; table-layout: fixed; }
        .div-table-header, .div-table-row { display: table-row; border-bottom: 1px solid #eee; }
        .div-table-header { font-weight: bold; background-color: var(--bg-light); }
        .div-table-row:hover { background-color: #f9f9f9; }
        .div-table-cell { display: table-cell; padding: 12px 10px; vertical-align: middle; text-align: left; word-break: break-word; }

        /* Appointments Div Table Specifics */
        .appointments-list .div-table-cell.appointment-time,
        .appointments-list .div-table-cell.appointment-time-header { width: 18%; white-space: nowrap; }
        .appointments-list .div-table-cell.appointment-patient,
        .appointments-list .div-table-cell.appointment-patient-header { width: 25%; }
        .appointments-list .div-table-cell.appointment-type,
        .appointments-list .div-table-cell.appointment-type-header { width: 22%; color: #666; font-size: 0.9em; }
        .appointments-list .div-table-cell.appointment-status-cell,
        .appointments-list .div-table-cell.appointment-status-header { width: 15%; text-align: center; }
        .appointments-list .div-table-cell.appointment-actions,
        .appointments-list .div-table-cell.appointment-actions-header { width: 20%; text-align: right; white-space: nowrap; }

        .appointments-list .div-table-cell.appointment-patient,
        .appointments-list .div-table-cell.appointment-type { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .appointment-status { display: inline-block; padding: 5px 10px; border-radius: 20px; font-size: 0.8rem; font-weight: 500; color: white; white-space: nowrap; }
        .status-scheduled { background-color: var(--primary-light); }
        .status-completed { background-color: var(--secondary); }
        .status-cancelled { background-color: var(--danger); }
        .status-default { background-color: #6c757d; }
        .appointments-list .div-table-cell.appointment-actions > * { margin-left: 5px; }
        .appointments-list .div-table-cell.appointment-actions > *:first-child { margin-left: 0; }
        .appointments-list .div-table-row .div-table-cell[style*="grid-column"],
        .appointments-list .div-table-row .div-table-cell[style*="column-span"] {
            grid-column: 1 / -1;
            -ms-grid-column: 1;
            -ms-grid-column-span: 5; /* Adjust '5' to actual number of columns */
            text-align: center;
            padding: 20px;
        }

        /* Consultations div table specifics */
        .consultations-list .div-table-cell.consultation-date { width: 25%; }
        .consultations-list .div-table-cell.consultation-patient { width: 35%; }
        .consultations-list .div-table-cell.consultation-reason { width: 25%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;}
        .consultations-list .div-table-cell.consultation-actions { width: 15%; text-align: right; white-space: nowrap; }
        .consultations-list .div-table-cell.consultation-actions > * { margin-left: 5px; }
        .consultations-list .div-table-cell.consultation-actions > *:first-child { margin-left: 0; }

        /* Prescriptions (Ordonnances) div table specifics */
        .prescriptions-list .div-table-header > .div-table-cell:nth-child(1) { width: 15%; }
        .prescriptions-list .div-table-header > .div-table-cell:nth-child(2) { width: 25%; }
        .prescriptions-list .div-table-header > .div-table-cell:nth-child(3) { width: 15%; text-align: center; }
        .prescriptions-list .div-table-header > .div-table-cell:nth-child(4) {
            width: 45%;
            text-align: center;
        }

        .prescriptions-list .div-table-row > .div-table-cell:nth-child(1) { width: 15%; }
        .prescriptions-list .div-table-row > .div-table-cell:nth-child(2) { width: 25%; }
        .prescriptions-list .div-table-row > .div-table-cell:nth-child(3) { width: 15%; text-align: center; padding: 12px 0px; }

        .prescriptions-list .div-table-row > .div-table-cell.prescription-actions {
            width: 45%;                /* This cell has 45% of the row's width */
            display: flex;  /* Make it a flex container */
            flex-direction: row;       /* Default, but explicit */
            flex-wrap: nowrap;         /* Prevent buttons from wrapping */
            justify-content: center;   /* Center the flex items (buttons) *within this cell* */
            align-items: center;       /* Vertically align them */
            gap: 5px;
            text-align: center;
        }

        /* The following styles for buttons and form should remain the same */
        .prescriptions-list .div-table-cell.prescription-actions .btn,
        .prescriptions-list .div-table-cell.prescription-actions form {
            white-space: nowrap;
            flex-shrink: 0;
            margin: 0;
        }

        .prescriptions-list .div-table-cell.prescription-actions form {
            display: inline-flex;
            align-items: center;
        }

        .prescriptions-list .div-table-row .div-table-cell[data-empty-row="true"] {
            text-align: center;
            padding: 20px;
        }

        /* Patient Cards Styles */
        .patient-cards-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }
        .patient-card { background-color: var(--bg-white); border-radius: 8px; box-shadow: var(--shadow); display: flex; flex-direction: column; overflow: hidden; }
        .patient-card-header { display: flex; align-items: center; padding: 15px; background-color: var(--bg-light); border-bottom: 1px solid #eee; }
        .patient-avatar-sm { width: 50px; height: 50px; border-radius: 50%; background-color: var(--primary-light); color: var(--text-light); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-right: 15px; text-transform: uppercase; }
        .patient-name { margin: 0; font-size: 1.1rem; font-weight: 600; color: var(--text-dark); }
        .patient-card-body { padding: 15px; font-size: 0.9rem; color: #555; flex-grow: 1; }
        .patient-info-item { margin-bottom: 8px; display: flex; justify-content: space-between; }
        .patient-info-item .info-label { font-weight: 500; color: var(--text-dark); margin-right: 5px; }
        .patient-info-item .info-value { text-align: right; }
        .patient-card-footer { padding: 15px; background-color: var(--bg-light); border-top: 1px solid #eee; text-align: right; }

        /* Messagerie Styles */
        .messagerie-container { display: grid; grid-template-columns: 300px 1fr; gap: 20px; height: calc(100vh - 220px); background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 25px; margin-bottom: 30px; }
        .contacts-list { border-right: 1px solid #eee; overflow-y: auto; padding-right: 10px;}
        .contact-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; }
        .contact-item:last-child { border-bottom: none; }
        .contact-item.active, .contact-item:hover { background-color: var(--bg-light); }
        .contact-name { font-weight: bold; margin-bottom: 5px; }
        .contact-preview { color: #666; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .messages-area { display: flex; flex-direction: column; height: 100%; }
        .messages-header { padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .messages-header h3 { margin:0; font-size: 1.2em; }
        .messages-content { flex: 1; overflow-y: auto; padding: 15px 0; display: flex; flex-direction: column; }
        .message { max-width: 70%; padding: 10px 15px; border-radius: 10px; margin-bottom: 10px; line-height: 1.4; }
        .message-text { word-wrap: break-word; }
        .message-received { background-color: var(--bg-light); align-self: flex-start; }
        .message-sent { background-color: var(--primary-light); color: white; align-self: flex-end; }
        .message-time { font-size: 0.8rem; text-align: right; margin-top: 5px; opacity: 0.8; }
        .message-form { display: flex; border-top: 1px solid #eee; padding-top: 15px; }
        .message-input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; margin-right: 10px; }
        .send-btn { padding: 10px 20px; }

        /* Ordonnance Form (Create/Edit Prescription) Specifics */
        .medication-item-row .row { margin-right: -5px; margin-left: -5px; } /* For tighter grid in medication rows */
        .medication-item-row .col-md-6,
        .medication-item-row .col-md-4 { padding-right: 5px; padding-left: 5px; } /* For tighter grid in medication rows */

        /* Modals (Generic) */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .modal-overlay.active { display: flex; opacity: 1; visibility: visible; }
        .modal { width: 90%; background-color: var(--bg-white); border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); overflow: hidden; transform: translateY(-20px) scale(0.95); transition: transform 0.3s ease, opacity 0.3s ease; opacity: 0; }
        .modal-overlay.active .modal { transform: translateY(0) scale(1); opacity: 1; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #eee; background-color: var(--primary); color: white; }
        .modal-title { font-size: 1.2rem; font-weight: 500; }
        .modal-close { font-size: 1.5rem; cursor: pointer; background: none; border: none; color: white; line-height: 1; padding: 0.25rem 0.5rem; }
        .modal-body { padding: 20px; max-height: 70vh; overflow-y: auto; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 15px 20px; border-top: 1px solid #eee; background-color: var(--bg-light); }
        .modal-form { display: grid; grid-template-columns: 1fr; gap: 15px; }
        @media (min-width: 768px) { .modal-form { grid-template-columns: 1fr 1fr; } }
        .modal-form .form-group.full-width { grid-column: span 2; }

        /* Styles for Appointment Filters & Alerts */
        .form-inline { display: flex; flex-wrap: wrap; align-items: center; margin-bottom: 1rem; }
        .form-inline .form-group { margin-right: 10px; margin-bottom: 10px; }
        .form-inline .form-control-sm { height: calc(1.5em + .5rem + 2px); padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; padding: 1rem 1.25rem; font-size: 1.1rem;}
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; padding: 1rem 1.25rem; font-size: 1.1rem;}

        /* Pagination Centering */
        .pagination {
            justify-content: center;
        }

        /* Recent Activities List */
        .recent-activities-container { /* Placeholder if specific container styles needed */ }
        .recent-activities-list .div-table-cell { font-size: 0.9rem; }
        .recent-activities-list .activity-date-col { width: 20%; }
        .recent-activities-list .activity-type-col { width: 15%; text-align: center;}
        .recent-activities-list .activity-patient-col { width: 25%; }
        .recent-activities-list .activity-desc-col { width: 25%; }
        .recent-activities-list .activity-status-col { width: 15%; text-align: center; }

        .badge.activity-type-consultation { background-color: var(--info); color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.8em;}
        .badge.activity-type-rendez-vous { background-color: var(--primary-light); color: white; padding: 3px 8px; border-radius: 4px; font-size: 0.8em;}
        .badge.activity-type-ordonnance { background-color: var(--warning); color: var(--text-dark); padding: 3px 8px; border-radius: 4px; font-size: 0.8em;}

        .recent-activities-list .status-termine,
        .recent-activities-list .status-completed { background-color: var(--secondary); }
        .recent-activities-list .status-a-venir,
        .recent-activities-list .status-scheduled { background-color: var(--primary-light); }
        .recent-activities-list .status-annule,
        .recent-activities-list .status-cancelled { background-color: var(--danger); }
        .recent-activities-list .status-delivree { background-color: #6f42c1; color:white; }
    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="<?php echo e(route('dashboard')); ?>" class="logo">Medi<span>Consult</span></a>
            </div>
            <?php if(auth()->guard()->check()): ?>
            <div class="user-info">
                <div class="user-avatar"><?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?></div>
                <div class="user-name"><?php echo e(Auth::user()->name); ?></div>
                <?php if(Auth::user()->role): ?>
                    <div class="user-role"><?php echo e(ucfirst(Auth::user()->role->name)); ?></div>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            <ul class="sidebar-menu">
                <li>
                    <a href="#" class="menu-link active" data-section="dashboard">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/tableau_de_bord.png')); ?>" alt="Dashboard Icon">
                        </div>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="appointments">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/rendez_vous.png')); ?>" alt="Appointments Icon">
                        </div>
                        <span>Rendez-vous</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patients">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/patients.png')); ?>" alt="Patients Icon">
                        </div>
                        <span>Patients</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="consultations">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/consultations.png')); ?>" alt="Consultations Icon">
                        </div>
                        <span>Consultations</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="ordonnances">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/ordonnances.png')); ?>" alt="Ordonnances Icon">
                        </div>
                        <span>Ordonnances</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="messagerie">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/messages.png')); ?>" alt="Messagerie Icon">
                        </div>
                        <span>Messagerie</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="parametres">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/profile.png')); ?>" alt="Profile Icon">
                        </div>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" id="logout-form-doctor-dashboard" style="display: none;"><?php echo csrf_field(); ?></form>
                    <a href="<?php echo e(route('logout')); ?>" class="menu-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form-doctor-dashboard').submit();">
                        <div class="menu-icon">
                            <img src="<?php echo e(asset('assets/sidebar/logout.png')); ?>" alt="Logout Icon">
                        </div>
                        <span>Déconnexion</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <h1 class="page-title" id="dynamicPageTitle">Tableau de bord</h1>
                <?php if(auth()->guard()->check()): ?>
                <div class="topbar-actions">
                    <div class="user-profile">
                        <div class="user-profile-img"><?php echo e(strtoupper(substr(Auth::user()->name, 0, 2))); ?></div>
                        <span><?php echo e(Str::before(Auth::user()->name, ' ')); ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div class="content-wrapper">
                
                <?php if(session('success')): ?>
                    <div class="alert alert-success" role="alert"><?php echo e(session('success')); ?></div>
                <?php endif; ?>
                <?php if(session('error')): ?>
                    <div class="alert alert-danger" role="alert"><?php echo e(session('error')); ?></div>
                <?php endif; ?>

                
                

                


                <?php echo $__env->make('doctor.dashboard', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.appointments', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.patients', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.consultations', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.ordonnances', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.messagerie', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php echo $__env->make('doctor.parametres', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </main>
    </div>

    <!-- Modal pour ajouter un nouveau patient -->
    <div class="modal-overlay <?php echo e($errors->hasBag('addPatientModal') || session('open_modal_on_load') === 'add-patient-modal' ? 'active' : ''); ?>" id="add-patient-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Ajouter un nouveau patient</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                <?php if($errors->hasBag('addPatientModal') && $errors->getBag('addPatientModal')->any()): ?>
                    <div class="alert alert-danger">
                        <ul><?php $__currentLoopData = $errors->getBag('addPatientModal')->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                    </div>
                <?php endif; ?>
                <form class="modal-form" id="form-add-new-patient-details-modal" action="<?php echo e(route('doctor.patients.store_from_modal')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="form-group"><label for="modal_new_patient_name_field">Nom Complet</label><input type="text" class="form-control <?php $__errorArgs = ['name', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="modal_new_patient_name_field" name="name" value="<?php echo e(old('name')); ?>" required> <?php $__errorArgs = ['name', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    <div class="form-group"><label for="modal_new_patient_email_field">Email</label><input type="email" class="form-control <?php $__errorArgs = ['email', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="modal_new_patient_email_field" name="email" value="<?php echo e(old('email')); ?>" required> <?php $__errorArgs = ['email', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    <div class="form-group"><label for="modal_new_patient_password_field">Mot de passe</label><input type="password" class="form-control <?php $__errorArgs = ['password', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="modal_new_patient_password_field" name="password" required> <?php $__errorArgs = ['password', 'addPatientModal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    <div class="form-group"><label for="modal_new_patient_password_confirmation_field">Confirmer Mot de passe</label><input type="password" class="form-control" id="modal_new_patient_password_confirmation_field" name="password_confirmation" required></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" form="form-add-new-patient-details-modal" class="btn">Enregistrer</button>
            </div>
        </div>
    </div>

    <!-- View Patient Dossier Modal -->
    <div class="modal-overlay" id="viewPatientDossierModal">
        <div class="modal modal-content" style="max-width: 900px;"> 
            <div class="modal-header">
                <h5 class="modal-title">Dossier Patient: <span id="dossier_patient_name"></span></h5>
                <button type="button" class="modal-close" data-modal-dismiss="viewPatientDossierModal">×</button>
            </div>
            <div class="modal-body" id="viewPatientDossierModalBody">
                <p class="text-center">Chargement du dossier...</p>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="viewPatientDossierModal">Fermer</button>
            </div>
        </div>
    </div>

    <!-- Modal pour que le DOCTEUR crée un nouveau rendez-vous -->
    <div class="modal-overlay <?php echo e(($errors->any() && !$errors->hasBag('addPatientModal')) || session('open_modal_on_load') === 'doctor-create-appointment-modal' ? 'active' : ''); ?>" id="doctor-create-appointment-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Créer un Nouveau Rendez-vous</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            <div class="modal-body">
                 <?php if($errors->any() && !$errors->hasBag('addPatientModal')): ?> 
                    <div class="alert alert-danger">
                        <ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                    </div>
                <?php endif; ?>
                <form id="form-doctor-create-appointment-modal" action="<?php echo e(route('doctor.appointments.store')); ?>" method="POST" class="modal-form">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label for="modal_doc_create_patient_select">Patient</label>
                        <select id="modal_doc_create_patient_select" name="patient_id" class="form-control <?php $__errorArgs = ['patient_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Sélectionner un patient</option>
                            <?php $__currentLoopData = $patientsForModal ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $patient_user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($patient_user->id); ?>" <?php echo e(old('patient_id') == $patient_user->id ? 'selected' : ''); ?>><?php echo e($patient_user->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['patient_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-modal-target="add-patient-modal" style="font-size: 0.8em; padding: 0.25rem 0.5rem;">
                            + Ajouter un nouveau patient
                        </button>
                    </div>

                    <?php if(Auth::check() && Auth::user()->role->name !== 'doctor'): ?>
                        <div class="form-group">
                            <label for="modal_doc_assign_doctor_select">Assigner au Docteur</label>
                            <select id="modal_doc_assign_doctor_select" name="doctor_id" class="form-control <?php $__errorArgs = ['doctor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Sélectionner un docteur</option>
                                <?php $__currentLoopData = $doctorsForModal ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doc_user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($doc_user->id); ?>" <?php echo e(old('doctor_id') == $doc_user->id ? 'selected' : ''); ?>><?php echo e($doc_user->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                             <?php $__errorArgs = ['doctor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    <?php else: ?>
                        <input type="hidden" name="doctor_id" value="<?php echo e(Auth::id()); ?>">
                         <div class="form-group">
                            <label>Docteur</label>
                            <input type="text" class="form-control" value="<?php echo e(Auth::user()->name); ?>" readonly>
                        </div>
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="modal_doc_create_date_input">Date</label>
                        <input type="date" id="modal_doc_create_date_input" name="appointment_date" class="form-control <?php $__errorArgs = ['appointment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('appointment_date', date('Y-m-d'))); ?>" min="<?php echo e(date('Y-m-d')); ?>" required>
                        <?php $__errorArgs = ['appointment_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group full-width">
                        <label for="modal_doc_create_time_select">Heure Disponible</label>
                        <select id="modal_doc_create_time_select" name="appointment_time" class="form-control <?php $__errorArgs = ['appointment_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">Sélectionnez d'abord un médecin et une date</option>
                            <?php if(old('appointment_time')): ?>
                                <option value="<?php echo e(old('appointment_time')); ?>" selected><?php echo e(old('appointment_time')); ?> (Précédemment)</option>
                            <?php endif; ?>
                        </select>
                        <?php $__errorArgs = ['appointment_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <div id="modal_doc_slots_loading" style="display: none; margin-top: 5px;">Chargement...</div>
                        <div id="modal_doc_slots_error" style="display: none; color: red; margin-top: 5px;"></div>
                    </div>

                    <div class="form-group full-width">
                        <label for="modal_doc_create_notes_textarea">Notes (optionnel)</label>
                        <textarea id="modal_doc_create_notes_textarea" name="reason" class="form-control <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('reason')); ?></textarea>
                        <?php $__errorArgs = ['reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger text-sm"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                <button type="submit" form="form-doctor-create-appointment-modal" class="btn">Créer Rendez-vous</button>
            </div>
        </div>
    </div>

    <!-- Create Consultation Modal (Simplified - No Appointment Linking) -->
    <div class="modal-overlay <?php echo e($errors->hasBag('consultationCreate') && session('open_modal_on_load') === 'createConsultationModal' ? 'active' : ''); ?>" id="createConsultationModal">
        <div class="modal modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Consultation</h5>
                <button type="button" class="modal-close" data-modal-dismiss="createConsultationModal">×</button>
            </div>
            <form method="POST" action="<?php echo e(route('doctor.consultations.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                     <?php if($errors->hasBag('consultationCreate')): ?>
                        <div class="alert alert-danger"><ul><?php $__currentLoopData = $errors->consultationCreate->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
                    <?php endif; ?>
                    <div class="modal-form">
                        <div class="form-group"><label for="modal_create_consult_patient_id">Patient *</label><select name="patient_id" id="modal_create_consult_patient_id" class="form-control <?php $__errorArgs = ['patient_id', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required><option value="">Sélectionner Patient</option><?php $__currentLoopData = $patientsForModal ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($p->id); ?>" <?php echo e(old('patient_id') == $p->id ? 'selected' : ''); ?>><?php echo e($p->name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></select><?php $__errorArgs = ['patient_id', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                        <div class="form-group"><label for="modal_create_consult_consultation_date_time">Date et Heure *</label><input type="datetime-local" name="consultation_date_time" id="modal_create_consult_consultation_date_time" class="form-control <?php $__errorArgs = ['consultation_date_time', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('consultation_date_time', now()->format('Y-m-d\TH:i'))); ?>" required><?php $__errorArgs = ['consultation_date_time', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                        <div class="form-group full-width"><label for="modal_create_consult_reason_for_visit">Motif *</label><input type="text" name="reason_for_visit" id="modal_create_consult_reason_for_visit" class="form-control <?php $__errorArgs = ['reason_for_visit', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('reason_for_visit')); ?>" required><?php $__errorArgs = ['reason_for_visit', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                        <div class="form-group full-width"><label for="modal_create_consult_symptoms">Symptômes *</label><textarea name="symptoms" id="modal_create_consult_symptoms" class="form-control <?php $__errorArgs = ['symptoms', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('symptoms')); ?></textarea><?php $__errorArgs = ['symptoms', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                        <div class="form-group full-width"><label for="modal_create_consult_notes">Notes Docteur</label><textarea name="notes" id="modal_create_consult_notes" class="form-control <?php $__errorArgs = ['notes', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('notes')); ?></textarea><?php $__errorArgs = ['notes', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                        <div class="form-group full-width"><label for="modal_create_consult_diagnosis">Diagnostic</label><textarea name="diagnosis" id="modal_create_consult_diagnosis" class="form-control <?php $__errorArgs = ['diagnosis', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" rows="3"><?php echo e(old('diagnosis')); ?></textarea><?php $__errorArgs = ['diagnosis', 'consultationCreate'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><span class="text-danger text-sm"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
            </form>
        </div>
    </div>

    <!-- Edit Consultation Modal (Simplified) -->
    <div class="modal-overlay <?php echo e(session('open_modal_on_load') === 'editConsultationModal' && $errors->hasBag('consultationEdit_' . session('consultation_id_for_error_bag')) ? 'active' : ''); ?>" id="editConsultationModal">
        <div class="modal modal-content">
            <div class="modal-header"><h5 class="modal-title">Modifier Consultation</h5><button type="button" class="modal-close" data-modal-dismiss="editConsultationModal">×</button></div>
            <form method="POST" action="" id="editConsultationForm"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <?php if(session('consultation_id_for_error_bag') && $errors->hasBag('consultationEdit_' . session('consultation_id_for_error_bag'))): ?><div class="alert alert-danger"><ul><?php $__currentLoopData = $errors->getBag('consultationEdit_' . session('consultation_id_for_error_bag'))->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>
                    <div id="editConsultationErrorsGeneral" class="alert alert-danger" style="display:none;"><ul></ul></div>
                    <input type="hidden" name="consultation_id_for_error_bag_identifier" id="edit_consultation_id_for_error_bag">
                    <div class="modal-form">
                        <div class="form-group"><label>Patient</label><input type="text" class="form-control" id="edit_consult_patient_name_display" readonly></div>
                        <div class="form-group"><label for="edit_consult_consultation_date_time">Date et Heure *</label><input type="datetime-local" name="consultation_date_time" id="edit_consult_consultation_date_time" class="form-control" value="<?php echo e(old('consultation_date_time')); ?>" required></div>
                        <div class="form-group full-width"><label for="edit_consult_reason_for_visit">Motif *</label><textarea name="reason_for_visit" id="edit_consult_reason_for_visit" class="form-control" rows="2" required><?php echo e(old('reason_for_visit')); ?></textarea></div>
                        <div class="form-group full-width"><label for="edit_consult_symptoms">Symptômes</label><textarea name="symptoms" id="edit_consult_symptoms" class="form-control" rows="3"><?php echo e(old('symptoms')); ?></textarea></div>
                        <div class="form-group full-width"><label for="edit_consult_notes">Notes</label><textarea name="notes" id="edit_consult_notes" class="form-control" rows="3"><?php echo e(old('notes')); ?></textarea></div>
                        <div class="form-group full-width"><label for="edit_consult_diagnosis">Diagnostic</label><textarea name="diagnosis" id="edit_consult_diagnosis" class="form-control" rows="3"><?php echo e(old('diagnosis')); ?></textarea></div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="editConsultationModal">Annuler</button><button type="submit" class="btn btn-primary">Mettre à Jour</button></div>
            </form>
        </div>
    </div>

    <!-- View Consultation Detail Modal (Simplified) -->
    <div class="modal-overlay" id="viewConsultationDetailModal">
        <div class="modal modal-content">
            <div class="modal-header"><h5 class="modal-title">Détails Consultation</h5><button type="button" class="modal-close" data-modal-dismiss="viewConsultationDetailModal">×</button></div>
            <div class="modal-body">
                <p><strong>Patient:</strong> <span id="view_consult_patient_name"></span></p>
                <p><strong>Date:</strong> <span id="view_consult_date"></span></p>
                <p><strong>RDV Associé:</strong> <span id="view_consult_appointment_info"></span></p> 
                <hr>
                <p><strong>Motif:</strong></p><p id="view_consult_reason" style="white-space:pre-wrap;"></p>
                <p><strong>Symptômes:</strong></p><p id="view_consult_symptoms" style="white-space:pre-wrap;"></p>
                <p><strong>Notes Docteur:</strong></p><p id="view_consult_notes" style="white-space:pre-wrap;"></p>
                <p><strong>Diagnostic:</strong></p><p id="view_consult_diagnosis" style="white-space:pre-wrap;"></p>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="viewConsultationDetailModal">Fermer</button></div>
        </div>
    </div>

    <!-- View Prescription Modal -->
    <div class="modal-overlay" id="viewPrescriptionModal">
        <div class="modal modal-content" style="max-width: 700px;">
            <div class="modal-header"><h5 class="modal-title">Détails Ordonnance</h5><button type="button" class="modal-close" data-modal-dismiss="viewPrescriptionModal">×</button></div>
            <div class="modal-body" id="viewPrescriptionModalBody" style="font-size: 0.9rem;">
                
                <p>Chargement...</p>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="viewPrescriptionModal">Fermer</button></div>
        </div>
    </div>

    <!-- Edit Prescription Modal -->
    <div class="modal-overlay <?php echo e((session('editing_prescription') || (session('prescription_id_for_error_bag') && session('open_modal_on_load') === 'editPrescriptionModal')) ? 'active' : ''); ?>" id="editPrescriptionModal">
        <div class="modal modal-content" style="max-width: 900px;">
            <div class="modal-header"><h5 class="modal-title">Modifier Ordonnance</h5><button type="button" class="modal-close" data-modal-dismiss="editPrescriptionModal">×</button></div>
            <form id="form-edit-prescription" method="POST" action=""><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="modal-body">
                    <?php $editPrescriptionErrorBag = 'prescriptionEdit_' . session('prescription_id_for_error_bag'); ?>
                    <?php if(session('prescription_id_for_error_bag') && $errors->hasBag($editPrescriptionErrorBag)): ?>
                        <div class="alert alert-danger"><strong>Erreurs:</strong><ul><?php $__currentLoopData = $errors->getBag($editPrescriptionErrorBag)->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
                    <?php endif; ?>
                    <div class="modal-form">
                        <div class="form-group"><label for="edit_prescription_patient_id">Patient *</label>
                            <select class="form-control" id="edit_prescription_patient_id" name="patient_id" required>
                                <option value="">Sélectionner Patient</option>
                                <?php $__currentLoopData = $patientsForModal ?? (session('patientsForModal') ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($p->id); ?>" <?php echo e((old('patient_id', session('editing_prescription.patient_id') ?? '') == $p->id) ? 'selected' : ''); ?>><?php echo e($p->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="form-group"><label for="edit_prescription_date">Date *</label><input type="date" class="form-control" id="edit_prescription_date" name="prescription_date" value="<?php echo e(old('prescription_date', substr(session('editing_prescription.prescription_date') ?? date('Y-m-d'),0,10))); ?>" required></div>
                        <div class="form-group full-width"><label for="edit_prescription_consultation_id">Consultation Liée</label>
                            <select class="form-control" id="edit_prescription_consultation_id" name="consultation_id"><option value="">-- Aucune --</option></select>
                            <small id="edit_prescription_consultation_loading" style="display:none;">Chargement...</small>
                        </div>
                        <div class="form-group full-width"><label for="edit_prescription_general_notes">Notes Générales</label><textarea class="form-control" id="edit_prescription_general_notes" name="general_notes" rows="2"><?php echo e(old('general_notes', session('editing_prescription.general_notes') ?? '')); ?></textarea></div>
                    </div><hr class="my-3">
                    <h6 class="mb-2">Médicaments</h6>
                    <div id="edit-medication-fields-container">
                        <?php
                            $medsToDisplay = old('medications', session('editing_prescription.items') ?? []);
                        ?>
                        <?php if(!empty($medsToDisplay)): ?>
                            <?php $__currentLoopData = $medsToDisplay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $med): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
                                <input type="hidden" name="medications[<?php echo e($key); ?>][id]" value="<?php echo e($med['id'] ?? ''); ?>">
                                <div class="row gx-2">
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_name_<?php echo e($key); ?>">Nom *</label><input type="text" id="edit_med_name_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][name]" class="form-control form-control-sm" value="<?php echo e($med['medication_name'] ?? ($med['name'] ?? '')); ?>" required></div>
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_dosage_<?php echo e($key); ?>">Dosage</label><input type="text" id="edit_med_dosage_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][dosage]" class="form-control form-control-sm" value="<?php echo e($med['dosage'] ?? ''); ?>"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_freq_<?php echo e($key); ?>">Fréquence</label><input type="text" id="edit_med_freq_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][frequency]" class="form-control form-control-sm" value="<?php echo e($med['frequency'] ?? ''); ?>"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_duration_<?php echo e($key); ?>">Durée</label><input type="text" id="edit_med_duration_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][duration]" class="form-control form-control-sm" value="<?php echo e($med['duration'] ?? ''); ?>"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_notes_<?php echo e($key); ?>">Notes</label><input type="text" id="edit_med_notes_<?php echo e($key); ?>" name="medications[<?php echo e($key); ?>][notes]" class="form-control form-control-sm" value="<?php echo e($med['notes'] ?? ''); ?>"></div>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-medication-row-btn mt-1">Retirer</button>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php elseif(empty(old('medications')) && !session('editing_prescription.items')): ?>
                             <div class="medication-item-row border p-3 mb-3 rounded bg-light shadow-sm">
                                <input type="hidden" name="medications[0][id]" value="">
                                <div class="row gx-2">
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_name_0">Nom *</label><input type="text" id="edit_med_name_0" name="medications[0][name]" class="form-control form-control-sm" required></div>
                                    <div class="col-md-6 form-group mb-2"><label for="edit_med_dosage_0">Dosage</label><input type="text" id="edit_med_dosage_0" name="medications[0][dosage]" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_freq_0">Fréquence</label><input type="text" id="edit_med_freq_0" name="medications[0][frequency]" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_duration_0">Durée</label><input type="text" id="edit_med_duration_0" name="medications[0][duration]" class="form-control form-control-sm"></div>
                                    <div class="col-md-4 form-group mb-2"><label for="edit_med_notes_0">Notes</label><input type="text" id="edit_med_notes_0" name="medications[0][notes]" class="form-control form-control-sm"></div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm mb-3" id="add-edit-medication-row-btn">+ Ajouter Médicament</button>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary modal-close-btn" data-modal-dismiss="editPrescriptionModal">Annuler</button><button type="submit" class="btn btn-primary">Mettre à Jour</button></div>
            </form>
        </div>
    </div>


    <script>
    // PASTE THE COMPLETE JAVASCRIPT FROM THE PREVIOUS ANSWER HERE
    // It's a large block, so I won't repeat it, but it should be the one
    // that includes all functionalities (SPA, modals, ordonnance JS, consultation JS, etc.)
    // from the answer where I provided the "Complete JavaScript for doctor_dashboard.blade.php"
    document.addEventListener('DOMContentLoaded', function() {
        // --- Helper function to decode HTML entities ---
        function decodeHtmlEntities(str) {
            if (typeof str !== 'string') {
                console.warn('decodeHtmlEntities received non-string input:', str);
                return str; // Or throw an error, or return empty string
            }
            const textArea = document.createElement('textarea');
            textArea.innerHTML = str;
            return textArea.value;
        }

        // --- SPA Navigation Logic ---
        const menuLinks = document.querySelectorAll('.sidebar-menu .menu-link');
        const contentSections = document.querySelectorAll('.content-wrapper .content-section');
        const pageTitleElement = document.getElementById('dynamicPageTitle');

        function activateSection(sectionId, fromLocalStorage = false) {
            let sectionFound = false;
            contentSections.forEach(section => {
                if (section.id === sectionId) {
                    section.classList.add('active');
                    sectionFound = true;
                } else {
                    section.classList.remove('active');
                }
            });

            if (sectionFound) {
                 if (!fromLocalStorage || !localStorage.getItem('activeDoctorSection')) {
                    localStorage.setItem('activeDoctorSection', sectionId);
                 }
            } else if (contentSections.length > 0 && !document.querySelector('.content-section.active')) {
                contentSections[0].classList.add('active');
                sectionId = contentSections[0].id;
                localStorage.setItem('activeDoctorSection', sectionId);
            }
            return sectionId;
        }

        menuLinks.forEach(link => {
            const isLogoutLink = link.getAttribute('href') === "<?php echo e(route('logout')); ?>" || (link.onclick && link.onclick.toString().includes('logout-form'));
            if (isLogoutLink) return;

            link.addEventListener('click', function(e) {
                e.preventDefault();
                menuLinks.forEach(item => item.classList.remove('active'));
                this.classList.add('active');
                const targetSectionId = this.getAttribute('data-section');
                activateSection(targetSectionId);
                if (pageTitleElement && this.querySelector('span')) {
                    pageTitleElement.textContent = this.querySelector('span').textContent;
                }
            });
        });

        let activeSectionFromPHP = "<?php echo e(session('active_section_on_load')); ?>";
        let savedSection = localStorage.getItem('activeDoctorSection');
        let initialSectionId = 'dashboard';

        if (activeSectionFromPHP) {
            initialSectionId = activeSectionFromPHP;
            localStorage.setItem('activeDoctorSection', activeSectionFromPHP); // Overwrite local storage if PHP provides a section
        } else if (savedSection) {
            initialSectionId = savedSection;
        }

        const finalActiveSectionId = activateSection(initialSectionId, true); // 'true' indicates it might be from local storage
        const activeLink = document.querySelector(`.sidebar-menu .menu-link[data-section="${finalActiveSectionId}"]`);
        if (activeLink) {
            menuLinks.forEach(item => item.classList.remove('active'));
            activeLink.classList.add('active');
            if (pageTitleElement && activeLink.querySelector('span')) {
                pageTitleElement.textContent = activeLink.querySelector('span').textContent;
            }
        } else if (menuLinks.length > 0) { // Fallback if no matching link found (e.g., localStorage had an old value)
            menuLinks.forEach(item => item.classList.remove('active'));
            menuLinks[0].classList.add('active'); // Activate the first link
             if (pageTitleElement && menuLinks[0].querySelector('span')) {
                pageTitleElement.textContent = menuLinks[0].querySelector('span').textContent;
            }
            if(contentSections.length > 0) activateSection(menuLinks[0].getAttribute('data-section')); // Activate its section
        }


        // --- Alert Auto-Dismissal ---
        document.querySelectorAll('.alert-success').forEach(successAlert => {
            setTimeout(() => {
                successAlert.style.transition = 'opacity 0.5s ease';
                successAlert.style.opacity = '0';
                setTimeout(() => { successAlert.style.display = 'none'; }, 500);
            }, 7000);
        });
        document.querySelectorAll('.alert-danger').forEach(errorAlert => {
            const parentModal = errorAlert.closest('.modal-overlay.active'); // Only check if inside an ACTIVE modal
            const isGeneralErrorNotSpecificToModal = !errorAlert.closest('.modal-body'); // Check if it's a general page error

            // Don't auto-hide errors that are inside an active modal and intended for user action
            // or if they are general page errors that appeared on load without a specific modal context
            if (!parentModal && !isGeneralErrorNotSpecificToModal && !("<?php echo e(session('open_modal_on_load')); ?>")) {
                 setTimeout(() => {
                    errorAlert.style.transition = 'opacity 0.5s ease';
                    errorAlert.style.opacity = '0';
                    setTimeout(() => { errorAlert.style.display = 'none'; }, 500);
                }, 15000); // Longer for errors
            }
        });


        // --- General Modal Toggle Logic ---
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-modal-target');
                const modal = document.getElementById(modalId);
                if (modal) {
                    document.querySelectorAll('.modal-overlay.active').forEach(activeModal => {
                        if (activeModal.id !== modalId) activeModal.classList.remove('active');
                    });
                    modal.classList.add('active');
                    if (modalId === 'doctor-create-appointment-modal') {
                        const dateInput = document.getElementById('modal_doc_create_date_input');
                        if (dateInput && dateInput.value) {
                            fetchDoctorModalAvailableSlots();
                        }
                    }
                }
            });
        });
        document.querySelectorAll('.modal-close, .modal-close-btn').forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                const modal = button.closest('.modal-overlay');
                if (modal) modal.classList.remove('active');
            });
        });
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('active'); });
        });


        // --- START: JS for Patient Dossier Modal ---
        document.querySelectorAll('.view-patient-dossier-btn').forEach(button => {
            button.addEventListener('click', function() {
                const patientId = this.dataset.patientId;
                const dossierModal = document.getElementById('viewPatientDossierModal');
                const dossierModalBody = document.getElementById('viewPatientDossierModalBody');
                const dossierPatientNameSpan = document.getElementById('dossier_patient_name');

                if (!dossierModal || !dossierModalBody || !dossierPatientNameSpan) {
                    console.error('Dossier modal elements not found!');
                    return;
                }

                dossierPatientNameSpan.textContent = '';
                dossierModalBody.innerHTML = '<p class="text-center py-5">Chargement du dossier patient...</p>';
                dossierModal.classList.add('active');

                const fetchUrl = `<?php echo e(url('doctor/patients')); ?>/${patientId}/dossier`;

                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(patientData => {
                        dossierPatientNameSpan.textContent = patientData.name || 'N/A';
                        let contentHtml = `
                            <h4>Informations Générales :</h4><br>
                            <p><strong>Nom:</strong> ${patientData.name || 'N/A'}</p>
                            <p><strong>Email:</strong> ${patientData.email || 'N/A'}</p>
                            
                            
                            <hr class="my-4">
                        `;

                        // Consultations
                        contentHtml += `<h4>Consultations avec Dr. <?php echo e(Auth::user()->name); ?> (${patientData.patient_consultations ? patientData.patient_consultations.length : 0}) :</h4><br>`;
                        if (patientData.patient_consultations && patientData.patient_consultations.length > 0) {
                            contentHtml += '<div class="list-group list-group-flush">'; // Simple list styling
                            patientData.patient_consultations.forEach(consult => {
                                const consultDate = new Date(consult.consultation_date).toLocaleString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour:'2-digit', minute: '2-digit' });
                                contentHtml += `
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Consultation du ${consultDate}</h6>
                                            
                                        </div>
                                        <p class="mb-1"><strong>Motif:</strong> ${consult.reason_for_visit || 'N/A'}</p>
                                        <p class="mb-1"><strong>Symptômes:</strong> ${consult.symptoms ? consult.symptoms.substring(0,100) : 'N/A'}</p>
                                        <p class="mb-1"><strong>Diagnostic:</strong> ${consult.diagnosis || 'N/A'}</p>
                                        
                                    </div>
                                    <br>
                                `;
                            });
                            contentHtml += '</div>';
                        } else {
                            contentHtml += '<p>Aucune consultation enregistrée avec ce médecin.</p>';
                        }
                        contentHtml += '<hr class="my-4">';

                        // Prescriptions
                        contentHtml += `<h4>Ordonnances par Dr. <?php echo e(Auth::user()->name); ?> (${patientData.received_prescriptions ? patientData.received_prescriptions.length : 0}) :</h4><br>`;
                        if (patientData.received_prescriptions && patientData.received_prescriptions.length > 0) {
                            contentHtml += '<div class="list-group list-group-flush">';
                            patientData.received_prescriptions.forEach(presc => {
                                const prescDate = new Date(presc.prescription_date).toLocaleDateString('fr-FR');
                                let itemsSummary = presc.items.map(item => item.medication_name).slice(0, 2).join(', ');
                                if (presc.items.length > 2) itemsSummary += '...';

                                contentHtml += `
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">Ordonnance du ${prescDate}</h6>&ensp; &ensp; &ensp;
                                            <small>${presc.items_count || presc.items.length} médicament(s)</small>
                                        </div>
                                        <p class="mb-1"><strong>Médicaments:</strong> ${itemsSummary || 'N/A'}</p>
                                        <p class="mb-1"><strong>Notes:</strong> ${presc.general_notes || 'Aucune'}</p>
                                        
                                    </div>
                                    <br>
                                `;
                            });
                            contentHtml += '</div>';
                        } else {
                            contentHtml += '<p>Aucune ordonnance enregistrée par ce médecin.</p>';
                        }

                        dossierModalBody.innerHTML = contentHtml;
                    })
                    .catch(error => {
                        console.error('Error fetching patient dossier:', error);
                        dossierModalBody.innerHTML = '<p class="text-danger text-center py-5">Erreur lors du chargement du dossier patient.</p>';
                    });
            });
        });
        // --- END: JS for Patient Dossier Modal ---


        // --- JS for Doctor Create Appointment Modal Slots ---
        const doctorCreateApptModalEl = document.getElementById('doctor-create-appointment-modal');
        let doctorSelectInModal, doctorHiddenInputInModal, dateInputDocModal, timeSelectDocModal, slotsLoadingDocModal, slotsErrorDocModal;

        if (doctorCreateApptModalEl) {
            doctorSelectInModal = document.getElementById('modal_doc_assign_doctor_select');
            doctorHiddenInputInModal = doctorCreateApptModalEl.querySelector('input[name="doctor_id"]');
            dateInputDocModal = document.getElementById('modal_doc_create_date_input');
            timeSelectDocModal = document.getElementById('modal_doc_create_time_select');
            slotsLoadingDocModal = document.getElementById('modal_doc_slots_loading');
            slotsErrorDocModal = document.getElementById('modal_doc_slots_error');

            function fetchDoctorModalAvailableSlots() {
                const doctorId = (doctorSelectInModal && doctorSelectInModal.offsetParent !== null && doctorSelectInModal.value) ? doctorSelectInModal.value : (doctorHiddenInputInModal ? doctorHiddenInputInModal.value : null);
                const selectedDate = dateInputDocModal.value;
                const previouslySelectedTime = timeSelectDocModal.dataset.oldTime || "<?php echo e(old('appointment_time', '')); ?>";

                timeSelectDocModal.innerHTML = '<option value="">Chargement...</option>';
                timeSelectDocModal.disabled = true;
                slotsErrorDocModal.style.display = 'none';

                if (!doctorId || !selectedDate) {
                    timeSelectDocModal.innerHTML = '<option value="">Sélectionnez un médecin et une date</option>'; return;
                }
                if (new Date(selectedDate) < new Date(new Date().toISOString().split('T')[0])) {
                     timeSelectDocModal.innerHTML = '<option value="">Date invalide</option>';
                     slotsErrorDocModal.textContent = 'La date ne peut pas être dans le passé.';
                     slotsErrorDocModal.style.display = 'block'; return;
                }
                slotsLoadingDocModal.style.display = 'block';

                fetch("<?php echo e(route('appointments.available_slots')); ?>", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json'},
                    body: JSON.stringify({ doctor_id: doctorId, date: selectedDate })
                })
                .then(response => response.ok ? response.json() : response.json().then(err => { console.error("Server error:", err); throw err; }))
                .then(data => {
                    slotsLoadingDocModal.style.display = 'none';
                    timeSelectDocModal.innerHTML = '<option value="">-- Choisissez une heure --</option>';
                    if (data.slots && data.slots.length > 0) {
                        data.slots.forEach(slot => {
                            const option = document.createElement('option'); option.value = slot; option.textContent = slot;
                            if (slot === previouslySelectedTime) option.selected = true;
                            timeSelectDocModal.appendChild(option);
                        });
                        timeSelectDocModal.disabled = false;
                    } else {
                        if (previouslySelectedTime) {
                             const option = document.createElement('option');
                             option.value = previouslySelectedTime; option.textContent = previouslySelectedTime + " (Non disponible)";
                             option.disabled = true; option.selected = true;
                             timeSelectDocModal.appendChild(option);
                        } else {
                            timeSelectDocModal.innerHTML = '<option value="">Aucun créneau</option>';
                        }
                        slotsErrorDocModal.textContent = data.message || 'Aucun créneau disponible.';
                        slotsErrorDocModal.style.display = 'block';
                    }
                })
                .catch(error => {
                    slotsLoadingDocModal.style.display = 'none';
                    timeSelectDocModal.innerHTML = '<option value="">Erreur</option>';
                    console.error('Error fetching slots for doctor modal:', error);
                    let errorMsg = 'Erreur de chargement des créneaux.';
                    if(error && error.errors) { errorMsg = Object.values(error.errors).flat().join(' '); }
                    else if (error && error.message) { errorMsg = error.message; }
                    slotsErrorDocModal.textContent = `Erreur: ${errorMsg}`;
                    slotsErrorDocModal.style.display = 'block';
                });
            }
            if(doctorSelectInModal) doctorSelectInModal.addEventListener('change', fetchDoctorModalAvailableSlots);
            if(dateInputDocModal) dateInputDocModal.addEventListener('change', fetchDoctorModalAvailableSlots);
            if(timeSelectDocModal && "<?php echo e(old('appointment_time')); ?>") timeSelectDocModal.dataset.oldTime = "<?php echo e(old('appointment_time')); ?>";
        }


        // --- Re-open modal if there were validation errors (from session) ---
        const modalToOpenFromSession = "<?php echo e(session('open_modal_on_load')); ?>";
        const consultationIdForError = "<?php echo e(session('consultation_id_for_error_bag')); ?>";
        const prescriptionIdForErrorBag = "<?php echo e(session('prescription_id_for_error_bag')); ?>"; // For prescription edit

        if (modalToOpenFromSession) {
            const modalElement = document.getElementById(modalToOpenFromSession);
            // Blade directive should already add 'active' class, but ensure it is
            if (modalElement && !modalElement.classList.contains('active')) {
                 // modalElement.classList.add('active'); // Let Blade handle initial, this is for JS logic after
            }

            if (modalToOpenFromSession === 'doctor-create-appointment-modal' && dateInputDocModal && dateInputDocModal.value) {
                fetchDoctorModalAvailableSlots();
            }
            if (modalToOpenFromSession === 'editConsultationModal' && consultationIdForError) {
                const editButton = document.querySelector(`.edit-consultation-btn[data-id="${consultationIdForError}"]`);
                if (editButton) { /* ... (Logic to repopulate edit consultation modal if needed, old() usually suffices) ... */ }
            }
            if (modalToOpenFromSession === 'createConsultationModal' && document.getElementById('modal_create_consult_patient_id')?.value) {
                document.getElementById('modal_create_consult_patient_id').dispatchEvent(new Event('change'));
            }
             if (modalToOpenFromSession === 'editPrescriptionModal' && prescriptionIdForErrorBag) {
                // Handled by specific edit prescription modal JS below (session('editing_prescription') check)
            }
            if (modalToOpenFromSession === 'createPrescriptionModal' && document.getElementById('prescription_patient_id')?.value){
                // Trigger change on patient select to re-fetch consultations if create prescription modal reopens
                document.getElementById('prescription_patient_id').dispatchEvent(new Event('change'));
            }
        }


        // --- START: JS for Ordonnances (Prescriptions) Section ---
        const addMedicationBtn = document.getElementById('add-medication-row-btn'); // For create form
        const medicationFieldsContainer = document.getElementById('medication-fields-container'); // For create form
        const medicationRowTemplate = document.getElementById('medication-row-template');
        let createMedicationIndex = document.querySelectorAll('#medication-fields-container .medication-item-row').length;

        if (addMedicationBtn && medicationFieldsContainer && medicationRowTemplate) {
            addMedicationBtn.addEventListener('click', function() {
                const templateContent = medicationRowTemplate.innerHTML.replace(/__INDEX__/g, createMedicationIndex);
                const newRowDiv = document.createElement('div'); // Create a temporary div to parse the template
                newRowDiv.innerHTML = templateContent;
                const newRowElement = newRowDiv.firstElementChild; // Get the actual .medication-item-row

                // Ensure unique IDs for labels and inputs if they use __INDEX__
                newRowElement.querySelectorAll('[id*="__INDEX__"]').forEach(el => {
                    el.id = el.id.replace(/__INDEX__/g, createMedicationIndex);
                });
                newRowElement.querySelectorAll('label[for*="__INDEX__"]').forEach(label => {
                    label.htmlFor = label.htmlFor.replace(/__INDEX__/g, createMedicationIndex);
                });


                medicationFieldsContainer.appendChild(newRowElement);
                createMedicationIndex++;
            });

            medicationFieldsContainer.addEventListener('click', function(e) {
                if (e.target && e.target.classList.contains('remove-medication-row-btn')) {
                    if (medicationFieldsContainer.querySelectorAll('.medication-item-row').length > 1) { // Prevent removing the last one
                        e.target.closest('.medication-item-row').remove();
                    } else {
                        alert("Vous ne pouvez pas retirer le dernier médicament. Ajoutez-en un autre d'abord si vous souhaitez modifier celui-ci.");
                    }
                }
            });
        }
        const cancelCreatePrescriptionBtn = document.getElementById('cancel-create-prescription-btn');
        if (cancelCreatePrescriptionBtn) { /* ... (Cancel logic from previous answer) ... */ }
        // --- END: JS for Ordonnances (Prescriptions) Section ---


        // --- START: JS for Messagerie Section ---
        // ... (Keep your existing messagerie JS as it was) ...


        // --- START: JS for Consultation Modals (Create, Edit, View) ---
        // (Includes appointment linking for consultations - simplified as per "not adding appointment" decision)
        // --- Edit/View Consultation Modal JS (Simplified) ---
        const editConsultationModalEl = document.getElementById('editConsultationModal');
        const editConsultationFormEl = document.getElementById('editConsultationForm');
        const viewConsultationDetailModalEl = document.getElementById('viewConsultationDetailModal');

        document.querySelectorAll('.edit-consultation-btn').forEach(button => {
            button.addEventListener('click', function() { /* ... (Implementation from previous simplified version) ... */ });
        });
        document.querySelectorAll('.view-consultation-details-btn').forEach(button => {
            button.addEventListener('click', function() { /* ... (Implementation with decodeHtmlEntities) ... */ });
        });
        // (Full implementation for these simplified edit/view consultation listeners from previous answer)
        document.querySelectorAll('.edit-consultation-btn').forEach(button => {
            button.addEventListener('click', function() {
                const consultationId = this.dataset.id;
                if(editConsultationFormEl) editConsultationFormEl.action = `<?php echo e(url('doctor/consultations')); ?>/${consultationId}`;

                if(document.getElementById('edit_consult_patient_name_display')) document.getElementById('edit_consult_patient_name_display').value = this.dataset.patientName;
                if(document.getElementById('edit_consult_consultation_date_time')) document.getElementById('edit_consult_consultation_date_time').value = this.dataset.consultationDate;
                if(document.getElementById('edit_consult_reason_for_visit')) document.getElementById('edit_consult_reason_for_visit').value = decodeHtmlEntities(this.dataset.reasonForVisit);
                if(document.getElementById('edit_consult_symptoms')) document.getElementById('edit_consult_symptoms').value = decodeHtmlEntities(this.dataset.symptoms);
                if(document.getElementById('edit_consult_notes')) document.getElementById('edit_consult_notes').value = decodeHtmlEntities(this.dataset.notes);
                if(document.getElementById('edit_consult_diagnosis')) document.getElementById('edit_consult_diagnosis').value = decodeHtmlEntities(this.dataset.diagnosis);
                if(document.getElementById('edit_consultation_id_for_error_bag')) document.getElementById('edit_consultation_id_for_error_bag').value = consultationId;

                const errorDivGeneral = document.getElementById('editConsultationErrorsGeneral');
                if(errorDivGeneral) {
                    errorDivGeneral.style.display = 'none';
                    if(errorDivGeneral.querySelector('ul')) errorDivGeneral.querySelector('ul').innerHTML = '';
                }
                if(editConsultationModalEl) editConsultationModalEl.classList.add('active');
            });
        });
        document.querySelectorAll('.view-consultation-details-btn').forEach(button => {
            button.addEventListener('click', function() {
                let rawDetailsString, decodedDetailsString, details;
                try {
                    rawDetailsString = this.getAttribute('data-consultation-details');
                    decodedDetailsString = decodeHtmlEntities(rawDetailsString);
                    details = JSON.parse(decodedDetailsString);

                    if(document.getElementById('view_consult_patient_name')) document.getElementById('view_consult_patient_name').textContent = details.patient ? details.patient.name : 'N/A';
                    if(document.getElementById('view_consult_date')) document.getElementById('view_consult_date').textContent = details.consultation_date ? new Date(details.consultation_date).toLocaleString('fr-FR') : 'N/A';
                    let appointmentInfo = 'Aucun';
                    if (details.appointment) {
                        appointmentInfo = `RDV du ${new Date(details.appointment.appointment_datetime).toLocaleString('fr-FR')}`;
                    }
                    if(document.getElementById('view_consult_appointment_info')) document.getElementById('view_consult_appointment_info').textContent = appointmentInfo;
                    if(document.getElementById('view_consult_reason')) document.getElementById('view_consult_reason').textContent = details.reason_for_visit || 'N/A';
                    if(document.getElementById('view_consult_symptoms')) document.getElementById('view_consult_symptoms').textContent = details.symptoms || 'N/A';
                    if(document.getElementById('view_consult_notes')) document.getElementById('view_consult_notes').textContent = details.notes || 'N/A';
                    if(document.getElementById('view_consult_diagnosis')) document.getElementById('view_consult_diagnosis').textContent = details.diagnosis || 'N/A';

                    if(viewConsultationDetailModalEl) viewConsultationDetailModalEl.classList.add('active');
                } catch (e) { /* ... (error handling) ... */ }
            });
        });
        // --- END: JS for Consultation Modals ---


        // --- START: JS for Prescription Form - Link to Consultation (Create Prescription) ---
        const prescriptionPatientSelect = document.getElementById('prescription_patient_id');
        const prescriptionConsultationSelect = document.getElementById('prescription_consultation_id');
        const prescriptionConsultationLoading = document.getElementById('prescription_consultation_loading');

        if (prescriptionPatientSelect && prescriptionConsultationSelect && prescriptionConsultationLoading) {
            function fetchAndPopulatePatientConsultations(patientId, targetSelectElement, loadingElement, preSelectedConsultationId = null) {
                targetSelectElement.innerHTML = '<option value="">-- Chargement... --</option>';
                targetSelectElement.disabled = true;
                loadingElement.style.display = 'inline';

                if (!patientId) {
                    loadingElement.style.display = 'none';
                    targetSelectElement.innerHTML = '<option value="">-- Sélectionnez d\'abord un patient --</option>';
                    return;
                }
                const fetchUrl = `<?php echo e(url('doctor/patients')); ?>/${patientId}/consultations-for-linking`;
                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(consultations => {
                        loadingElement.style.display = 'none';
                        targetSelectElement.innerHTML = '<option value="">-- Aucune Consultation --</option>';
                        if (consultations && consultations.length > 0) {
                            consultations.forEach(consult => {
                                const option = document.createElement('option');
                                option.value = consult.id;
                                const consultDate = new Date(consult.consultation_date).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                                option.textContent = `Consultation du ${consultDate} (Motif: ${consult.reason_for_visit ? consult.reason_for_visit.substring(0,40) : 'N/A'})`;
                                if (preSelectedConsultationId && preSelectedConsultationId == consult.id) {
                                    option.selected = true;
                                }
                                targetSelectElement.appendChild(option);
                            });
                        } else {
                            targetSelectElement.innerHTML += '<option value="" disabled>Aucune consultation trouvée</option>';
                        }
                        targetSelectElement.disabled = false;
                    })
                    .catch(error => { /* ... (error handling) ... */ });
            }

            prescriptionPatientSelect.addEventListener('change', function() {
                fetchAndPopulatePatientConsultations(this.value, prescriptionConsultationSelect, prescriptionConsultationLoading, "<?php echo e(old('consultation_id')); ?>");
            });
            if (prescriptionPatientSelect.value && ("<?php echo e(old('patient_id')); ?>" === prescriptionPatientSelect.value || ! "<?php echo e(old('patient_id')); ?>") ) { // Also run if patient already selected but not from old input
                fetchAndPopulatePatientConsultations(prescriptionPatientSelect.value, prescriptionConsultationSelect, prescriptionConsultationLoading, "<?php echo e(old('consultation_id')); ?>");
            }
        }
        // --- END: JS for Prescription Form - Link to Consultation ---

        // --- START: JS for "Edit Prescription Modal" ---
        // 1. Event Listener for all ".edit-prescription-btn" buttons
        document.querySelectorAll('.edit-prescription-btn').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default browser action
                const editUrl = this.dataset.editUrl;
                const prescriptionId = this.dataset.id;
                console.log(`ACTION: Clicked Edit Prescription Button for ID: ${prescriptionId}`);
                console.log('ACTION: Intending to navigate to URL:', editUrl);

                if (editUrl) {
                    window.location.href = editUrl; // This initiates the redirect to PrescriptionController@edit
                } else {
                    console.error('CRITICAL ERROR: data-edit-url is missing on the button!');
                    alert('Erreur: URL de modification manquante. Vérifiez la console.');
                }
            });
        });

        // 2. Logic to Open and Populate Modal on Page Load (after redirect or validation error)
        const editingPrescriptionDataFromSession = <?php echo json_encode(session('editing_prescription'), 15, 512) ?>;
        const prescriptionOpenModalOnLoad = "<?php echo e(session('open_modal_on_load')); ?>";
        const prescriptionIdForErrorFromSession = "<?php echo e(session('prescription_id_for_error_bag')); ?>";

        console.log("EDIT_MODAL_JS (Load): editing_prescription (from session):", editingPrescriptionDataFromSession);
        console.log("EDIT_MODAL_JS (Load): open_modal_on_load (from session):", prescriptionOpenModalOnLoad);
        console.log("EDIT_MODAL_JS (Load): prescription_id_for_error_bag (from session):", prescriptionIdForErrorFromSession);

        const editPrescriptionModalElement = document.getElementById('editPrescriptionModal');
        if(editPrescriptionModalElement) { // Ensure modal exists on page
            console.log("EDIT_MODAL_JS (Load): Found #editPrescriptionModal element.");

            const shouldProcessEditModal =
                (editingPrescriptionDataFromSession && prescriptionOpenModalOnLoad === 'editPrescriptionModal') || // Fresh edit, modal forced open
                (prescriptionOpenModalOnLoad === 'editPrescriptionModal' && prescriptionIdForErrorFromSession);     // Validation error, modal forced open

            console.log("EDIT_MODAL_JS (Load): shouldProcessEditModal condition is:", shouldProcessEditModal);

            if (shouldProcessEditModal) {
                console.log("EDIT_MODAL_JS (Load): CONDITION MET. Processing modal content.");
                const form = editPrescriptionModalElement.querySelector('form#form-edit-prescription');
                console.log("EDIT_MODAL_JS (Load): Found #form-edit-prescription element:", form);

                if (form) {
                    const targetPrescriptionId = (editingPrescriptionDataFromSession && editingPrescriptionDataFromSession.id) ? editingPrescriptionDataFromSession.id : prescriptionIdForErrorFromSession;
                    console.log("EDIT_MODAL_JS (Load): Target Prescription ID for form action:", targetPrescriptionId);
                    if (targetPrescriptionId) {
                        form.action = `<?php echo e(url('doctor/prescriptions')); ?>/${targetPrescriptionId}`; // Set form action for PUT
                        console.log("EDIT_MODAL_JS (Load): Form action set to:", form.action);
                    }

                    const patientSelectField = form.querySelector('#edit_prescription_patient_id');
                    const dateField = form.querySelector('#edit_prescription_date');
                    const generalNotesField = form.querySelector('#edit_prescription_general_notes');
                    const consultationSelectField = form.querySelector('#edit_prescription_consultation_id'); // Corrected to use form.querySelector
                    const consultationLoadingField = form.querySelector('#edit_prescription_consultation_loading'); // Corrected

                    console.log("EDIT_MODAL_JS (Load): Patient Select:", patientSelectField, "Date Field:", dateField, "Notes Field:", generalNotesField, "Consult Select:", consultationSelectField);

                    // Scenario 1: Fresh edit (editing_prescription is set, and it's not a re-display due to error for *this* ID)
                    if (editingPrescriptionDataFromSession && (!prescriptionIdForErrorFromSession || prescriptionIdForErrorFromSession != editingPrescriptionDataFromSession.id)) {
                        console.log('EDIT_MODAL_JS (Load): Populating form from FRESH session data for ID:', editingPrescriptionDataFromSession.id);
                        if (patientSelectField) patientSelectField.value = editingPrescriptionDataFromSession.patient_id;
                        if (dateField) dateField.value = editingPrescriptionDataFromSession.prescription_date ? editingPrescriptionDataFromSession.prescription_date.substring(0,10) : '';
                        if (generalNotesField) generalNotesField.value = editingPrescriptionDataFromSession.general_notes || '';

                        // Medication items for FRESH edit are rendered by Blade using session('editing_prescription.items')

                        const editPatientId = editingPrescriptionDataFromSession.patient_id;
                        if (editPatientId && consultationSelectField && consultationLoadingField) {
                            console.log("EDIT_MODAL_JS (Load - Fresh): Fetching consultations for patient ID:", editPatientId, "Pre-selecting:", editingPrescriptionDataFromSession.consultation_id);
                            fetchAndPopulatePatientConsultations( // Make sure this function is defined and accessible
                                editPatientId,
                                consultationSelectField,
                                consultationLoadingField,
                                editingPrescriptionDataFromSession.consultation_id
                            );
                        }
                    }
                    // Scenario 2: Reopening due to validation error for THIS prescription
                    else if (prescriptionIdForErrorFromSession) {
                        console.log('EDIT_MODAL_JS (Load - Error): Modal reopening due to validation error for ID:', prescriptionIdForErrorFromSession);
                        // Form fields (patient, date, notes) should be repopulated by Blade's old() helper.
                        // Medication items are also handled by Blade's old('medications')
                        // We only need to re-trigger the dynamic consultation dropdown if patient_id was set by old().
                        const oldPatientIdForEdit = patientSelectField ? patientSelectField.value : ("<?php echo e(old('patient_id')); ?>" || (editingPrescriptionDataFromSession ? editingPrescriptionDataFromSession.patient_id : ''));
                        const oldConsultationIdForEdit = "<?php echo e(old('consultation_id')); ?>";
                        console.log("EDIT_MODAL_JS (Load - Error): oldPatientIdForEdit (from form/old()):", oldPatientIdForEdit, "oldConsultationIdForEdit:", oldConsultationIdForEdit);

                        if (oldPatientIdForEdit && consultationSelectField && consultationLoadingField) {
                            fetchAndPopulatePatientConsultations(
                                oldPatientIdForEdit,
                                consultationSelectField,
                                consultationLoadingField,
                                oldConsultationIdForEdit
                            );
                        }
                    }

                    const editMedContainer = form.querySelector('#edit-medication-fields-container');
                    if (editMedContainer) {
                        window.editMedicationGlobalIndex = editMedContainer.querySelectorAll('.medication-item-row').length;
                        console.log("EDIT_MODAL_JS (Load): Initialized editMedicationGlobalIndex to:", window.editMedicationGlobalIndex);
                    } else {
                        window.editMedicationGlobalIndex = 0;
                        console.warn("EDIT_MODAL_JS (Load): #edit-medication-fields-container not found!");
                    }
                } else {
                    console.error("EDIT_MODAL_JS (Load): Form with id 'form-edit-prescription' NOT FOUND inside edit modal!");
                }
                // The modal's 'active' class is set by Blade directive using session('open_modal_on_load')
                // So, no need to addClass('active') here if Blade handles it.
                console.log("EDIT_MODAL_JS (Load): Edit prescription modal processing complete. Modal should be active via Blade.");
            } else {
                console.log("EDIT_MODAL_JS (Load): Condition NOT MET for processing Edit Prescription Modal.");
            }
        } else {
            console.log("EDIT_MODAL_JS (Load): #editPrescriptionModal element NOT found on page.");
        }


        // 3. JS for adding/removing medication rows in an EDIT prescription modal
        const editPrescriptionModalForm = document.getElementById('form-edit-prescription'); // This refers to the form inside the modal
        if (editPrescriptionModalForm) {
            const addEditMedBtn = editPrescriptionModalForm.querySelector('#add-edit-medication-row-btn'); // Button specific to edit modal
            const editMedContainer = editPrescriptionModalForm.querySelector('#edit-medication-fields-container'); // Container specific to edit modal
            // medicationRowTemplate should be globally available from ordonnances.blade.php

            if (addEditMedBtn && editMedContainer && medicationRowTemplate) {
                if(typeof window.editMedicationGlobalIndex === 'undefined') {
                    // Initialize if not set by the block above (e.g., if modal opened by direct JS call not session)
                    window.editMedicationGlobalIndex = editMedContainer.querySelectorAll('.medication-item-row').length;
                }
                addEditMedBtn.addEventListener('click', function() {
                    console.log("EDIT_MODAL_JS: Add medication row clicked. Current index:", window.editMedicationGlobalIndex);
                    const templateContent = medicationRowTemplate.innerHTML.replace(/__INDEX__/g, window.editMedicationGlobalIndex);
                    const newRowDiv = document.createElement('div');
                    newRowDiv.innerHTML = templateContent;
                    const newRowElement = newRowDiv.firstElementChild;

                    // Ensure unique IDs for labels and inputs in new rows for the edit form
                    newRowElement.querySelectorAll('[id*="__INDEX__"]').forEach(el => {
                        el.id = el.id.replace(/__INDEX__/g, `edit_med_${window.editMedicationGlobalIndex}`); // Prefix to avoid clashes if create form IDs are similar
                    });
                    newRowElement.querySelectorAll('label[for*="__INDEX__"]').forEach(label => {
                        label.htmlFor = label.htmlFor.replace(/__INDEX__/g, `edit_med_${window.editMedicationGlobalIndex}`);
                    });

                    editMedContainer.appendChild(newRowElement);
                    window.editMedicationGlobalIndex++;
                });

                editMedContainer.addEventListener('click', function(e) {
                    if (e.target && e.target.classList.contains('remove-medication-row-btn')) {
                        if (editMedContainer.querySelectorAll('.medication-item-row').length > 1) {
                            e.target.closest('.medication-item-row').remove();
                            console.log("EDIT_MODAL_JS: Medication row removed.");
                            // Note: Re-indexing is complex and usually not necessary if backend handles array keys flexibly.
                        } else {
                            alert("Vous ne pouvez pas retirer le dernier médicament de l'ordonnance. Au moins un est requis.");
                        }
                    }
                });
            } else {
                if (!addEditMedBtn) console.warn("EDIT_MODAL_JS: #add-edit-medication-row-btn not found in edit form.");
                if (!editMedContainer) console.warn("EDIT_MODAL_JS: #edit-medication-fields-container not found in edit form.");
                if (!medicationRowTemplate) console.warn("EDIT_MODAL_JS: #medication-row-template not found on page.");
            }
        } else {
            // This might log if the edit modal is not present on the page initially, which is fine.
            // console.log("EDIT_MODAL_JS: #form-edit-prescription not found (this is okay if modal isn't active).");
        }
        // --- END: JS for "Edit Prescription Modal" ---

        // --- START: JS for "View Prescription Modal" ---
        document.querySelectorAll('.view-prescription-btn').forEach(button => {
            button.addEventListener('click', function() {
                const url = this.dataset.url;
                const modal = document.getElementById('viewPrescriptionModal');
                const body = document.getElementById('viewPrescriptionModalBody');
                if (!modal || !body || !url) {
                    console.error("View Prescription Modal or Body or URL not found.");
                    return;
                }

                body.innerHTML = '<p class="text-center py-3">Chargement des détails...</p>'; // Loading state
                modal.classList.add('active');

                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            let errorMsg = `Erreur réseau: ${response.status}`;
                            return response.text().then(text => { // Try to get more error info
                                throw new Error(`${errorMsg} - ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data || !data.patient) { // Basic check if data is as expected
                            throw new Error("Données de l'ordonnance invalides ou patient manquant.");
                        }

                        let itemsHtml = ''; // Changed from <ul> to generate paragraphs
                        if (data.items && data.items.length > 0) {
                            data.items.forEach(item => {
                                let sentence = `Le médicament <strong>${item.medication_name || 'Non spécifié'}</strong>`;

                                if (item.dosage) {
                                    sentence += ` doit être pris à une dose de ${item.dosage}`;
                                } else {
                                    sentence += ` doit être pris`; // If no dosage, phrase it slightly differently
                                }

                                if (item.frequency) {
                                    sentence += `, ${item.frequency.toLowerCase()}/jour`; // e.g., "trois fois par jour"
                                }

                                if (item.duration) {
                                    sentence += `, pendant une durée de ${item.duration.toLowerCase()} jour`;
                                }
                                sentence += "."; // End base sentence

                                if (item.notes) {
                                    // Capitalize first letter of notes if it doesn't start with a common phrase
                                    let formattedNotes = item.notes.trim();
                                    if (formattedNotes) {
                                        formattedNotes = formattedNotes.charAt(0).toUpperCase() + formattedNotes.slice(1);
                                        sentence += ` ${formattedNotes}`;
                                        if (!formattedNotes.endsWith('.')) {
                                            sentence += ".";
                                        }
                                    }
                                }
                                itemsHtml += `<p style="margin-bottom: 0.75em;">${sentence}</p>`;
                            });
                        } else {
                            itemsHtml = "<p>Aucun médicament listé pour cette ordonnance.</p>";
                        }


                        const consultationLink = data.consultation
                            ? `Consultation du ${new Date(data.consultation.consultation_date).toLocaleDateString('fr-FR')} (Motif: ${data.consultation.reason_for_visit ? data.consultation.reason_for_visit.substring(0,30) : 'N/A'})`
                            : 'Aucune';

                        body.innerHTML = `
                            <p><strong>Patient:</strong> <span id="view_prescription_patient">${data.patient.name}</span></p>
                            <p><strong>Date:</strong> <span id="view_prescription_date">${new Date(data.prescription_date).toLocaleDateString('fr-FR')}</span></p>
                            <p><strong>Consultation Liée:</strong> <span id="view_prescription_consultation">${consultationLink}</span></p>
                            <p><strong>Notes Générales:</strong></p>
                            <p id="view_prescription_general_notes" style="white-space:pre-wrap; margin-bottom: 1em;">${data.general_notes || 'N/A'}</p>
                            <hr>
                            <h6>Médicaments:</h6>
                            <div id="view_prescription_items_list">${itemsHtml}</div>
                        `;
                    })
                    .catch(error => {
                        console.error('Error fetching or processing prescription details:', error);
                        body.innerHTML = `<p class="text-danger text-center py-3">Erreur lors du chargement des détails de l'ordonnance. (${error.message})</p>`;
                    });
            });
        });
        // --- END: JS for "View Prescription Modal" ---

    }); // End DOMContentLoaded
    </script>
</body>
</html>
<?php /**PATH C:\Users\hp\OneDrive\Desktop\consulatation-Med1\resources\views/layouts/doctor_dashboard.blade.php ENDPATH**/ ?>