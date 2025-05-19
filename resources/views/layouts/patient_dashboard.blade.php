<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MediConsult - Espace Patient</title>
    <style>
        /* Variables et styles de base */
        :root {
            --primary: #1976d2; --primary-light: #4791db; --primary-dark: #115293;
            --secondary: #43a047; --secondary-light: #76d275; --secondary-dark: #2d7031;
            --danger: #e53935; --danger-dark: #c02320;
            --warning: #ffb74d; --warning-dark: #ffaa00;
            --info: #00bcd4;  --info-dark: #00acc1;
            --text-dark: #333; --text-light: #f5f5f5;
            --bg-light: #f8f9fa; --bg-white: #ffffff; --shadow: 0 2px 5px rgba(0,0,0,0.1);
            --color-patient-appointments: #FFA500; /* Orange like doctor's appointment card */
            --color-patient-prescriptions: #2196F3; /* Blue like doctor's prescription card */
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--bg-light); color: var(--text-dark); line-height: 1.6; }
        a { text-decoration: none; color: var(--primary); cursor: pointer; }
        .dashboard-layout { display: flex; height: 100vh; overflow: hidden; }

        /* Sidebar */
        .sidebar { width: 280px; background-color: var(--primary-dark); color: var(--text-light); height: 100%; overflow-y: auto; position: fixed; left: 0; top: 0; z-index: 100; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
        /* This CSS should be in the stylesheet used by the patient dashboard */

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            /* ... other existing 'a' tag styles ... */
        }

        .menu-icon {
            margin-right: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;  /* Adjust as needed */
            height: 24px; /* Adjust as needed */
            flex-shrink: 0;
        }

        .menu-icon img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            display: block;
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
        .search-bar { position: relative; }
        .search-bar input { padding: 10px 15px 10px 40px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; width: 250px; }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; }
        .notification-bell { position: relative; cursor: pointer; width: 40px; height: 40px; background-color: var(--bg-light); border-radius: 50%; display: flex; align-items: center; justify-content: center; }
        .notification-badge { position: absolute; top: -5px; right: -5px; background-color: var(--danger); color: var(--text-light); width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; }
        .user-profile { display: flex; align-items: center; gap: 10px; cursor: pointer; }
        .user-profile-img { width: 40px; height: 40px; border-radius: 50%; background-color: var(--primary); color: var(--text-light); display: flex; align-items: center; justify-content: center; font-weight: bold; text-transform: uppercase; }
        .content-wrapper { padding: 20px; flex-grow: 1; overflow-y: auto; }

        /* Content Sections (SPA style) */
        .content-section { display: none; }
        .content-section.active { display: block; }

        /* REPLACE existing styles for these classes in patient_dashboard.blade.php */
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); /* Match doctor's minmax */
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: var(--bg-white);
            border-radius: 8px; /* Match doctor's radius */
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Match doctor's shadow */
            padding: 18px 20px; /* Match doctor's padding */
            display: flex;
            align-items: center;
            border-left: 5px solid transparent; /* This is key for the colored left border */
            transition: box-shadow 0.2s ease-in-out, transform 0.2s ease-in-out;
        }
        .stat-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        /* NEW: Add this class for the icon container (using image) */
        .stat-icon-img-only {
            width: 38px;  /* Adjust size as needed, doctor uses this */
            height: 38px; /* Adjust size as needed */
            margin-right: 18px; /* Space between icon and text */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .stat-icon-img-only img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain; /* Ensures the image fits well */
        }

        .stat-info { /* Ensure this matches doctor's styling */
            flex-grow: 1;
            line-height: 1.3;
        }

        .stat-info h3 { /* Adjust font size to match doctor's if desired */
            font-size: 1.9rem; /* Doctor's dashboard uses this, or adjust */
            font-weight: 700;
            margin-bottom: 0px; /* Tighten up spacing */
        }

        .stat-info p { /* Adjust font size and color */
            color: #555;
            font-size: 0.85rem; /* Doctor's dashboard uses this, or adjust */
            margin: 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* NEW: Specific styles for patient stat cards (border and H3 color) */
        .stat-card.card-patient-appointments { border-left-color: var(--color-patient-appointments); }
        .stat-card.card-patient-appointments .stat-info h3 { color: var(--color-patient-appointments); }

        .stat-card.card-patient-prescriptions { border-left-color: var(--color-patient-prescriptions); }
        .stat-card.card-patient-prescriptions .stat-info h3 { color: var(--color-patient-prescriptions); }

        .stat-info h3 { font-size: 1.8rem; margin-bottom: 5px; }
        .stat-info p { color: #777; font-size: 0.9rem; }
        .content-container { background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 25px; margin-bottom: 30px; }
        .section-title { font-size: 1.3rem; margin-bottom: 20px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
        .section-title.d-flex { display: flex; justify-content: space-between; align-items: center; } /* For button next to title */
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .form-control.is-invalid { border-color: var(--danger); }
        .text-danger { color: var(--danger); font-size: 0.875em; }
        .text-sm { font-size: 0.875em; }
        textarea.form-control { min-height: 80px; resize: vertical; }
        .form-group.full-width { grid-column: span 2; } /* For modal forms */
        .btn { padding: 10px 15px; background-color: var(--primary); color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; transition: background-color 0.3s; display: inline-flex; align-items: center; justify-content: center; text-decoration: none; }
        .btn:hover { background-color: var(--primary-dark); }
        .btn-secondary { background-color: #6c757d; }
        .btn-secondary:hover { background-color: #5a6268; }
        .btn-sm { padding: .25rem .5rem; font-size: .875rem; line-height: 1.5; border-radius: .2rem; }
        .mt-2 { margin-top: .5rem !important; }
        .mt-4 { margin-top: 1.5rem !important; }
        .form-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 15px; }
        .medication-list { margin-top: 15px; padding-left:0; list-style:none; }
        .medication-item { padding: 10px; background-color: var(--bg-light); margin-bottom: 10px; border-radius: 5px; }
        .medication-name { font-weight: bold; }
        .medication-dosage { color: #666; font-size: 0.9em; }

        /* Patient Appointment Item tyle */
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529; /* Or your --text-dark */
            border-collapse: collapse;
        }
        .table th,
        .table td {
            padding: 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6; /* Light gray border */
            text-align: left;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: var(--bg-light);
            font-weight: bold;
        }
        .table tbody + tbody { /* In case you ever have multiple tbody sections */
            border-top: 2px solid #dee2e6;
        }
        .patient-appointments-table tbody tr:hover { /* Hover effect for rows */
            background-color: #f8f9fa; /* A very light gray or your --bg-light */
        }

        /* Specific column styling examples for patient's appointment table */
        .patient-appointments-table .appointment-time-header,
        .patient-appointments-table .appointment-time {
            width: 160px; /* Adjust as needed */
            white-space: nowrap;
        }
        .patient-appointments-table .appointment-doctor-header, /* Header for doctor column */
        .patient-appointments-table .appointment-doctor {
            /* Allow this to take more space */
        }
        .patient-appointments-table .appointment-type-header, /* Header for type/reason column */
        .patient-appointments-table .appointment-type {
            color: #666;
            font-size: 0.9em;
        }
        .patient-appointments-table .appointment-status-header, /* Header for status column */
        .patient-appointments-table .appointment-status-cell { /* Cell containing the status badge */
            width: 120px; /* Adjust for status badge */
            text-align: center;
        }
        .patient-appointments-table .appointment-actions-header, /* Header for actions column */
        .patient-appointments-table .appointment-actions {
            width: 200px; /* Adjust for the cancel button */
            text-align: right;
        }

        /* Overflow handling for cells that might have long content */
        .patient-appointments-table .appointment-doctor,
        .patient-appointments-table .appointment-type {
            /* max-width: 200px; /* Example, if needed */
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .btn-danger {
            background-color: var(--danger); /* Your red color variable */
            color: white; /* Or whatever text color contrasts well */
        }
        .btn-danger:hover {
            background-color: var(--danger-dark); /* A darker shade of red for hover */
        }

        /* Status badge styles (ensure these are defined) */
        .appointment-status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
            color: white;
            white-space: nowrap;
        }
        .status-scheduled { background-color: var(--primary-light); }
        .status-completed { background-color: var(--secondary); }
        .status-cancelled { background-color: var(--danger); }
        .status-default { background-color: #6c757d; }

        /* Table responsiveness */
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto; /* Adds horizontal scroll on small screens */
            -webkit-overflow-scrolling: touch;
        }

        /* --- Patient Medical File Specific Styles --- */

        /* Main container for each section within the medical file (Personal Info, Consultations, Prescriptions) */
        .medical-file-section-container {
            background-color: var(--bg-white); /* Uses your defined --bg-white */
            border-radius: 10px;              /* Uses your defined border-radius for containers */
            box-shadow: var(--shadow);            /* Uses your defined --shadow */
            padding: 25px;
            margin-bottom: 30px;              /* Consistent spacing between sections */
        }

        /* Title for each section (e.g., "Informations Personnelles") */
        .medical-file-section-container .section-title {
            font-size: 1.4rem; /* Slightly larger title */
            margin-bottom: 25px; /* More space below title */
            padding-bottom: 12px; /* Underline padding */
            border-bottom: 2px solid var(--primary-light); /* Use a theme color for underline */
            color: var(--primary-dark); /* Darker theme color for title text */
            font-weight: 600;
        }

        /* Grid for Personal Information */
        .personal-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Responsive columns */
            gap: 20px 30px; /* Row gap, Column gap */
        }

        .info-block {
            /* No border by default, looks cleaner */
        }

        .info-block .info-label {
            font-weight: 500; /* Was 600, slightly less heavy */
            color: #555;      /* Dark gray for label */
            display: block;
            margin-bottom: 5px;
            font-size: 0.9em; /* Smaller label text */
            /* text-transform: uppercase; */ /* Removed uppercase for softer look, optional */
        }

        .info-block .info-value {
            font-size: 1em;
            color: var(--text-dark);
            word-break: break-word; /* Handle long values like emails */
        }

        /* Card styling for each Consultation or Prescription entry */
        .medical-entry-card {
            background-color: #fdfdfd; /* Very light gray, almost white, for the card background */
            border: 1px solid #e9e9e9; /* Softer border color */
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 25px;      /* Space between cards */
            box-shadow: 0 2px 5px rgba(0,0,0,0.06); /* Softer shadow */
            transition: box-shadow 0.2s ease-in-out, transform 0.2s ease-in-out;
        }

        .medical-entry-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); /* More pronounced shadow on hover */
            /* transform: translateY(-2px); */ /* Slight lift on hover - optional */
        }

        /* Header within each entry card (for Date and Doctor Name) */
        .entry-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 12px;
            border-bottom: 1px dashed #ccc; /* Softer dashed line */
        }

        .entry-header h5 { /* For Consultation Date / Prescription Date */
            margin: 0;
            font-size: 1.15em; /* Slightly larger date heading */
            color: var(--primary);
            font-weight: 600;
        }

        .entry-header .doctor-name {
            font-size: 0.9em;
            color: #6c757d; /* Bootstrap's text-muted color */
            font-style: italic;
        }

        /* Styling for detail blocks (Motif, Symptômes, Notes, Diagnostic, etc.) */
        .entry-detail {
            margin-bottom: 15px; /* Space between detail items */
        }
        .entry-detail:last-child {
            margin-bottom: 0;
        }

        .entry-detail strong { /* For labels like "Motif:", "Symptômes:" */
            display: block;
            color: var(--text-dark);
            font-weight: 600; /* Bold labels */
            margin-bottom: 6px;
            font-size: 0.95em; /* Standard label size */
        }

        .entry-detail .detail-content { /* For the actual value/text */
            padding: 12px 15px; /* More padding */
            background-color: var(--bg-light); /* Uses your defined --bg-light */
            border-radius: 5px;
            border: 1px solid #eef0f3; /* Lighter border for content box */
            white-space: pre-wrap;     /* Preserve formatting for notes/symptoms */
            font-size: 0.95em;         /* Readable content text */
            color: #333;             /* Darker text for content */
            line-height: 1.6;
        }

        /* Styling for the list of medications within a prescription card */
        .medication-list-item {
            padding: 10px 0; /* More vertical padding */
            border-bottom: 1px dotted #ddd; /* Dotted separator for medication items */
            font-size: 0.95em; /* Consistent font size */
        }

        .medication-list-item:last-child {
            border-bottom: none;
            padding-bottom: 0; /* Remove bottom padding for the last item */
        }

        .medication-list-item .med-name {
            font-weight: bold;
            color: var(--primary-dark); /* Use a theme color for medication name */
        }

        .medication-list-item .med-details {
            color: #454545; /* Slightly darker gray for details */
            display: block;
            margin-left: 15px; /* More indentation */
            font-size: 0.9em; /* Slightly smaller for details */
            line-height: 1.5;
        }
        .medication-list-item .med-details span { /* If you wrap parts of details in spans */
            margin-right: 8px; /* Space between detail parts */
        }


        .medication-list-item .med-notes {
            display: block;
            margin-left: 15px;
            margin-top: 4px; /* Add some space above notes */
            font-style: italic;
            color: #777;
            font-size: 0.85em; /* Smaller for notes */
        }

        /* Utility classes from your Blade file (ensure they are defined or use Bootstrap) */
        .text-center { text-align: center !important; }
        .py-3 { padding-top: 1rem !important; padding-bottom: 1rem !important; }
        .mb-4 { margin-bottom: 1.5rem !important; }
        .mt-4 { margin-top: 1.5rem !important; }
        .mt-3 { margin-top: 1rem !important; }
        .text-end { text-align: right !important; }
        .d-flex { display: flex !important; }
        .justify-content-between { justify-content: space-between !important; }
        .align-items-center { align-items: center !important; }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-1 { margin-bottom: .25rem !important; }
        .mb-2 { margin-bottom: .5rem !important; }
        .p-3 { padding: 1rem !important; }
        .border { border: 1px solid #dee2e6 !important; } /* Bootstrap's default border color */
        .rounded { border-radius: .25rem !important; } /* Bootstrap's default border radius */
        .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; } /* Bootstrap's small shadow */
        .list-unstyled { padding-left: 0; list-style: none; }
        .ps-3 { padding-left: 1rem !important; }
        .text-muted { color: #6c757d !important; } /* Bootstrap's text-muted color */

        /* Ensure these are defined if you used them in the dossier and are not using Bootstrap full CSS */
        .list-group.list-group-flush .list-group-item {
            border-width: 0 0 1px;
            padding: .75rem 0; /* Adjust as needed if you use list-group-item directly */
            background-color: transparent; /* Flush items usually don't have background */
        }
        .list-group.list-group-flush .list-group-item:last-child {
            border-bottom-width: 0;
        }
        .w-100 { width: 100% !important; }

        /* Prescriptions (Patient View) */
        .prescription-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .prescription-header { display: flex; justify-content: space-between; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .prescription-doctor { font-weight: bold; }
        .prescription-date { color: #666; }
        .prescription-details { margin-bottom: 10px; }
        .prescription-actions { display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px; }
        /* Lab Results */
        .results-card { border: 1px solid #eee; border-radius: 8px; padding: 15px; margin-bottom: 15px; }
        .results-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 10px; }
        .results-header > div:first-child { font-weight:bold;} .results-header > div:last-child { font-size: 0.9em; color:#666;}
        .results-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .results-table th, .results-table td { padding: 10px; border-bottom: 1px solid #eee; text-align: left; }
        .results-table th { background-color: var(--bg-light); font-weight: 500; }
        .result-normal { color: var(--secondary); }
        .result-abnormal { color: var(--danger); font-weight: bold; }
        /* Patient Messaging */
        .messagerie-container { background-color: var(--bg-white); border-radius: 10px; box-shadow: var(--shadow); padding: 25px; display: grid; grid-template-columns: 300px 1fr; gap: 20px; height: calc(100vh - 220px); margin-bottom: 30px; }
        .contacts-list { border-right: 1px solid #eee; overflow-y: auto; padding-right: 10px; }
        .contact-item { padding: 15px; border-bottom: 1px solid #eee; cursor: pointer; }
        .contact-item:last-child { border-bottom:none; }
        .contact-item.active, .contact-item:hover { background-color: var(--bg-light); }
        .contact-name { font-weight: bold; margin-bottom: 5px; }
        .contact-preview { color: #666; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .messages-area { display: flex; flex-direction: column; height: 100%; }
        .messages-header { padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .messages-header h3 { margin:0; font-size: 1.2em;}
        .messages-content { flex: 1; overflow-y: auto; padding: 15px 0; display: flex; flex-direction: column; }
        .message { max-width: 70%; padding: 10px 15px; border-radius: 10px; margin-bottom: 10px; line-height: 1.4;}
        .message-text { word-wrap: break-word;}
        .message-received { background-color: var(--bg-light); align-self: flex-start; }
        .message-sent { background-color: var(--primary-light); color: white; align-self: flex-end; }
        .message-time { font-size: 0.8rem; text-align: right; margin-top: 5px; opacity: 0.8; }
        .message-form { display: flex; border-top: 1px solid #eee; padding-top: 15px; }
        .message-input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; margin-right: 10px; }
        .send-btn { padding: 10px 20px; }
        /* Settings Form */
        .settings-form { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .settings-form .form-group div { margin-bottom: 5px; } .settings-form .form-group div label {font-weight:normal; margin-left:5px;}
        /* Payments */
        .payment-list { list-style: none; padding-left: 0; }
        .payment-item { display: flex; justify-content: space-between; padding: 15px; border-bottom: 1px solid #eee; align-items: center; }
        .payment-item:last-child { border-bottom:none; }
        .payment-info { flex: 1; }
        .payment-date { font-weight: 500; }
        .payment-details { color: #666; font-size: 0.9rem; }
        .payment-amount { font-weight: bold; min-width: 100px; text-align: right; }
        .payment-status { padding: 5px 10px; border-radius: 20px; text-align: center; font-size: 0.8rem; font-weight: 500; margin-left: 15px; min-width: 100px; }
        .status-paid { background-color: var(--secondary-light); color: white; }
        .status-pending { background-color: var(--warning); color: var(--text-dark); }
        .status-received { background-color: var(--primary-light); color: white; }

        /* Modals */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .modal-overlay.active { display: flex; opacity: 1; visibility: visible; }
        .modal { width: 90%; max-width: 700px; /* Slightly smaller for patient modal maybe */ background-color: var(--bg-white); border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); overflow: hidden; transform: translateY(-20px) scale(0.95); transition: transform 0.3s ease, opacity 0.3s ease; opacity: 0; }
        .modal-overlay.active .modal { transform: translateY(0) scale(1); opacity: 1; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 20px; border-bottom: 1px solid #eee; background-color: var(--primary); color: white; }
        .modal-title { font-size: 1.2rem; font-weight: 500; }
        .modal-close { font-size: 1.4rem; cursor: pointer; background: none; border: none; color: white; }
        .modal-body { padding: 20px; max-height: 70vh; overflow-y: auto; }
        .modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 15px 20px; border-top: 1px solid #eee; background-color: var(--bg-light); }
        .modal-form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .modal-form .form-group.full-width { grid-column: span 2; }
        .alert { padding: .75rem 1.25rem; margin-bottom: 1rem; border: 1px solid transparent; border-radius: .25rem; }
        .alert-danger { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }
        .alert-success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border-width: 0; }

    </style>
</head>
<body>
    <div class="dashboard-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <a href="{{ route('dashboard') }}" class="logo">Medi<span>Consult</span></a>
            </div>
            @auth
            <div class="user-info">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                <div class="user-name">{{ Auth::user()->name }}</div>
                @if(Auth::user()->role)
                <div class="user-role">{{ ucfirst(Auth::user()->role->name) }}</div>
                @endif
            </div>
            @endauth
            <ul class="sidebar-menu">
                <li>
                    <a href="#" class="menu-link active" data-section="patient_dashboard_content">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/tableau_de_bord.png') }}" alt="Dashboard Icon">
                        </div>
                        <span>Tableau de bord</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_appointments_content">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/rendez_vous.png') }}" alt="Appointments Icon">
                        </div>
                        <span>Mes rendez-vous</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_medical_file_content">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/dossier_medical.png') }}" alt="Medical File Icon">
                        </div>
                        <span>Mon dossier médical</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_prescriptions_content">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/ordonnances.png') }}" alt="Prescriptions Icon">
                        </div>
                        <span>Mes ordonnances</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_messaging_content">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/messages.png') }}" alt="Messaging Icon">
                        </div>
                        <span>Messagerie</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="menu-link" data-section="patient_settings_content">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/profile.png') }}" alt="Profile Icon">
                        </div>
                        <span>Profile</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form-patient-dashboard" style="display: none;">@csrf</form>
                    <a href="{{ route('logout') }}" class="menu-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form-patient-dashboard').submit();">
                        <div class="menu-icon">
                            <img src="{{ asset('assets/sidebar/logout.png') }}" alt="Logout Icon">
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
                <h1 class="page-title" id="patientDynamicPageTitle">Tableau de bord</h1>
                @auth
                <div class="topbar-actions">
                    <div class="user-profile">
                        <div class="user-profile-img">{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</div>
                        <span>{{ Str::before(Auth::user()->name, ' ') }}</span>
                    </div>
                </div>
                @endauth
            </div>

            <div class="content-wrapper">
                {{-- Session Messages & Validation Errors --}}
                @if(session('success')) <div class="alert alert-success success-alert" role="alert">{{ session('success') }}</div> @endif
                @if(session('error')) <div class="alert alert-danger error-alert" role="alert">{{ session('error') }}</div> @endif
                @php $openModalPatient = session('open_modal_on_load'); @endphp
                @if($errors->any() && $openModalPatient === 'patient-create-appointment-modal' )
                    <div class="alert alert-danger error-alert"><strong>Erreurs:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @elseif($errors->any() && !$openModalPatient )
                     <div class="alert alert-danger error-alert"><strong>Erreurs:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif


                @include('patient.dashboard')
                @include('patient.appointments', [
                    'upcomingAppointments' => $upcomingAppointments ?? collect(),
                    'pastAppointments' => $pastAppointments ?? collect()
                ])
                @include('patient.medical_file')
                @include('patient.prescriptions')
                @include('patient.messaging')
                @include('patient.settings')
            </div>
        </main>
    </div>

    <!-- Modal pour que le PATIENT crée un nouveau rendez-vous -->
    <div class="modal-overlay {{ ($errors->any() && session('open_modal_on_load') === 'patient-create-appointment-modal') ? 'active' : '' }}" id="patient-create-appointment-modal">
        <div class="modal">
            <div class="modal-header">
                <h3 class="modal-title">Prendre un Nouveau Rendez-vous</h3>
                <button type="button" class="modal-close" aria-label="Fermer">×</button>
            </div>
            {{-- The FORM tag now starts here and wraps modal-body and modal-footer --}}
            <form id="form-patient-create-appointment-modal" action="{{ route('patient.appointments.store') }}" method="POST">
                @csrf {{-- CSRF token at the beginning of the form --}}
                <div class="modal-body">
                    @if($errors->any() && session('open_modal_on_load') === 'patient-create-appointment-modal')
                        <div class="alert alert-danger">
                            <strong>Erreurs:</strong>
                            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                        </div>
                    @endif

                    {{-- This div now handles the grid layout for form elements --}}
                    <div class="modal-form">
                        <div class="form-group">
                            <label for="modal_patient_appt_doctor_select">Médecin</label>
                            <select id="modal_patient_appt_doctor_select" name="doctor_id" class="form-control @error('doctor_id') is-invalid @enderror" required>
                                <option value="">Sélectionner un médecin</option>
                                @foreach ($doctors ?? [] as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>{{ $doctor->name }}</option>
                                @endforeach
                            </select>
                            @error('doctor_id') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="modal_patient_appt_date_input">Date</label>
                            <input type="date" id="modal_patient_appt_date_input" name="appointment_date" class="form-control @error('appointment_date') is-invalid @enderror" value="{{ old('appointment_date', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                            @error('appointment_date') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group full-width">
                            <label for="modal_patient_appt_time_select">Heure Disponible</label>
                            <select id="modal_patient_appt_time_select" name="appointment_time" class="form-control @error('appointment_time') is-invalid @enderror" required>
                                <option value="">Sélectionnez d'abord un médecin et une date</option>
                                 @if(old('appointment_time'))
                                    <option value="{{ old('appointment_time') }}" selected>{{ old('appointment_time') }} (Précédemment)</option>
                                @endif
                            </select>
                            @error('appointment_time') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                            <div id="modal_patient_slots_loading" style="display: none; margin-top: 5px;">Chargement des créneaux...</div>
                            <div id="modal_patient_slots_error" style="display: none; color: red; margin-top: 5px;"></div>
                        </div>

                        <div class="form-group full-width">
                            <label for="modal_patient_appt_notes_textarea">Notes (optionnel)</label>
                            <textarea id="modal_patient_appt_notes_textarea" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Motif court ou informations...">{{ old('notes') }}</textarea>
                            @error('notes') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div> {{-- End of .modal-form div --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close-btn">Annuler</button>
                    {{-- ADDED THE SUBMIT BUTTON --}}
                    <button type="submit" class="btn btn-primary">Créer le rendez-vous</button>
                </div>
            </form> {{-- The FORM tag now ends here, after the modal-footer --}}
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- SPA Navigation Logic ---
        const menuLinks = document.querySelectorAll('.sidebar-menu .menu-link');
        const contentSections = document.querySelectorAll('.content-wrapper .content-section');
        const pageTitleElement = document.getElementById('patientDynamicPageTitle'); // Ensure this ID is unique if you have multiple dashboards on one page (unlikely)

        function activateSection(sectionId) {
            contentSections.forEach(section => {
                section.classList.toggle('active', section.id === sectionId);
            });
            if(sectionId) localStorage.setItem('activePatientSection', sectionId);
        }

        menuLinks.forEach(link => {
            const isLogoutLink = link.getAttribute('href') === "{{ route('logout') }}" || (link.onclick && link.onclick.toString().includes('logout-form'));
            const isExternalProfile = link.getAttribute('href') === "{{ route('profile.edit') }}"; // Standard profile edit
            const isSettingsSPA = link.getAttribute('data-section') === 'patient_settings_content' && link.getAttribute('href') === '#'; // If 'Profile' in sidebar is SPA

            if (isLogoutLink || (isExternalProfile && !isSettingsSPA) ) return;

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
        const savedPatientSection = localStorage.getItem('activePatientSection');
        let defaultPatientActiveLink = null;
        if (savedPatientSection) defaultPatientActiveLink = document.querySelector(`.sidebar-menu .menu-link[data-section="${savedPatientSection}"]`);
        if (!defaultPatientActiveLink) defaultPatientActiveLink = document.querySelector('.sidebar-menu .menu-link.active') || (menuLinks.length > 0 ? menuLinks[0] : null);
        if (defaultPatientActiveLink) {
            menuLinks.forEach(item => item.classList.remove('active'));
            defaultPatientActiveLink.classList.add('active');
            activateSection(defaultPatientActiveLink.getAttribute('data-section'));
            if (pageTitleElement && defaultPatientActiveLink.querySelector('span')) pageTitleElement.textContent = defaultPatientActiveLink.querySelector('span').textContent;
        }

        // --- General Modal Toggle Logic ---
        document.querySelectorAll('[data-modal-target]').forEach(button => {
            button.addEventListener('click', () => {
                const modalId = button.getAttribute('data-modal-target');
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.classList.add('active');
                     // If opening patient create appointment modal, and date is pre-filled, fetch slots
                    if (modalId === 'patient-create-appointment-modal') {
                        const dateInput = document.getElementById('modal_patient_appt_date_input');
                        const doctorInput = document.getElementById('modal_patient_appt_doctor_select');
                        if (dateInput && dateInput.value && doctorInput && doctorInput.value) {
                            fetchPatientModalAvailableSlots();
                        }
                    }
                }
            });
        });
        document.querySelectorAll('.modal-close, .modal-close-btn').forEach(button => {
            button.addEventListener('click', () => { button.closest('.modal-overlay')?.classList.remove('active'); });
        });
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) { if (e.target === this) this.classList.remove('active'); });
        });

        // --- JS for Patient Create Appointment Modal Slots ---
        const patientCreateApptModalEl = document.getElementById('patient-create-appointment-modal');
        let doctorSelectPatientModal, dateInputPatientModal, timeSelectPatientModal, slotsLoadingPatientModal, slotsErrorPatientModal;

        if(patientCreateApptModalEl){ // Ensure modal exists before selecting children
            doctorSelectPatientModal = document.getElementById('modal_patient_appt_doctor_select');
            dateInputPatientModal = document.getElementById('modal_patient_appt_date_input');
            timeSelectPatientModal = document.getElementById('modal_patient_appt_time_select');
            slotsLoadingPatientModal = document.getElementById('modal_patient_slots_loading');
            slotsErrorPatientModal = document.getElementById('modal_patient_slots_error');

            function fetchPatientModalAvailableSlots() {
                const doctorId = doctorSelectPatientModal.value;
                const selectedDate = dateInputPatientModal.value;
                const previouslySelectedTime = timeSelectPatientModal.dataset.oldTime || "{{ old('appointment_time', '') }}";

                timeSelectPatientModal.innerHTML = '<option value="">Chargement...</option>';
                timeSelectPatientModal.disabled = true;
                slotsErrorPatientModal.style.display = 'none';
                slotsErrorPatientModal.textContent = '';

                if (!doctorId || !selectedDate) { timeSelectPatientModal.innerHTML = '<option value="">Sélectionnez un médecin et une date</option>'; return; }
                if (new Date(selectedDate) < new Date(new Date().toISOString().split('T')[0])) {
                     timeSelectPatientModal.innerHTML = '<option value="">Date invalide</option>';
                     slotsErrorPatientModal.textContent = 'La date ne peut pas être dans le passé.'; slotsErrorPatientModal.style.display = 'block'; return;
                }
                slotsLoadingPatientModal.style.display = 'block';

                fetch("{{ route('appointments.available_slots') }}", {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
                    body: JSON.stringify({ doctor_id: doctorId, date: selectedDate })
                })
                .then(response => response.ok ? response.json() : response.json().then(err => { console.error("Server error:", err); throw err; }))
                .then(data => {
                    slotsLoadingPatientModal.style.display = 'none';
                    timeSelectPatientModal.innerHTML = '<option value="">-- Choisissez une heure --</option>';
                    if (data.slots && data.slots.length > 0) {
                        data.slots.forEach(slot => {
                            const opt = document.createElement('option'); opt.value = slot; opt.textContent = slot;
                            if (slot === previouslySelectedTime) opt.selected = true;
                            timeSelectPatientModal.appendChild(opt);
                        });
                        timeSelectPatientModal.disabled = false;
                    } else {
                        if (previouslySelectedTime) { const opt = document.createElement('option'); opt.value = previouslySelectedTime; opt.textContent = previouslySelectedTime + " (Non disponible)"; opt.disabled = true; opt.selected = true; timeSelectPatientModal.appendChild(opt); }
                        else { timeSelectPatientModal.innerHTML = '<option value="">Aucun créneau</option>'; }
                        slotsErrorPatientModal.textContent = data.message || 'Aucun créneau disponible.'; slotsErrorPatientModal.style.display = 'block';
                    }
                })
                .catch(error => {
                    slotsLoadingPatientModal.style.display = 'none'; timeSelectPatientModal.innerHTML = '<option value="">Erreur</option>';
                    console.error('Error fetching slots for patient modal:', error);
                    let errorMsg = (error && error.errors) ? Object.values(error.errors).flat().join(' ') : ((error && error.message) ? error.message : 'Erreur de chargement.');
                    slotsErrorPatientModal.textContent = `Erreur: ${errorMsg}`; slotsErrorPatientModal.style.display = 'block';
                });
            }
            if(doctorSelectPatientModal) doctorSelectPatientModal.addEventListener('change', fetchPatientModalAvailableSlots);
            if(dateInputPatientModal) dateInputPatientModal.addEventListener('change', fetchPatientModalAvailableSlots);
            if(timeSelectPatientModal && "{{old('appointment_time')}}") timeSelectPatientModal.dataset.oldTime = "{{old('appointment_time')}}";
        }

        const patientModalToOpen = "{{ session('open_modal_on_load') }}";
        if (patientModalToOpen) {
            const modalEl = document.getElementById(patientModalToOpen);
            if (modalEl) {
                modalEl.classList.add('active');
                if (patientModalToOpen === 'patient-create-appointment-modal' && dateInputPatientModal && dateInputPatientModal.value && doctorSelectPatientModal && doctorSelectPatientModal.value) {
                    fetchPatientModalAvailableSlots(); // Re-fetch if modal is opened due to error and fields have values
                }
            }
        }

        // --- Auto-hide session alerts ---
        const successAlerts = document.querySelectorAll('.alert-success.success-alert');
        successAlerts.forEach(alertEl => {
            setTimeout(() => {
                alertEl.style.transition = 'opacity 0.5s ease'; alertEl.style.opacity = '0';
                setTimeout(() => { alertEl.style.display = 'none'; }, 500);
            }, 10000);
        });
        // Add similar for .error-alert if you want them to auto-hide too
    });
    </script>
</body>
</html>
