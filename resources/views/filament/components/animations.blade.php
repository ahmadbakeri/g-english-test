<style>
    /* 1. Animasi Transisi Tema (Light <-> Dark) */
    html, body, .fi-topbar, .fi-sidebar, .fi-main {
        transition: background-color 0.4s ease-in-out, color 0.4s ease-in-out, border-color 0.4s ease-in-out, box-shadow 0.4s ease-in-out;
    }

    /* 2. Animasi Transisi Halaman (View Transitions API) */
    @keyframes fade-in {
        from { opacity: 0; }
    }

    @keyframes fade-out {
        to { opacity: 0; }
    }

    /* Genza Design System Overrides */
    .fi-btn {
        transition: all 0.3s ease-in-out !important;
        border-radius: 9999px !important; /* Pill shape for buttons */
    }
    .fi-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px -1px rgba(10, 66, 130, 0.2), 0 2px 4px -1px rgba(10, 66, 130, 0.1) !important;
    }
    
    .fi-ta-record, .fi-wi-stats-overview-stat {
        border-radius: 1rem !important; /* rounded-2xl */
        transition: all 0.3s ease-in-out !important;
    }
    .fi-ta-record:hover, .fi-wi-stats-overview-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04) !important;
    }
    
    .dark .fi-ta-record:hover, .dark .fi-wi-stats-overview-stat:hover {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -2px rgba(0, 0, 0, 0.2) !important;
    }

    @keyframes slide-from-right {
        from { transform: translateX(30px); opacity: 0; }
    }

    @keyframes slide-to-left {
        to { transform: translateX(-30px); opacity: 0; }
    }



    /* Overlay Fullscreen untuk Ganti Bahasa */
    #lang-switch-overlay {
        position: fixed;
        inset: 0;
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(12px) saturate(150%);
        -webkit-backdrop-filter: blur(12px) saturate(150%);
        z-index: 999999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.4s ease-out;
    }
    
    .dark #lang-switch-overlay {
        background: rgba(15, 23, 42, 0.7);
    }

    #lang-switch-overlay.active {
        opacity: 1;
        pointer-events: all;
    }

    /* Elegant Text */
    .lang-text-elegant {
        font-size: 1.1rem;
        font-weight: 500;
        color: #475569;
        letter-spacing: 0.05em;
        margin-top: 1.5rem;
        animation: pulse-soft 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    .dark .lang-text-elegant {
        color: #cbd5e1;
    }

    @keyframes pulse-soft {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.7; transform: scale(0.98); }
    }
    
    .spinner-elegant {
        width: 3.5rem;
        height: 3.5rem;
        border-radius: 9999px;
        border: 3px solid rgba(var(--primary-500), 0.2);
        border-top-color: rgb(var(--primary-500));
        animation: spin 1s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    }
    
    /* Colorful Sidebar Icons */
    .fi-sidebar-item-active .fi-sidebar-item-icon {
        color: rgb(var(--primary-600)) !important;
        filter: drop-shadow(0 0 6px rgba(var(--primary-600), 0.5));
    }
    
    /* Specific Colors based on URL - Colorful & Aesthetic */
    
    /* Dashboard */
    a[href$="/admin"] .fi-sidebar-item-icon { color: #3b82f6; filter: drop-shadow(0 0 4px rgba(59, 130, 246, 0.4)); }
    
    /* Shield & Roles */
    a[href*="roles"] .fi-sidebar-item-icon { color: #84cc16; filter: drop-shadow(0 0 4px rgba(132, 204, 22, 0.4)); }

    /* Administration */
    a[href*="admissions"] .fi-sidebar-item-icon { color: #06b6d4; filter: drop-shadow(0 0 4px rgba(6, 182, 212, 0.4)); }
    a[href*="students"] .fi-sidebar-item-icon { color: #10b981; filter: drop-shadow(0 0 4px rgba(16, 185, 129, 0.4)); }
    a[href*="tutors"] .fi-sidebar-item-icon { color: #8b5cf6; filter: drop-shadow(0 0 4px rgba(139, 92, 246, 0.4)); }
    a[href*="users"] .fi-sidebar-item-icon, a[href*="employees"] .fi-sidebar-item-icon { color: #6366f1; filter: drop-shadow(0 0 4px rgba(99, 102, 241, 0.4)); }

    /* Academic & Daily */
    a[href*="scan-attendance"] .fi-sidebar-item-icon { color: #f43f5e; filter: drop-shadow(0 0 4px rgba(244, 63, 94, 0.4)); }
    a[href*="display-qr"] .fi-sidebar-item-icon { color: #f97316; filter: drop-shadow(0 0 4px rgba(249, 115, 22, 0.4)); }
    a[href*="class-schedules"] .fi-sidebar-item-icon { color: #f59e0b; filter: drop-shadow(0 0 4px rgba(245, 158, 11, 0.4)); }
    a[href*="attendances"] .fi-sidebar-item-icon { color: #0ea5e9; filter: drop-shadow(0 0 4px rgba(14, 165, 233, 0.4)); }
    a[href*="journals"] .fi-sidebar-item-icon { color: #38bdf8; filter: drop-shadow(0 0 4px rgba(56, 189, 248, 0.4)); }
    a[href*="journal-validations"] .fi-sidebar-item-icon { color: #d946ef; filter: drop-shadow(0 0 4px rgba(217, 70, 239, 0.4)); }
    
    /* Evaluation & Reports */
    a[href*="evaluations"] .fi-sidebar-item-icon { color: #ec4899; filter: drop-shadow(0 0 4px rgba(236, 72, 153, 0.4)); }
    a[href*="attendance-report"] .fi-sidebar-item-icon { color: #14b8a6; filter: drop-shadow(0 0 4px rgba(20, 184, 166, 0.4)); }
    a[href*="student-report"] .fi-sidebar-item-icon { color: #818cf8; filter: drop-shadow(0 0 4px rgba(129, 140, 248, 0.4)); }
    a[href*="financial-report"] .fi-sidebar-item-icon { color: #eab308; filter: drop-shadow(0 0 4px rgba(234, 179, 8, 0.4)); }

    /* Finance */
    a[href*="expenses"] .fi-sidebar-item-icon { color: #ef4444; filter: drop-shadow(0 0 4px rgba(239, 68, 68, 0.4)); }
    a[href*="honors"] .fi-sidebar-item-icon { color: #d946ef; filter: drop-shadow(0 0 4px rgba(217, 70, 239, 0.4)); }
    a[href*="student-payments"] .fi-sidebar-item-icon { color: #10b981; filter: drop-shadow(0 0 4px rgba(16, 185, 129, 0.4)); }
    a[href*="tutor-salaries"] .fi-sidebar-item-icon { color: #8b5cf6; filter: drop-shadow(0 0 4px rgba(139, 92, 246, 0.4)); }

    /* Master Data & System */
    a[href*="academic-years"] .fi-sidebar-item-icon { color: #f59e0b; filter: drop-shadow(0 0 4px rgba(245, 158, 11, 0.4)); }
    a[href*="semesters"] .fi-sidebar-item-icon { color: #f97316; filter: drop-shadow(0 0 4px rgba(249, 115, 22, 0.4)); }
    a[href*="class-rooms"] .fi-sidebar-item-icon { color: #6366f1; filter: drop-shadow(0 0 4px rgba(99, 102, 241, 0.4)); }
    a[href*="subjects"] .fi-sidebar-item-icon { color: #14b8a6; filter: drop-shadow(0 0 4px rgba(20, 184, 166, 0.4)); }
    a[href*="schedules"] .fi-sidebar-item-icon { color: #3b82f6; filter: drop-shadow(0 0 4px rgba(59, 130, 246, 0.4)); }
    a[href*="settings"] .fi-sidebar-item-icon { color: #64748b; filter: drop-shadow(0 0 4px rgba(100, 116, 139, 0.4)); }
    
    /* Global glow effect for all sidebar icons */
    .fi-sidebar-item-icon {
        transition: all 0.3s ease;
    }
    .fi-sidebar-item:hover .fi-sidebar-item-icon {
        transform: scale(1.1);
        filter: drop-shadow(0 0 4px currentColor);
    }
    
    /* ---------------------------------------------------- */
    /* CUSTOM QUICK LINKS STYLES (To prevent Tailwind Purge)*/
    /* ---------------------------------------------------- */
    .ql-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 1rem;
    }
    .ql-card {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 1rem;
        border: 1px solid rgba(0,0,0,0.05);
        background-color: #f8fafc;
        transition: all 0.3s ease;
        text-decoration: none !important;
    }
    .dark .ql-card {
        background-color: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.1);
    }
    .ql-card:hover {
        background-color: #ffffff;
        transform: translateY(-4px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
    .dark .ql-card:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }
    
    /* Icon Box */
    .ql-icon-box {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 0.75rem;
        flex-shrink: 0;
        color: white;
        transition: transform 0.3s ease;
    }
    .ql-card:hover .ql-icon-box {
        transform: scale(1.1) rotate(3deg);
    }
    
    /* Gradient Colors & Glows */
    .ql-emerald { background: linear-gradient(to bottom right, #34d399, #059669); box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.39); }
    .ql-card:hover .ql-emerald { box-shadow: 0 6px 20px rgba(16, 185, 129, 0.6); }

    .ql-violet { background: linear-gradient(to bottom right, #a78bfa, #7c3aed); box-shadow: 0 4px 14px 0 rgba(139, 92, 246, 0.39); }
    .ql-card:hover .ql-violet { box-shadow: 0 6px 20px rgba(139, 92, 246, 0.6); }

    .ql-amber { background: linear-gradient(to bottom right, #fbbf24, #d97706); box-shadow: 0 4px 14px 0 rgba(245, 158, 11, 0.39); }
    .ql-card:hover .ql-amber { box-shadow: 0 6px 20px rgba(245, 158, 11, 0.6); }

    .ql-sky { background: linear-gradient(to bottom right, #38bdf8, #0284c7); box-shadow: 0 4px 14px 0 rgba(14, 165, 233, 0.39); }
    .ql-card:hover .ql-sky { box-shadow: 0 6px 20px rgba(14, 165, 233, 0.6); }

    .ql-rose { background: linear-gradient(to bottom right, #fb7185, #e11d48); box-shadow: 0 4px 14px 0 rgba(244, 63, 94, 0.39); }
    .ql-card:hover .ql-rose { box-shadow: 0 6px 20px rgba(244, 63, 94, 0.6); }

    .ql-indigo { background: linear-gradient(to bottom right, #818cf8, #4f46e5); box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.39); }
    .ql-card:hover .ql-indigo { box-shadow: 0 6px 20px rgba(99, 102, 241, 0.6); }

    .ql-teal { background: linear-gradient(to bottom right, #2dd4bf, #0d9488); box-shadow: 0 4px 14px 0 rgba(20, 184, 166, 0.39); }
    .ql-card:hover .ql-teal { box-shadow: 0 6px 20px rgba(20, 184, 166, 0.6); }

    .ql-orange { background: linear-gradient(to bottom right, #fb923c, #ea580c); box-shadow: 0 4px 14px 0 rgba(249, 115, 22, 0.39); }
    .ql-card:hover .ql-orange { box-shadow: 0 6px 20px rgba(249, 115, 22, 0.6); }

    .ql-slate { background: linear-gradient(to bottom right, #94a3b8, #475569); box-shadow: 0 4px 14px 0 rgba(100, 116, 139, 0.39); }
    .ql-card:hover .ql-slate { box-shadow: 0 6px 20px rgba(100, 116, 139, 0.6); }

    /* Text Label */
    .ql-label {
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
        transition: color 0.3s ease;
    }
    .dark .ql-label {
        color: #e2e8f0;
    }
    
    .ql-card:hover .ql-label { color: #0a4282; }
    .dark .ql-card:hover .ql-label { color: #fbb900; }

    /* ---------------------------------------------------- */
    /* CUSTOM TOPBAR STYLES (Icons & Avatar)                */
    /* ---------------------------------------------------- */
    
    /* Aesthetic Icon Buttons (Notifications, Dark Mode, etc) */
    .fi-topbar .fi-icon-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        background-color: rgba(255, 255, 255, 0.6) !important;
        border: 1px solid rgba(0, 0, 0, 0.05) !important;
        backdrop-filter: blur(8px) !important;
    }
    .dark .fi-topbar .fi-icon-btn {
        background-color: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .fi-topbar .fi-icon-btn:hover {
        transform: translateY(-2px) scale(1.05) !important;
        background-color: #ffffff !important;
        box-shadow: 0 4px 15px -2px rgba(var(--primary-600), 0.2) !important;
        color: rgb(var(--primary-600)) !important;
        border-color: rgba(var(--primary-600), 0.3) !important;
    }
    .dark .fi-topbar .fi-icon-btn:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 4px 15px -2px rgba(var(--primary-500), 0.4) !important;
        color: rgb(var(--primary-400)) !important;
        border-color: rgba(var(--primary-500), 0.5) !important;
    }

    /* Target the language switcher if it doesn't use fi-icon-btn */
    .fi-topbar button[title*="Lang"], 
    .fi-topbar button[title*="Bahasa"], 
    .fi-topbar button[title*="Language"],
    .fi-topbar .fi-dropdown-trigger {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .fi-topbar .fi-dropdown-trigger:hover {
        transform: translateY(-2px) !important;
        filter: drop-shadow(0 4px 8px rgba(var(--primary-600), 0.3));
    }

    /* Aesthetic User Avatar (Avoid targeting the site Logo) */
    .fi-topbar .fi-avatar,
    .fi-topbar button img,
    .fi-topbar [class*="avatar"] {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: 2px solid transparent !important;
        box-shadow: 0 2px 8px -2px rgba(0, 0, 0, 0.1) !important;
    }
    .fi-topbar .fi-avatar:hover,
    .fi-topbar button img:hover,
    .fi-topbar [class*="avatar"]:hover {
        transform: scale(1.1) rotate(5deg) !important;
        border-color: rgb(var(--primary-500)) !important;
        box-shadow: 0 6px 15px rgba(var(--primary-600), 0.4) !important;
    }

    /* ---------------------------------------------------- */
    /* CUSTOM STATS OVERVIEW STYLES                         */
    /* ---------------------------------------------------- */
    .stat-card {
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border-radius: 1.25rem !important;
        overflow: hidden;
        position: relative;
        border: 1px solid rgba(0,0,0,0.05) !important;
        background: #ffffff !important;
    }
    .dark .stat-card {
        border: 1px solid rgba(255,255,255,0.08) !important;
        background: rgba(255,255,255,0.03) !important;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px -5px rgba(0,0,0,0.1) !important;
    }
    .dark .stat-card:hover {
        box-shadow: 0 15px 30px -5px rgba(0,0,0,0.4) !important;
    }

    /* Soft Gradient Backgrounds for Stats */
    .stat-emerald { background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(255, 255, 255, 0)) !important; }
    .stat-emerald:hover { box-shadow: 0 12px 25px -5px rgba(16, 185, 129, 0.25) !important; border-color: rgba(16, 185, 129, 0.3) !important; }
    .dark .stat-emerald { background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(255, 255, 255, 0.02)) !important; }

    .stat-sky { background: linear-gradient(135deg, rgba(14, 165, 233, 0.08), rgba(255, 255, 255, 0)) !important; }
    .stat-sky:hover { box-shadow: 0 12px 25px -5px rgba(14, 165, 233, 0.25) !important; border-color: rgba(14, 165, 233, 0.3) !important; }
    .dark .stat-sky { background: linear-gradient(135deg, rgba(14, 165, 233, 0.15), rgba(255, 255, 255, 0.02)) !important; }

    .stat-amber { background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(255, 255, 255, 0)) !important; }
    .stat-amber:hover { box-shadow: 0 12px 25px -5px rgba(245, 158, 11, 0.25) !important; border-color: rgba(245, 158, 11, 0.3) !important; }
    .dark .stat-amber { background: linear-gradient(135deg, rgba(245, 158, 11, 0.15), rgba(255, 255, 255, 0.02)) !important; }

    .stat-rose { background: linear-gradient(135deg, rgba(244, 63, 94, 0.08), rgba(255, 255, 255, 0)) !important; }
    .stat-rose:hover { box-shadow: 0 12px 25px -5px rgba(244, 63, 94, 0.25) !important; border-color: rgba(244, 63, 94, 0.3) !important; }
    .dark .stat-rose { background: linear-gradient(135deg, rgba(244, 63, 94, 0.15), rgba(255, 255, 255, 0.02)) !important; }

    .stat-indigo { background: linear-gradient(135deg, rgba(99, 102, 241, 0.08), rgba(255, 255, 255, 0)) !important; }
    .stat-indigo:hover { box-shadow: 0 12px 25px -5px rgba(99, 102, 241, 0.25) !important; border-color: rgba(99, 102, 241, 0.3) !important; }
    .dark .stat-indigo { background: linear-gradient(135deg, rgba(99, 102, 241, 0.15), rgba(255, 255, 255, 0.02)) !important; }

    /* Animated Glow on the Chart inside Stats */
    .stat-card:hover canvas {
        filter: drop-shadow(0 4px 6px currentColor);
        transform: scaleY(1.05);
        transition: all 0.3s ease;
    }
    /* ---------------------------------------------------- */
    /* GENZA GLOBAL UI OVERHAUL (AESTHETIC & PREMIUM)       */
    /* ---------------------------------------------------- */

    /* 1. Global Forms & Inputs */
    .fi-input, .fi-select, .fi-textarea {
        border-radius: 0.75rem !important; /* rounded-xl */
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border: 1px solid rgba(0,0,0,0.1) !important;
        background-color: #f8fafc !important;
        box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.02) !important;
    }
    
    .dark .fi-input, .dark .fi-select, .dark .fi-textarea {
        background-color: rgba(15, 23, 42, 0.6) !important;
        border-color: rgba(255,255,255,0.1) !important;
    }

    .fi-input:focus, .fi-select:focus, .fi-textarea:focus,
    .fi-input:focus-within, .fi-select:focus-within, .fi-textarea:focus-within {
        border-color: rgb(var(--primary-500)) !important;
        box-shadow: 0 0 0 4px rgba(var(--primary-500), 0.15) !important;
        background-color: #ffffff !important;
        transform: translateY(-1px);
    }
    
    .dark .fi-input:focus, .dark .fi-select:focus, .dark .fi-textarea:focus,
    .dark .fi-input:focus-within, .dark .fi-select:focus-within, .dark .fi-textarea:focus-within {
        background-color: rgba(15, 23, 42, 0.9) !important;
        box-shadow: 0 0 0 4px rgba(var(--primary-400), 0.2) !important;
    }

    /* Input wrappers */
    .fi-fo-field-wrp {
        transition: all 0.3s ease;
    }
    
    /* Checkbox & Radio */
    .fi-checkbox, .fi-radio {
        border-radius: 0.375rem !important;
        transition: all 0.2s ease !important;
    }
    .fi-checkbox:checked, .fi-radio:checked {
        transform: scale(1.1);
        box-shadow: 0 0 10px rgba(var(--primary-500), 0.4) !important;
    }

    /* 2. Global Tables & Data */
    .fi-ta-table {
        border-collapse: separate !important;
        border-spacing: 0 0.5rem !important;
        background: transparent !important;
    }
    
    .fi-ta-header {
        border-bottom: none !important;
    }

    /* Table Headers */
    .fi-ta-table th {
        background-color: rgba(var(--primary-50), 0.5) !important;
        border-bottom: 2px solid rgba(var(--primary-200), 0.5) !important;
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        font-size: 0.75rem !important;
        color: rgb(var(--primary-700)) !important;
    }
    
    .dark .fi-ta-table th {
        background-color: rgba(15, 23, 42, 0.5) !important;
        border-bottom-color: rgba(255, 255, 255, 0.05) !important;
        color: rgb(var(--primary-400)) !important;
    }

    /* Table Rows (Floating Rows effect) */
    .fi-ta-row {
        background-color: #ffffff !important;
        border-radius: 1rem !important;
        box-shadow: 0 2px 5px -1px rgba(0, 0, 0, 0.03) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    
    .dark .fi-ta-row {
        background-color: rgba(255, 255, 255, 0.03) !important;
        box-shadow: none !important;
    }

    .fi-ta-row:hover {
        transform: translateY(-2px) scale(1.001) !important;
        box-shadow: 0 10px 15px -3px rgba(var(--primary-500), 0.1), 0 4px 6px -2px rgba(var(--primary-500), 0.05) !important;
        z-index: 10;
        position: relative;
    }
    
    .dark .fi-ta-row:hover {
        background-color: rgba(255, 255, 255, 0.06) !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5) !important;
    }

    /* Table Cells */
    .fi-ta-cell {
        border-top: 1px solid rgba(0, 0, 0, 0.02) !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.02) !important;
    }
    
    .dark .fi-ta-cell {
        border-color: rgba(255, 255, 255, 0.02) !important;
    }
    
    .fi-ta-row td:first-child {
        border-left: 1px solid rgba(0, 0, 0, 0.02) !important;
        border-top-left-radius: 1rem !important;
        border-bottom-left-radius: 1rem !important;
    }
    .fi-ta-row td:last-child {
        border-right: 1px solid rgba(0, 0, 0, 0.02) !important;
        border-top-right-radius: 1rem !important;
        border-bottom-right-radius: 1rem !important;
    }

    /* Remove default ugly borders from the table container */
    .fi-ta-content {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
    }

    /* 3. Global Sections & Cards */
    .fi-section, .fi-fo-section, .fi-in-section, .fi-ta-record, .fi-modal-window {
        border-radius: 1.25rem !important; /* rounded-2xl */
        border: 1px solid rgba(0,0,0,0.05) !important;
        background: rgba(255, 255, 255, 0.85) !important;
        backdrop-filter: blur(12px) saturate(180%) !important;
        -webkit-backdrop-filter: blur(12px) saturate(180%) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.03), 0 8px 10px -6px rgba(0, 0, 0, 0.01) !important;
        transition: all 0.3s ease-in-out !important;
    }

    .dark .fi-section, .dark .fi-fo-section, .dark .fi-in-section, .dark .fi-ta-record, .dark .fi-modal-window {
        background: rgba(15, 23, 42, 0.75) !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5) !important;
    }

    /* 4. Global Tabs */
    .fi-tabs {
        background-color: rgba(0, 0, 0, 0.03) !important;
        border-radius: 1rem !important;
        padding: 0.375rem !important;
        border: none !important;
        gap: 0.25rem !important;
    }
    
    .dark .fi-tabs {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    .fi-tabs-item {
        border-radius: 0.75rem !important;
        transition: all 0.3s ease !important;
        padding: 0.5rem 1rem !important;
    }
    
    .fi-tabs-item:hover {
        background-color: rgba(0, 0, 0, 0.05) !important;
    }
    
    .dark .fi-tabs-item:hover {
        background-color: rgba(255, 255, 255, 0.05) !important;
    }

    .fi-tabs-item.fi-active {
        background-color: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        color: rgb(var(--primary-600)) !important;
    }
    
    .dark .fi-tabs-item.fi-active {
        background-color: rgba(15, 23, 42, 1) !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3) !important;
        color: rgb(var(--primary-400)) !important;
    }

    /* 5. Pagination Buttons */
    .fi-pagination-item-btn {
        border-radius: 9999px !important; /* Circle shape */
        transition: all 0.3s ease !important;
    }
    .fi-pagination-item-btn:hover {
        background-color: rgba(var(--primary-500), 0.1) !important;
        color: rgb(var(--primary-600)) !important;
    }
    
    /* 6. Badges */
    .fi-badge {
        border-radius: 9999px !important; /* Pill shape */
        padding: 0.25rem 0.75rem !important;
        font-weight: 600 !important;
        letter-spacing: 0.025em !important;
        box-shadow: 0 2px 4px -1px rgba(0,0,0,0.05) !important;
    }

</style>

<div id="lang-switch-overlay">
    <div class="spinner-elegant"></div>
    <div class="lang-text-elegant" id="lang-switch-text">Translating interface...</div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('language-changed', (event) => {
            const locale = event[0]?.locale || event.locale || 'en';
            const overlay = document.getElementById('lang-switch-overlay');
            const text = document.getElementById('lang-switch-text');
            
            // Set text based on target language
            if (locale === 'id') {
                text.innerText = 'Menyiapkan Bahasa Indonesia...';
            } else if (locale === 'zh_CN' || locale === 'zh-CN') {
                text.innerText = '切换到中文...';
            } else if (locale === 'zh_PY' || locale === 'zh-PY') {
                text.innerText = 'Qiēhuàn dào Zhōngwén...';
            } else if (locale === 'ja') {
                text.innerText = '日本語に切り替えています...';
            } else if (locale === 'ja_RM' || locale === 'ja-RM') {
                text.innerText = 'Nihongo ni kirikaete imasu...';
            } else if (locale === 'ar') {
                text.innerText = 'جاري التحضير للغة العربية...';
            } else if (locale === 'ar_RM' || locale === 'ar-RM') {
                text.innerText = 'Jari al-tahdir lil-lughah al-Arabiyyah...';
            } else {
                text.innerText = 'Switching to English...';
            }
            
            // Activate animation
            overlay.classList.add('active');
            
            // Wait for animation to finish, then hard reload to ensure perfect translation
            setTimeout(() => {
                window.location.reload();
            }, 600);
        });
    });
</script>
