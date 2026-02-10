<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FreshHR') - Sistem Manajemen HR</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 50%, #f0fdf4 100%);
            background-attachment: fixed;
            color: #1e293b;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Colors */
        :root {
            --primary: #4ade80;
            --primary-dark: #22c55e;
            --primary-light: #bbf7d0;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-700: #334155;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
        }

        /* No Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-right: 1px solid rgba(226, 232, 240, 0.6);
            padding: 24px;
            overflow-y: auto;
            z-index: 101;
            display: flex;
            flex-direction: column;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 40px;
            padding: 0 16px;
        }

        .sidebar-logo-icon {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 25px -5px rgba(74, 222, 128, 0.4);
        }

        .sidebar-logo-icon span {
            color: white;
            font-weight: 700;
            font-size: 20px;
        }

        .sidebar-logo-text {
            font-size: 20px;
            font-weight: 700;
            color: var(--slate-800);
            letter-spacing: -0.5px;
        }

        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            border-radius: 50px;
            text-decoration: none;
            color: var(--slate-500);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-item:hover {
            background: var(--slate-50);
        }

        .sidebar-item.active {
            background: var(--primary);
            color: var(--slate-900);
            font-weight: 600;
            box-shadow: 0 10px 25px -5px rgba(74, 222, 128, 0.3);
        }

        .sidebar-item.active svg {
            color: var(--slate-900);
        }

        .sidebar-item svg {
            width: 18px;
            height: 18px;
            transition: color 0.2s;
        }

        .sidebar-item:hover svg {
            color: var(--primary-dark);
        }

        /* Sidebar Menu Group */
        .sidebar-group {
            margin-bottom: 8px;
        }
        .sidebar-group {
            margin-bottom: 8px;
        }

        .sidebar-group-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 24px;
            border-radius: 50px;
            cursor: pointer;
            color: var(--slate-500);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            user-select: none;
        }

        .sidebar-group-header:hover {
            background: var(--slate-50);
            color: var(--slate-700);
        }

        .sidebar-group-header.active {
            background: var(--primary);
            color: var(--slate-900);
            font-weight: 600;
        }

        .sidebar-group-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-group-left svg {
            width: 18px;
            height: 18px;
        }

        .sidebar-group-toggle {
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.3s ease;
        }

        .sidebar-group-toggle svg {
            width: 14px;
            height: 14px;
        }

        .sidebar-group.open .sidebar-group-toggle {
            transform: rotate(180deg);
        }

        .sidebar-group-header:not(.active):hover {
            background: var(--slate-50);
        }

        .sidebar-group-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .sidebar-group.open .sidebar-group-content {
            max-height: 500px;
        }

        .sidebar-group-items {
            padding-left: 16px;
            padding-top: 2px;
            padding-bottom: 4px;
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sidebar-group-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 24px;
            border-radius: 50px;
            text-decoration: none;
            color: var(--slate-400);
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .sidebar-group-item:hover {
            background: var(--slate-50);
            color: var(--slate-700);
        }

        .sidebar-group-item.active {
            background: var(--primary-light);
            color: var(--slate-900);
            font-weight: 600;
        }

        .sidebar-group-item svg {
            width: 16px;
            height: 16px;
        }

        .sidebar-footer {
            margin-top: auto;
            padding-top: 24px;
            border-top: 1px solid rgba(226, 232, 240, 0.6);
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px;
            background: var(--slate-50);
            border-radius: 24px;
        }

        .sidebar-user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid white;
        }

        .sidebar-user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-user-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-800);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .sidebar-user-role {
            font-size: 10px;
            font-weight: 700;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            padding-bottom: 32px;
        }

        .main-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 40px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
            position: sticky;
            top: 0;
            z-index: 100;
            margin: 0 0 24px 0;
        }

        .main-header-scrolled {
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .main-header-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--slate-800);
        }

        .main-header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 50px;
            padding: 10px 24px 10px 48px;
            font-size: 14px;
            width: 256px;
            outline: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: all 0.2s;
        }

        .search-box input:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2);
        }

        .search-box svg {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            color: var(--slate-400);
        }

        .header-icon-btn {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            color: var(--slate-500);
            transition: all 0.2s;
        }

        .header-icon-btn:hover {
            background: white;
            color: var(--primary-dark);
            border-color: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 222, 128, 0.2);
        }

        /* User Dropdown */
        .user-dropdown {
            position: relative;
        }

        .user-dropdown-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 6px 6px 16px;
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(226, 232, 240, 0.6);
            border-radius: 50px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: all 0.2s;
        }

        .user-dropdown-btn:hover {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(74, 222, 128, 0.2);
        }

        .user-dropdown-info {
            text-align: right;
        }

        .user-dropdown-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--slate-800);
        }

        .user-dropdown-role {
            font-size: 11px;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-dropdown-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--slate-100);
        }

        .user-dropdown-menu {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            min-width: 220px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.15), 0 0 0 1px rgba(255,255,255,0.5) inset;
            border: 1px solid rgba(226, 232, 240, 0.6);
            padding: 10px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px) scale(0.95);
            transform-origin: top right;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 200;
        }

        .user-dropdown-menu::before {
            content: '';
            position: absolute;
            top: -6px;
            right: 20px;
            width: 12px;
            height: 12px;
            background: rgba(255, 255, 255, 0.95);
            border-left: 1px solid rgba(226, 232, 240, 0.6);
            border-top: 1px solid rgba(226, 232, 240, 0.6);
            transform: rotate(45deg);
        }

        .user-dropdown.active .user-dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .user-dropdown-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 16px;
            border-radius: 12px;
            color: var(--slate-700);
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
            cursor: pointer;
            border: none;
            background: none;
            width: 100%;
        }

        .user-dropdown-item:hover {
            background: var(--slate-50);
            color: var(--slate-900);
        }

        .user-dropdown-item svg {
            width: 18px;
            height: 18px;
            color: var(--slate-400);
        }

        .user-dropdown-item:hover svg {
            color: var(--primary-dark);
        }

        .user-dropdown-divider {
            height: 1px;
            background: var(--slate-100);
            margin: 8px 0;
        }

        .user-dropdown-item.danger {
            color: #ef4444;
        }

        .user-dropdown-item.danger:hover {
            background: #fee2e2;
        }

        .user-dropdown-item.danger svg {
            color: #ef4444;
        }

        .main-body {
            padding: 0 40px;
        }

        /* Cards */
        .card {
            background: white;
            border-radius: 32px;
            padding: 24px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.04);
            border: 1px solid white;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 600;
            border-radius: 50px;
            padding: 10px 24px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            text-decoration: none;
        }

        .btn:active {
            transform: scale(0.95);
        }

        .btn-primary {
            background: var(--primary);
            color: var(--slate-900);
            box-shadow: 0 10px 25px -5px rgba(74, 222, 128, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-secondary {
            background: white;
            color: var(--slate-600);
            border: 1px solid var(--slate-200);
        }

        .btn-secondary:hover {
            background: var(--slate-50);
        }

        .btn-danger {
            background: #f87171;
            color: white;
            box-shadow: 0 10px 25px -5px rgba(248, 113, 113, 0.3);
        }

        .btn-danger:hover {
            background: #ef4444;
        }

        .btn-sm {
            padding: 6px 16px;
            font-size: 12px;
        }

        .btn-lg {
            padding: 16px 32px;
            font-size: 16px;
        }

        /* Badges */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 16px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
        }

        .badge-success { background: #dcfce7; color: #15803d; }
        .badge-warning { background: #fef9c3; color: #a16207; }
        .badge-danger { background: #fee2e2; color: #b91c1c; }
        .badge-info { background: #dbeafe; color: #1d4ed8; }

        /* Inputs */
        .form-input {
            width: 100%;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 50px;
            padding: 12px 24px;
            font-size: 14px;
            outline: none;
            transition: all 0.2s;
        }

        .form-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2);
        }

        .form-label {
            display: block;
            font-size: 10px;
            font-weight: 700;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            margin-left: 16px;
        }

        .form-select {
            width: 100%;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 50px;
            padding: 12px 24px;
            font-size: 14px;
            outline: none;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%2394a3b8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            background-size: 16px;
        }

        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2);
        }

        .form-textarea {
            width: 100%;
            background: var(--slate-50);
            border: 1px solid var(--slate-200);
            border-radius: 24px;
            padding: 16px 24px;
            font-size: 14px;
            outline: none;
            resize: vertical;
            min-height: 100px;
            font-family: inherit;
            transition: all 0.2s;
        }

        .form-textarea:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.2);
        }

        /* Tables */
        .table-wrapper {
            background: white;
            border-radius: 40px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid var(--slate-50);
        }

        .data-table {
            width: 100%;
            text-align: left;
            border-collapse: collapse;
        }

        .data-table th {
            padding: 20px 32px;
            font-size: 10px;
            font-weight: 700;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 1px;
            background: rgba(248, 250, 252, 0.5);
        }

        .data-table td {
            padding: 20px 32px;
            font-size: 14px;
            border-top: 1px solid var(--slate-50);
        }

        .data-table tr:hover {
            background: rgba(248, 250, 252, 0.5);
        }

        .table-footer {
            padding: 24px 32px;
            background: rgba(248, 250, 252, 0.3);
            border-top: 1px solid var(--slate-50);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .table-footer-text {
            font-size: 12px;
            color: var(--slate-400);
        }

        /* Stat Card */
        .stat-card {
            background: white;
            border-radius: 32px;
            padding: 24px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.04);
            border: 1px solid white;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 16px;
            transition: all 0.3s;
            cursor: default;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-card-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-card-icon.green { background: #dcfce7; color: #22c55e; }
        .stat-card-icon.red { background: #fee2e2; color: #ef4444; }
        .stat-card-icon.blue { background: #dbeafe; color: #3b82f6; }
        .stat-card-icon.yellow { background: #fef9c3; color: #eab308; }

        .stat-card-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--slate-800);
        }

        .stat-card-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--slate-400);
            margin-top: 4px;
        }

        /* Grid */
        .grid { display: grid; gap: 24px; }
        .grid-cols-1 { grid-template-columns: repeat(1, 1fr); }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .grid-cols-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-cols-4 { grid-template-columns: repeat(4, 1fr); }

        .dashboard-split-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 32px;
        }

        .schedule-split-grid {
            display: grid;
            grid-template-columns: 3fr 1fr;
            gap: 32px;
        }

        /* Utilities */
        .flex { display: flex; }
        .flex-col { flex-direction: column; }
        .items-center { align-items: center; }
        .items-start { align-items: flex-start; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .gap-4 { gap: 16px; }
        .gap-6 { gap: 24px; }
        .gap-8 { gap: 32px; }
        .mb-2 { margin-bottom: 8px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mb-8 { margin-bottom: 32px; }
        .mt-4 { margin-top: 16px; }
        .mt-6 { margin-top: 24px; }
        .mt-8 { margin-top: 32px; }
        .p-4 { padding: 16px; }
        .p-6 { padding: 24px; }
        .px-4 { padding-left: 16px; padding-right: 16px; }
        .py-2 { padding-top: 8px; padding-bottom: 8px; }
        .py-4 { padding-top: 16px; padding-bottom: 16px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-sm { font-size: 14px; }
        .text-xs { font-size: 12px; }
        .text-lg { font-size: 18px; }
        .text-xl { font-size: 20px; }
        .text-2xl { font-size: 24px; }
        .text-3xl { font-size: 30px; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .text-slate-400 { color: var(--slate-400); }
        .text-slate-500 { color: var(--slate-500); }
        .text-slate-600 { color: var(--slate-600); }
        .text-slate-700 { color: var(--slate-700); }
        .text-slate-800 { color: var(--slate-800); }
        .text-green-500 { color: #22c55e; }
        .text-red-500 { color: #ef4444; }
        .w-full { width: 100%; }
        .rounded-full { border-radius: 9999px; }
        .rounded-2xl { border-radius: 16px; }
        .rounded-3xl { border-radius: 24px; }
        .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .space-y-2 > * + * { margin-top: 8px; }
        .space-y-4 > * + * { margin-top: 16px; }
        .space-y-6 > * + * { margin-top: 24px; }
        .space-y-8 > * + * { margin-top: 32px; }

        /* Mobile Styles */
        @media (max-width: 1024px) {
            .sidebar {
                display: none;
            }
            .main-content {
                margin-left: 0;
                padding-bottom: 100px;
            }
            .main-header {
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 12px;
            }
            .main-header-title {
                font-size: 20px;
            }
            .main-header-actions {
                gap: 8px;
                width: 100%;
                justify-content: flex-end;
            }
            .search-box {
                flex: 1;
                max-width: none;
            }
            .search-box input {
                width: 100%;
                min-width: 0;
            }
            .main-body {
                padding: 0 16px;
            }
            .grid-cols-4 { grid-template-columns: repeat(2, 1fr); gap: 16px; }
            .grid-cols-3 { grid-template-columns: repeat(1, 1fr); }
            .grid-cols-2 { grid-template-columns: 1fr; }
            .dashboard-split-grid { grid-template-columns: 1fr; }
            .schedule-split-grid { grid-template-columns: 1fr; }
            .card {
                padding: 20px;
                border-radius: 24px;
            }
            .stat-card {
                padding: 20px;
            }
            .table-wrapper {
                overflow-x: auto;
                border-radius: 24px;
            }
            .data-table {
                min-width: 600px;
            }
            .user-dropdown-info {
                display: none;
            }
            .user-dropdown-btn {
                padding: 4px;
            }
        }

        @media (max-width: 640px) {
            .grid-cols-4 { grid-template-columns: 1fr; }
            .main-header {
                padding: 12px;
            }
            .main-body { padding: 0 12px; }
            .header-icon-btn {
                width: 36px;
                height: 36px;
            }
            .user-dropdown-avatar {
                width: 32px;
                height: 32px;
            }
            .mobile-nav {
                left: 12px;
                right: 12px;
                bottom: 12px;
                height: 60px;
            }
            .mobile-nav-item span {
                font-size: 9px;
            }
        }

        /* Mobile Nav */
        .mobile-nav {
            display: none;
            position: fixed;
            bottom: 24px;
            left: 24px;
            right: 24px;
            height: 64px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 9999px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.1), 0 0 0 1px rgba(255,255,255,0.5) inset;
            border: 1px solid rgba(226, 232, 240, 0.6);
            z-index: 50;
            padding: 0 16px;
            align-items: center;
            justify-content: space-around;
        }

        @media (max-width: 1024px) {
            .mobile-nav {
                display: flex;
            }
        }

        .mobile-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 4px;
            flex: 1;
            padding: 8px;
            text-decoration: none;
            color: var(--slate-400);
        }

        .mobile-nav-item.active {
            color: var(--primary-dark);
        }

        .mobile-nav-item svg {
            width: 20px;
            height: 20px;
        }

        .mobile-nav-item span {
            font-size: 10px;
            font-weight: 500;
        }

        /* Alert */
        .alert {
            padding: 16px 24px;
            border-radius: 24px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .alert-warning {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
        }

        /* Avatar */
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 16px;
            object-fit: cover;
        }

        .avatar-sm {
            width: 32px;
            height: 32px;
            border-radius: 12px;
        }

        .avatar-lg {
            width: 56px;
            height: 56px;
            border-radius: 20px;
        }

        .avatar-xl {
            width: 80px;
            height: 80px;
            border-radius: 28px;
        }

        /* Status Indicator */
        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            position: absolute;
            bottom: -2px;
            right: -2px;
        }

        .status-indicator.online { background: #22c55e; }
        .status-indicator.away { background: #eab308; }
        .status-indicator.offline { background: #ef4444; }

        /* Pagination */
        .pagination {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .pagination-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: white;
            border: 1px solid var(--slate-100);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: var(--slate-400);
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .pagination-btn:hover {
            background: #dcfce7;
        }

        .pagination-btn.active {
            background: var(--primary);
            color: var(--slate-900);
            border-color: var(--primary);
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar no-scrollbar">
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <span>F</span>
            </div>
            <span class="sidebar-logo-text">FreshHR</span>
        </div>

         <nav class="sidebar-nav">
            {{-- Menu Utama --}}
            <div class="sidebar-group open" data-group="main">
                <div class="sidebar-group-header {{ request()->routeIs(['dashboard', 'attendance.*', 'schedule.*', 'leave.*']) ? 'active' : '' }}" onclick="toggleSidebarGroup(this)">
                    <div class="sidebar-group-left">
                        <i data-lucide="layout-grid"></i>
                        <span>Menu Utama</span>
                    </div>
                    <div class="sidebar-group-toggle">
                        <i data-lucide="chevron-down"></i>
                    </div>
                </div>
                <div class="sidebar-group-content">
                    <div class="sidebar-group-items">
                        <a href="{{ route('dashboard') }}" class="sidebar-group-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <i data-lucide="layout-dashboard"></i>
                            <span>Dashboard</span>
                        </a>
                        <a href="{{ route('attendance.index') }}" class="sidebar-group-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
                            <i data-lucide="map-pin"></i>
                            <span>Absensi</span>
                        </a>
                        <a href="{{ route('schedule.index') }}" class="sidebar-group-item {{ request()->routeIs('schedule.*') ? 'active' : '' }}">
                            <i data-lucide="calendar"></i>
                            <span>Jadwal</span>
                        </a>
                        <a href="{{ route('leave.index') }}" class="sidebar-group-item {{ request()->routeIs('leave.*') ? 'active' : '' }}">
                            <i data-lucide="file-text"></i>
                            <span>Cuti</span>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Laporan --}}
            @if(in_array(auth()->user()?->role, ['dept_head', 'hr_admin', 'super_admin']))
            <div class="sidebar-group {{ request()->routeIs('reports.*') ? 'open' : '' }}" data-group="reports">
                <div class="sidebar-group-header {{ request()->routeIs('reports.*') ? 'active' : '' }}" onclick="toggleSidebarGroup(this)">
                    <div class="sidebar-group-left">
                        <i data-lucide="bar-chart-2"></i>
                        <span>Laporan</span>
                    </div>
                    <div class="sidebar-group-toggle">
                        <i data-lucide="chevron-down"></i>
                    </div>
                </div>
                <div class="sidebar-group-content">
                    <div class="sidebar-group-items">
                        <a href="{{ route('reports.index') }}" class="sidebar-group-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                            <i data-lucide="bar-chart-3"></i>
                            <span>Ringkasan</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Manajemen HR --}}
            @if(in_array(auth()->user()?->role, ['hr_admin', 'super_admin']))
            <div class="sidebar-group {{ request()->routeIs(['management.employees.*', 'management.positions.*']) ? 'open' : '' }}" data-group="management">
                <div class="sidebar-group-header {{ request()->routeIs(['management.employees.*', 'management.positions.*']) ? 'active' : '' }}" onclick="toggleSidebarGroup(this)">
                    <div class="sidebar-group-left">
                        <i data-lucide="users"></i>
                        <span>Manajemen HR</span>
                    </div>
                    <div class="sidebar-group-toggle">
                        <i data-lucide="chevron-down"></i>
                    </div>
                </div>
                <div class="sidebar-group-content">
                    <div class="sidebar-group-items">
                        <a href="{{ route('management.employees.index') }}" class="sidebar-group-item {{ request()->routeIs('management.employees.*') ? 'active' : '' }}">
                            <i data-lucide="user-check"></i>
                            <span>Karyawan</span>
                        </a>
                        <a href="{{ route('management.positions.index') }}" class="sidebar-group-item {{ request()->routeIs('management.positions.*') ? 'active' : '' }}">
                            <i data-lucide="briefcase"></i>
                            <span>Jabatan</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif

            {{-- Admin Sistem --}}
            @if(auth()->user()?->role === 'super_admin')
            <div class="sidebar-group {{ request()->routeIs(['management.users.*', 'management.office.*', 'management.audit_logs.*']) ? 'open' : '' }}" data-group="admin">
                <div class="sidebar-group-header {{ request()->routeIs(['management.users.*', 'management.office.*', 'management.audit_logs.*']) ? 'active' : '' }}" onclick="toggleSidebarGroup(this)">
                    <div class="sidebar-group-left">
                        <i data-lucide="shield"></i>
                        <span>Admin Sistem</span>
                    </div>
                    <div class="sidebar-group-toggle">
                        <i data-lucide="chevron-down"></i>
                    </div>
                </div>
                <div class="sidebar-group-content">
                    <div class="sidebar-group-items">
                        <a href="{{ route('management.users.index') }}" class="sidebar-group-item {{ request()->routeIs('management.users.*') ? 'active' : '' }}">
                            <i data-lucide="shield-check"></i>
                            <span>Pengguna</span>
                        </a>
                        <a href="{{ route('management.office.index') }}" class="sidebar-group-item {{ request()->routeIs('management.office.*') ? 'active' : '' }}">
                            <i data-lucide="building-2"></i>
                            <span>Kantor</span>
                        </a>
                        <a href="{{ route('management.audit_logs.index') }}" class="sidebar-group-item {{ request()->routeIs('management.audit_logs.*') ? 'active' : '' }}">
                            <i data-lucide="clipboard-list"></i>
                            <span>Audit Log</span>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </nav>

        <!-- Sidebar footer removed - user moved to topbar -->
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <header class="main-header">
            <h1 class="main-header-title">@yield('header-title', 'Dashboard')</h1>
            <div class="main-header-actions">
                <div class="search-box">
                    <i data-lucide="search"></i>
                    <input type="text" placeholder="Cari apa saja...">
                </div>
                <button class="header-icon-btn">
                    <i data-lucide="bell"></i>
                </button>

                <!-- User Dropdown -->
                <div class="user-dropdown" id="userDropdown">
                    <button class="user-dropdown-btn" onclick="toggleUserDropdown()">
                        <div class="user-dropdown-info">
                            <div class="user-dropdown-name">{{ auth()->user()?->name ?? 'Guest' }}</div>
                            <div class="user-dropdown-role">{{ ucwords(str_replace('_', ' ', auth()->user()?->role ?? 'User')) }}</div>
                        </div>
                        <img src="{{ auth()->user()?->employee?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()?->name ?? 'User') . '&background=4ade80&color=1e293b' }}" alt="Avatar" class="user-dropdown-avatar">
                    </button>

                    <div class="user-dropdown-menu">
                        <a href="{{ route('profile.edit') }}" class="user-dropdown-item">
                            <i data-lucide="user"></i>
                            Edit Profil
                        </a>
                        <div class="user-dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="user-dropdown-item danger">
                                <i data-lucide="log-out"></i>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <div class="main-body">
            @if(session('success'))
                <div class="alert alert-success animate-fade-in">
                    <i data-lucide="check-circle-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-error animate-fade-in">
                    <i data-lucide="alert-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- Mobile Nav -->
    <nav class="mobile-nav">
        <a href="{{ route('dashboard') }}" class="mobile-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('attendance.index') }}" class="mobile-nav-item {{ request()->routeIs('attendance.*') ? 'active' : '' }}">
            <i data-lucide="map-pin"></i>
            <span>Absensi</span>
        </a>
        <a href="{{ route('leave.index') }}" class="mobile-nav-item {{ request()->routeIs('leave.*') ? 'active' : '' }}">
            <i data-lucide="file-text"></i>
            <span>Cuti</span>
        </a>
        @if(in_array(auth()->user()?->role, ['dept_head', 'hr_admin', 'super_admin']))
        <a href="{{ route('reports.index') }}" class="mobile-nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <i data-lucide="bar-chart-3"></i>
            <span>Laporan</span>
        </a>
        @else
        <a href="{{ route('schedule.index') }}" class="mobile-nav-item {{ request()->routeIs('schedule.*') ? 'active' : '' }}">
            <i data-lucide="calendar"></i>
            <span>Jadwal</span>
        </a>
        @endif
    </nav>

    <script>
        // Initialize Lucide icons
        lucide.createIcons();

        // User Dropdown Toggle
        function toggleUserDropdown() {
            const dropdown = document.getElementById('userDropdown');
            dropdown.classList.toggle('active');
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            if (dropdown && !dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });

        // Sidebar Group Toggle
        function toggleSidebarGroup(header) {
            const group = header.closest('.sidebar-group');
            const isOpen = group.classList.contains('open');

            // Close all other groups (optional - for accordion behavior)
            // document.querySelectorAll('.sidebar-group').forEach(g => g.classList.remove('open'));

            // Toggle current group
            if (isOpen) {
                group.classList.remove('open');
            } else {
                group.classList.add('open');
            }

            // Re-initialize icons for the group
            lucide.createIcons();
        }

        // Auto-open group if it has active item
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.sidebar-group').forEach(group => {
                const hasActiveItem = group.querySelector('.sidebar-group-item.active');
                const hasActiveHeader = group.querySelector('.sidebar-group-header.active');
                if (hasActiveItem || hasActiveHeader) {
                    group.classList.add('open');
                }
            });
        });

        // Sticky Header Scroll Effect
        const mainHeader = document.querySelector('.main-header');
        let lastScrollY = 0;

        window.addEventListener('scroll', function() {
            const currentScrollY = window.scrollY;

            if (currentScrollY > 10) {
                mainHeader.classList.add('main-header-scrolled');
            } else {
                mainHeader.classList.remove('main-header-scrolled');
            }

            lastScrollY = currentScrollY;
        }, { passive: true });
    </script>
    @stack('scripts')
</body>
</html>
