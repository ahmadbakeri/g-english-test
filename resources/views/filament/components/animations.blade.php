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
    :root {
        --genza-primary: #0a4282;
        --genza-secondary: #fbb900;
    }

    .fi-btn {
        transition: all 0.3s ease-in-out !important;
        border-radius: 9999px !important; /* Pill shape for buttons */
        font-weight: 600 !important;
    }
    .fi-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px -1px rgba(10, 66, 130, 0.22), 0 2px 4px -1px rgba(10, 66, 130, 0.12) !important;
    }
    /* Primary Submit & Action Button Discipline (#0a4282) */
    .fi-btn-color-primary,
    button[type="submit"].fi-btn-color-primary {
        background-color: #0a4282 !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
    }
    .fi-btn-color-primary:hover {
        background-color: #0d52a0 !important;
        border-color: rgba(251, 185, 0, 0.5) !important;
    }
    .dark .fi-btn-color-primary {
        background-color: #1d4ed8 !important;
        color: #ffffff !important;
    }
    /* Secondary Accent Button Discipline (#fbb900 with high-contrast #0a4282 text, never low-contrast yellow on white) */
    .fi-btn-color-secondary {
        background-color: #fbb900 !important;
        color: #0a4282 !important;
        font-weight: 700 !important;
    }
    .fi-btn-color-secondary:hover {
        background-color: #f59e0b !important;
        color: #0a4282 !important;
    }

    /* Key Headings & Active Navigation Indicators */
    .fi-header-heading {
        color: #0a4282 !important;
        font-weight: 800 !important;
        letter-spacing: -0.02em !important;
    }
    .dark .fi-header-heading {
        color: #f8fafc !important;
    }
    .fi-sidebar-item-active > a {
        border-left: 3px solid #0a4282 !important;
        background: linear-gradient(90deg, rgba(10, 66, 130, 0.08), rgba(10, 66, 130, 0.02)) !important;
    }
    .dark .fi-sidebar-item-active > a {
        border-left: 3px solid #fbb900 !important;
        background: linear-gradient(90deg, rgba(251, 185, 0, 0.12), rgba(255, 255, 255, 0.02)) !important;
    }
    
    .fi-ta-record, .fi-wi-stats-overview-stat {
        border-radius: 1rem !important; /* rounded-2xl */
        transition: box-shadow 0.3s ease-in-out, border-color 0.3s ease-in-out !important;
    }
    .fi-wi-stats-overview-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04) !important;
    }
    .fi-ta-record:hover {
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
    a[href$="/admin"] .fi-sidebar-item-icon,
    a[href$="/school"] .fi-sidebar-item-icon { color: #3b82f6; filter: drop-shadow(0 0 4px rgba(59, 130, 246, 0.4)); }
    a[href*="school-participants"] .fi-sidebar-item-icon { color: #f59e0b; filter: drop-shadow(0 0 4px rgba(245, 158, 11, 0.4)); }
    
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
    .fi-input, .fi-select, .fi-textarea, .fi-input-wrp {
        border-radius: 0.75rem !important; /* rounded-xl */
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    .fi-input, .fi-select, .fi-textarea {
        border: 1px solid rgba(15, 23, 42, 0.12) !important;
        background-color: #f8fafc !important;
        box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.02) !important;
    }
    
    .dark .fi-input, .dark .fi-select, .dark .fi-textarea {
        background-color: rgba(15, 23, 42, 0.65) !important;
        border-color: rgba(255,255,255,0.12) !important;
    }

    .fi-input:focus, .fi-select:focus, .fi-textarea:focus,
    .fi-input:focus-within, .fi-select:focus-within, .fi-textarea:focus-within,
    .fi-input-wrp:focus-within {
        border-color: #0a4282 !important;
        box-shadow: 0 0 0 3px rgba(10, 66, 130, 0.18) !important;
        background-color: #ffffff !important;
        transform: translateY(-1px);
    }
    
    .dark .fi-input:focus, .dark .fi-select:focus, .dark .fi-textarea:focus,
    .dark .fi-input:focus-within, .dark .fi-select:focus-within, .dark .fi-textarea:focus-within,
    .dark .fi-input-wrp:focus-within {
        border-color: #fbb900 !important;
        background-color: rgba(15, 23, 42, 0.92) !important;
        box-shadow: 0 0 0 3px rgba(251, 185, 0, 0.22) !important;
    }

    /* Input wrappers & Helper Text Styling */
    .fi-fo-field-wrp {
        transition: all 0.3s ease;
    }
    .fi-fo-field-wrp-label {
        font-weight: 600 !important;
        color: #1e293b !important;
        letter-spacing: 0.01em !important;
    }
    .dark .fi-fo-field-wrp-label {
        color: #e2e8f0 !important;
    }
    .fi-fo-field-wrp-helper-text {
        font-size: 0.775rem !important;
        line-height: 1.35 !important;
        color: #64748b !important;
        margin-top: 0.25rem !important;
    }
    .dark .fi-fo-field-wrp-helper-text {
        color: #94a3b8 !important;
    }
    
    /* Checkbox & Radio */
    .fi-checkbox, .fi-radio {
        border-radius: 0.375rem !important;
        transition: all 0.2s ease !important;
    }
    .fi-checkbox:checked, .fi-radio:checked {
        transform: scale(1.1);
        box-shadow: 0 0 10px rgba(10, 66, 130, 0.35) !important;
    }

    /* 2. Global Tables & Data Readability & Alignment */
    .fi-ta-table {
        border-collapse: separate !important;
        border-spacing: 0 0.5rem !important;
        background: transparent !important;
        width: 100% !important;
        max-width: 100% !important;
        table-layout: auto !important;
    }
    
    .fi-ta-header {
        border-bottom: none !important;
    }

    /* Table Headers */
    .fi-ta-table thead th {
        background-color: rgba(10, 66, 130, 0.05) !important;
        border-bottom: 2px solid rgba(10, 66, 130, 0.14) !important;
        padding: 0.75rem 0.65rem !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        font-size: 0.75rem !important;
        color: #0a4282 !important;
        vertical-align: middle !important;
    }
    
    .dark .fi-ta-table thead th {
        background-color: rgba(15, 23, 42, 0.65) !important;
        border-bottom-color: rgba(251, 185, 0, 0.25) !important;
        color: #93c5fd !important;
    }

    /* Table Rows (Floating Rows effect - NO transform/backdrop-filter so Floating UI x-anchor coordinates stay exact) */
    .fi-ta-row {
        background-color: #ffffff !important;
        border-radius: 1rem !important;
        box-shadow: 0 2px 5px -1px rgba(15, 23, 42, 0.04) !important;
        transition: background-color 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease !important;
    }
    
    .dark .fi-ta-row {
        background-color: rgba(30, 41, 59, 0.55) !important;
        box-shadow: none !important;
    }

    .fi-ta-row:hover {
        box-shadow: 0 10px 18px -4px rgba(10, 66, 130, 0.12), 0 4px 6px -2px rgba(10, 66, 130, 0.05) !important;
    }
    
    .dark .fi-ta-row:hover {
        background-color: rgba(30, 41, 59, 0.85) !important;
        box-shadow: 0 10px 18px -4px rgba(0, 0, 0, 0.55) !important;
    }

    /* ActionGroup & Global Dropdown Panels: solid surface + z-index: 50, never override position/top/left */
    .fi-dropdown-panel {
        z-index: 50 !important;
        background-color: #ffffff !important;
        border: 1px solid rgba(15, 23, 42, 0.1) !important;
        border-radius: 0.875rem !important;
        box-shadow: 0 15px 30px -5px rgba(15, 23, 42, 0.16), 0 8px 10px -6px rgba(15, 23, 42, 0.08) !important;
    }
    .dark .fi-dropdown-panel {
        background-color: #0f172a !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.65), 0 8px 10px -6px rgba(0, 0, 0, 0.45) !important;
    }

    /* Table Cells: Vertical & Compact Horizontal Padding, Crisp Subtle Borders & Column Alignment */
    .fi-ta-cell,
    .fi-ta-row td {
        padding: 0.55rem 0.5rem !important;
        vertical-align: middle !important;
        border-top: 1px solid rgba(15, 23, 42, 0.05) !important;
        border-bottom: 1px solid rgba(15, 23, 42, 0.05) !important;
        white-space: normal !important;
        overflow-wrap: anywhere !important;
    }
    .fi-ta-cell .fi-ta-col-wrp {
        padding-left: 0.25rem !important;
        padding-right: 0.25rem !important;
        min-width: 0 !important;
    }
    .fi-ta-table thead th {
        white-space: normal !important;
    }
    
    .dark .fi-ta-cell,
    .dark .fi-ta-row td {
        border-top-color: rgba(255, 255, 255, 0.06) !important;
        border-bottom-color: rgba(255, 255, 255, 0.06) !important;
    }
    
    .fi-ta-row td:first-child {
        border-left: 1px solid rgba(15, 23, 42, 0.05) !important;
        border-top-left-radius: 1rem !important;
        border-bottom-left-radius: 1rem !important;
    }
    .fi-ta-row td:last-child {
        border-right: 1px solid rgba(15, 23, 42, 0.05) !important;
        border-top-right-radius: 1rem !important;
        border-bottom-right-radius: 1rem !important;
    }
    .dark .fi-ta-row td:first-child {
        border-left-color: rgba(255, 255, 255, 0.06) !important;
    }
    .dark .fi-ta-row td:last-child {
        border-right-color: rgba(255, 255, 255, 0.06) !important;
    }
    .fi-ta-row:hover td {
        border-top-color: rgba(251, 185, 0, 0.45) !important;
        border-bottom-color: rgba(251, 185, 0, 0.45) !important;
    }
    .fi-ta-row:hover td:first-child {
        border-left-color: rgba(251, 185, 0, 0.45) !important;
    }
    .fi-ta-row:hover td:last-child {
        border-right-color: rgba(251, 185, 0, 0.45) !important;
    }

    /* Alignment Rules: Left-align text/names, Right-align action triggers & numbers/currency */
    .fi-ta-text {
        text-align: left;
    }
    .fi-ta-actions-cell,
    .fi-ta-cell:last-child:has(.fi-ta-actions) {
        text-align: right !important;
        width: 1% !important;
        white-space: nowrap !important;
    }
    .fi-ta-actions {
        justify-content: flex-end !important;
    }
    .fi-ta-text-item-numeric,
    [class*="fi-ta-text"][class*="numeric"],
    .fi-ta-cell[class*="numeric"] {
        text-align: right !important;
        font-variant-numeric: tabular-nums !important;
    }

    /* Grouped Table Headers (Seamless Single-Bar Accordion Row) */
    .fi-ta-group-header-row td {
        background: #f0f5fc !important;
        color: #0a4282 !important;
        border-top: 1px solid rgba(10, 66, 130, 0.14) !important;
        border-bottom: 1px solid rgba(10, 66, 130, 0.14) !important;
        border-left: none !important;
        border-right: none !important;
        border-radius: 0 !important;
        padding: 0.65rem 0.75rem !important;
        cursor: pointer !important;
        vertical-align: middle !important;
        transition: background-color 0.2s ease !important;
    }
    .fi-ta-group-header-row td:first-child {
        border-left: 1px solid rgba(10, 66, 130, 0.14) !important;
        border-top-left-radius: 0.85rem !important;
        border-bottom-left-radius: 0.85rem !important;
    }
    .fi-ta-group-header-row td:last-child {
        border-right: 1px solid rgba(10, 66, 130, 0.14) !important;
        border-top-right-radius: 0.85rem !important;
        border-bottom-right-radius: 0.85rem !important;
    }
    .fi-ta-group-header {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .dark .fi-ta-group-header-row td {
        background: rgba(30, 58, 138, 0.28) !important;
        border-top-color: rgba(147, 197, 253, 0.16) !important;
        border-bottom-color: rgba(147, 197, 253, 0.16) !important;
    }
    .dark .fi-ta-group-header-row td:first-child {
        border-left-color: rgba(147, 197, 253, 0.16) !important;
    }
    .dark .fi-ta-group-header-row td:last-child {
        border-right-color: rgba(147, 197, 253, 0.16) !important;
    }
    .fi-ta-group-header-row:hover td {
        background: #e4eefb !important;
    }
    .dark .fi-ta-group-header-row:hover td {
        background: rgba(30, 58, 138, 0.42) !important;
    }
    .fi-ta-group-header h4,
    .fi-ta-group-heading,
    .fi-ta-group-header-row td span {
        font-weight: 700 !important;
        color: #0a4282 !important;
        font-size: 0.9rem !important;
        letter-spacing: 0.01em !important;
    }
    .dark .fi-ta-group-header h4,
    .dark .fi-ta-group-heading,
    .dark .fi-ta-group-header-row td span {
        color: #f8fafc !important;
    }
    .fi-ta-group-header .fi-icon-btn,
    .fi-ta-group-header svg,
    .fi-ta-group-header-row td svg {
        color: #0a4282 !important;
        transition: transform 0.25s ease !important;
    }
    .dark .fi-ta-group-header .fi-icon-btn,
    .dark .fi-ta-group-header svg,
    .dark .fi-ta-group-header-row td svg {
        color: #fbb900 !important;
    }

    /* Remove default borders from table container */
    .fi-ta-content,
    .fi-ta-content-ctn,
    .fi-ta-ctn {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        transform: none !important;
    }

    /* 3. Global Sections, Form Headers & Modals (No backdrop-filter/transform on .fi-section/.fi-ta-record so Floating UI x-anchor works) */
    .fi-section, .fi-fo-section, .fi-in-section, .fi-ta-record, .fi-modal-window {
        border-radius: 1.25rem !important; /* rounded-2xl */
        border: 1px solid rgba(15, 23, 42, 0.07) !important;
        background: rgba(255, 255, 255, 0.95) !important;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.04), 0 8px 10px -6px rgba(15, 23, 42, 0.02) !important;
        transition: background-color 0.3s ease-in-out, border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out !important;
    }

    .dark .fi-section, .dark .fi-fo-section, .dark .fi-in-section, .dark .fi-ta-record, .dark .fi-modal-window {
        background: rgba(15, 23, 42, 0.92) !important;
        border: 1px solid rgba(255, 255, 255, 0.09) !important;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5) !important;
    }

    /* Section & Modal Header Polish */
    .fi-section-header,
    .fi-modal-header {
        border-bottom: 1px solid rgba(15, 23, 42, 0.06) !important;
        padding: 1.125rem 1.5rem !important;
        background: linear-gradient(90deg, rgba(10, 66, 130, 0.03), transparent) !important;
        border-top-left-radius: 1.25rem !important;
        border-top-right-radius: 1.25rem !important;
    }
    .dark .fi-section-header,
    .dark .fi-modal-header {
        border-bottom-color: rgba(255, 255, 255, 0.07) !important;
        background: linear-gradient(90deg, rgba(251, 185, 0, 0.06), transparent) !important;
    }
    .fi-section-header-heading,
    .fi-modal-heading {
        font-weight: 700 !important;
        color: #0a4282 !important;
        letter-spacing: -0.01em !important;
    }
    .dark .fi-section-header-heading,
    .dark .fi-modal-heading {
        color: #f8fafc !important;
    }
    .fi-section-header-description,
    .fi-modal-description {
        color: #64748b !important;
        font-size: 0.85rem !important;
    }
    .dark .fi-section-header-description,
    .dark .fi-modal-description {
        color: #94a3b8 !important;
    }
    .fi-section-header-icon {
        background-color: rgba(10, 66, 130, 0.1) !important;
        color: #0a4282 !important;
        padding: 0.4rem !important;
        border-radius: 0.65rem !important;
    }
    .dark .fi-section-header-icon {
        background-color: rgba(251, 185, 0, 0.15) !important;
        color: #fbb900 !important;
    }
    .fi-section-content {
        padding: 1.5rem !important;
    }

    /* 4. Global Tabs & Form Tabs */
    .fi-tabs, .fi-fo-tabs-tab-list {
        background-color: rgba(15, 23, 42, 0.04) !important;
        border-radius: 1rem !important;
        padding: 0.375rem !important;
        border: 1px solid rgba(15, 23, 42, 0.05) !important;
        gap: 0.25rem !important;
    }
    
    .dark .fi-tabs, .dark .fi-fo-tabs-tab-list {
        background-color: rgba(255, 255, 255, 0.05) !important;
        border-color: rgba(255, 255, 255, 0.08) !important;
    }

    .fi-tabs-item {
        border-radius: 0.75rem !important;
        transition: all 0.25s ease !important;
        padding: 0.5rem 1rem !important;
        font-weight: 600 !important;
    }
    
    .fi-tabs-item:hover {
        background-color: rgba(10, 66, 130, 0.06) !important;
    }
    
    .dark .fi-tabs-item:hover {
        background-color: rgba(255, 255, 255, 0.06) !important;
    }

    .fi-tabs-item.fi-active {
        background-color: #ffffff !important;
        box-shadow: 0 4px 8px -1px rgba(10, 66, 130, 0.12), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
        color: #0a4282 !important;
        border-bottom: 2px solid #fbb900 !important;
    }
    
    .dark .fi-tabs-item.fi-active {
        background-color: rgba(15, 23, 42, 0.95) !important;
        box-shadow: 0 4px 8px -1px rgba(0, 0, 0, 0.4) !important;
        color: #fbb900 !important;
        border-bottom: 2px solid #fbb900 !important;
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

    /* 7. Browser Surfaces & Accessibility Craft (Selection, Caret, Focus-Visible, Scrollbars) */
    ::selection {
        background-color: #0a4282;
        color: #ffffff;
    }
    .dark ::selection {
        background-color: #fbb900;
        color: #0a4282;
    }
    input, textarea, select {
        caret-color: #0a4282;
    }
    .dark input, .dark textarea, .dark select {
        caret-color: #fbb900;
    }
    :focus-visible {
        outline: 2px solid #0a4282;
        outline-offset: 2px;
    }
    .dark :focus-visible {
        outline-color: #fbb900;
    }
    * {
        scrollbar-width: thin;
        scrollbar-color: rgba(10, 66, 130, 0.28) transparent;
    }
    .dark * {
        scrollbar-color: rgba(251, 185, 0, 0.3) transparent;
    }
    .fi-ta-ctn,
    .fi-ta-content {
        max-width: 100% !important;
        overflow-x: auto !important;
        -webkit-overflow-scrolling: touch;
    }

    /* 8. Cross-Device Responsive Adaptation (Mobile <= 767px & Laptop/Desktop) */
    @media (max-width: 767px) {
        html, body, .fi-main {
            max-width: 100vw !important;
            overflow-x: hidden !important;
        }
        .fi-main {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
        .fi-header-heading {
            font-size: 1.35rem !important;
            line-height: 1.25 !important;
        }
        .g-welcome-title {
            font-size: 1.35rem !important;
        }
        .fi-header {
            gap: 0.75rem !important;
        }
        .fi-header-actions {
            flex-wrap: wrap !important;
            gap: 0.45rem !important;
        }
        .fi-btn {
            min-height: 38px !important;
            padding-left: 0.85rem !important;
            padding-right: 0.85rem !important;
        }
        .fi-ta-actions .fi-btn {
            min-height: 32px !important;
            padding-left: 0.7rem !important;
            padding-right: 0.7rem !important;
            font-size: 0.78rem !important;
        }
        .fi-icon-btn {
            min-width: 36px !important;
            min-height: 36px !important;
        }
        input, select, textarea, .fi-input {
            font-size: 16px !important; /* Prevent iOS auto-zoom on focus */
        }
        /* Mobile Table Toolbar & Search */
        .fi-ta-header-toolbar {
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
            padding: 0.65rem 0.75rem !important;
        }
        .fi-ta-search-field {
            width: 100% !important;
            flex: 1 0 100% !important;
        }
        /* Responsive Compact Card Tables on Mobile (No horizontal scrolling, no bloated tall cards) */
        .fi-ta-ctn,
        .fi-ta-content {
            overflow-x: visible !important;
        }
        .fi-ta-table,
        .fi-ta-table tbody {
            display: block !important;
            width: 100% !important;
            border-spacing: 0 !important;
        }
        .fi-ta-table thead {
            display: none !important;
        }
        .fi-ta-table tr.fi-ta-row {
            display: flex !important;
            flex-direction: column !important;
            gap: 0 !important;
            width: 100% !important;
            padding: 0.55rem 0.85rem !important;
            margin-bottom: 0.65rem !important;
            border-radius: 0.95rem !important;
            border: 1px solid rgba(15, 23, 42, 0.09) !important;
            background-color: #ffffff !important;
            box-shadow: 0 2px 6px -1px rgba(15, 23, 42, 0.05) !important;
        }
        .dark .fi-ta-table tr.fi-ta-row {
            border-color: rgba(255, 255, 255, 0.1) !important;
            background-color: rgba(30, 41, 59, 0.72) !important;
        }
        .fi-ta-table tr.fi-ta-row td,
        .fi-ta-table tr.fi-ta-row td.fi-ta-cell {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            gap: 0.75rem !important;
            width: 100% !important;
            padding: 0.42rem 0 !important;
            border: none !important;
            border-bottom: 1px dashed rgba(15, 23, 42, 0.08) !important;
            border-radius: 0 !important;
            text-align: right !important;
            white-space: normal !important;
        }
        .dark .fi-ta-table tr.fi-ta-row td,
        .dark .fi-ta-table tr.fi-ta-row td.fi-ta-cell {
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }
        .fi-ta-table tr.fi-ta-row td[data-label]::before {
            content: attr(data-label);
            font-size: 0.75rem !important;
            font-weight: 600 !important;
            color: #64748b !important;
            text-align: left !important;
            flex-shrink: 0 !important;
            max-width: 42%;
        }
        .dark .fi-ta-table tr.fi-ta-row td[data-label]::before {
            color: #94a3b8 !important;
        }
        .fi-ta-table tr.fi-ta-row td[data-label=""]::before,
        .fi-ta-table tr.fi-ta-row td.fi-ta-selection-cell::before,
        .fi-ta-table tr.fi-ta-row td.fi-ta-actions-cell::before,
        .fi-ta-table tr.fi-ta-row td:last-child:has(.fi-ta-actions)::before {
            display: none !important;
        }
        .fi-ta-table tr.fi-ta-row td .fi-ta-col-wrp {
            padding: 0 !important;
            text-align: right !important;
        }
        .fi-ta-table tr.fi-ta-row td .fi-ta-text,
        .fi-ta-table tr.fi-ta-row td .fi-ta-text-item {
            text-align: right !important;
            justify-content: flex-end !important;
        }
        .fi-ta-table tr.fi-ta-row td.fi-ta-selection-cell {
            justify-content: flex-start !important;
            padding-bottom: 0.35rem !important;
        }
        .fi-ta-table tr.fi-ta-row td.fi-ta-actions-cell,
        .fi-ta-table tr.fi-ta-row td:last-child:has(.fi-ta-actions) {
            display: flex !important;
            justify-content: flex-end !important;
            align-items: center !important;
            width: 100% !important;
            padding-top: 0.5rem !important;
            padding-bottom: 0.1rem !important;
            border-bottom: none !important;
        }
        .fi-ta-table tr.fi-ta-group-header-row {
            display: flex !important;
            align-items: center !important;
            gap: 0.55rem !important;
            width: 100% !important;
            margin-bottom: 0.5rem !important;
            background: #f0f5fc !important;
            border: 1px solid rgba(10, 66, 130, 0.14) !important;
            border-radius: 0.85rem !important;
            padding: 0.55rem 0.75rem !important;
        }
        .dark .fi-ta-table tr.fi-ta-group-header-row {
            background: rgba(30, 58, 138, 0.28) !important;
            border-color: rgba(147, 197, 253, 0.16) !important;
        }
        .fi-ta-table tr.fi-ta-group-header-row td {
            display: block !important;
            width: auto !important;
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
        }
        .fi-ta-table tr.fi-ta-group-header-row td:last-child {
            flex: 1 1 auto !important;
        }
        .fi-tabs, .fi-fo-tabs-tab-list {
            overflow-x: auto !important;
            flex-wrap: nowrap !important;
            scrollbar-width: none;
        }
        .fi-tabs::-webkit-scrollbar,
        .fi-fo-tabs-tab-list::-webkit-scrollbar {
            display: none;
        }
        .fi-modal-window {
            max-width: calc(100vw - 1rem) !important;
            border-radius: 1rem !important;
        }
        .fi-section-content {
            padding: 1rem !important;
        }
    }

    /* 9. Shared Genza Accordion & Click-Row Inline-Edit System (.gx-*) — Clean White Surfaces + Smooth Motion */
    @keyframes gxExpandIn {
        from { opacity: 0; transform: translateY(-4px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .gx-wrap { display: flex; flex-direction: column; gap: 0.9rem; font-family: 'Outfit', sans-serif; }
    .gx-toolbar {
        display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 0.75rem;
        background: #ffffff; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 1rem;
        padding: 0.8rem 1rem; box-shadow: 0 4px 14px -4px rgba(15, 23, 42, 0.06);
        transition: box-shadow 0.25s ease, border-color 0.25s ease;
    }
    .dark .gx-toolbar { background: rgba(30, 41, 59, 0.85); border-color: rgba(255, 255, 255, 0.09); }
    .gx-search {
        flex: 1 1 240px; min-width: 190px; padding: 0.52rem 0.9rem; border-radius: 0.75rem;
        border: 1px solid #cbd5e1; background: #f8fafc; color: #0f172a; font-size: 0.88rem;
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
    }
    .gx-search:focus { outline: none; background: #ffffff; border-color: #0a4282; box-shadow: 0 0 0 3px rgba(10, 66, 130, 0.12); }
    .dark .gx-search { background: #0f172a; border-color: #334155; color: #f8fafc; }
    .dark .gx-search:focus { border-color: #fbb900; box-shadow: 0 0 0 3px rgba(251, 185, 0, 0.2); }
    .gx-date-input {
        padding: 0.48rem 0.7rem; border-radius: 0.7rem; border: 1px solid #cbd5e1;
        background: #f8fafc; color: #0f172a; font-size: 0.82rem;
    }
    .dark .gx-date-input { background: #0f172a; border-color: #334155; color: #f8fafc; }
    .gx-badges { display: flex; flex-wrap: wrap; align-items: center; gap: 0.45rem; }
    .gx-pill {
        display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.28rem 0.72rem;
        border-radius: 9999px; font-size: 0.75rem; font-weight: 700; font-variant-numeric: tabular-nums;
    }
    .gx-pill-primary { background: rgba(10, 66, 130, 0.07); color: #0a4282; border: 1px solid rgba(10, 66, 130, 0.14); }
    .dark .gx-pill-primary { background: rgba(14, 165, 233, 0.14); color: #bae6fd; border-color: rgba(14, 165, 233, 0.28); }
    .gx-pill-gold { background: #fbb900; color: #0a4282; }
    .gx-pill-success { background: rgba(16, 185, 129, 0.11); color: #047857; border: 1px solid rgba(16, 185, 129, 0.24); }
    .dark .gx-pill-success { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; }
    .gx-pill-danger { background: rgba(244, 63, 94, 0.11); color: #be123c; border: 1px solid rgba(244, 63, 94, 0.24); }
    .dark .gx-pill-danger { background: rgba(244, 63, 94, 0.22); color: #fda4af; }
    .gx-pill-muted { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .dark .gx-pill-muted { background: #1e293b; color: #cbd5e1; border-color: #334155; }
    .gx-level-card {
        border-radius: 1rem; overflow: hidden; border: 1px solid rgba(15, 23, 42, 0.08);
        background: #ffffff; box-shadow: 0 4px 14px -4px rgba(15, 23, 42, 0.05);
        transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.25s ease;
    }
    .gx-level-card:hover {
        border-color: rgba(251, 185, 0, 0.55);
        box-shadow: 0 10px 24px -6px rgba(10, 66, 130, 0.1);
    }
    .dark .gx-level-card { background: #1e293b; border-color: rgba(255, 255, 255, 0.09); }
    .dark .gx-level-card:hover { border-color: rgba(251, 185, 0, 0.4); }
    .gx-level-btn {
        width: 100%; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
        gap: 0.5rem; padding: 0.85rem 1.05rem; background: #ffffff; color: #0a4282; font-weight: 700;
        font-size: 0.94rem; border: none; border-bottom: 1px solid rgba(15, 23, 42, 0.06);
        cursor: pointer; text-align: left; transition: background-color 0.2s ease, color 0.2s ease;
    }
    .gx-level-btn:hover { background: #f8fafc; }
    .dark .gx-level-btn { background: #1e293b; color: #f8fafc; border-bottom-color: rgba(255, 255, 255, 0.08); }
    .dark .gx-level-btn:hover { background: #26354d; }
    .gx-level-tag {
        display: inline-flex; align-items: center; justify-content: center; min-width: 2.5rem;
        padding: 0.18rem 0.65rem; border-radius: 9999px; background: rgba(10, 66, 130, 0.08); color: #0a4282;
        border: 1px solid rgba(10, 66, 130, 0.15); font-size: 0.73rem; font-weight: 800; letter-spacing: 0.02em;
    }
    .dark .gx-level-tag { background: #fbb900; color: #0a4282; border-color: #fbb900; }
    .gx-glass-pill {
        display: inline-flex; align-items: center; padding: 0.18rem 0.62rem; border-radius: 9999px;
        background: #f1f5f9; border: 1px solid #e2e8f0; font-size: 0.74rem; font-weight: 600; color: #475569;
        font-variant-numeric: tabular-nums;
    }
    .dark .gx-glass-pill { background: rgba(255, 255, 255, 0.09); border-color: rgba(255, 255, 255, 0.12); color: #cbd5e1; }
    .gx-chevron { width: 1rem; height: 1rem; color: #0a4282; transition: transform 0.25s cubic-bezier(0.22, 1, 0.36, 1); flex-shrink: 0; }
    .dark .gx-chevron { color: #fbb900; }
    .gx-chevron-open { transform: rotate(180deg); }
    .gx-level-body {
        padding: 0.85rem; display: flex; flex-direction: column; gap: 0.7rem; background: #f8fafc;
        animation: gxExpandIn 0.22s ease-out;
    }
    .dark .gx-level-body { background: #0f172a; }
    .gx-school-card {
        border-radius: 0.85rem; border: 1px solid rgba(15, 23, 42, 0.08); background: #ffffff; overflow: hidden;
        transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .gx-school-card:hover { border-color: rgba(10, 66, 130, 0.22); }
    .dark .gx-school-card { background: #1e293b; border-color: rgba(255, 255, 255, 0.08); }
    .gx-school-bar {
        width: 100%; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between;
        gap: 0.5rem; padding: 0.68rem 0.95rem; background: #ffffff;
        color: #0a4282; font-weight: 700; font-size: 0.88rem; border: none;
        border-bottom: 1px solid rgba(15, 23, 42, 0.06); cursor: pointer; text-align: left;
        transition: background-color 0.2s ease;
    }
    .gx-school-bar:hover { background: #f8fafc; }
    .dark .gx-school-bar { background: #1e293b; color: #f8fafc; border-bottom-color: rgba(255, 255, 255, 0.08); }
    .gx-school-body { padding: 0.7rem; display: flex; flex-direction: column; gap: 0.6rem; animation: gxExpandIn 0.2s ease-out; }
    .gx-class-card { border-radius: 0.75rem; border: 1px solid #e2e8f0; overflow: hidden; background: #ffffff; }
    .dark .gx-class-card { border-color: rgba(255, 255, 255, 0.08); background: #0f172a; }
    .gx-class-btn {
        width: 100%; display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;
        padding: 0.55rem 0.85rem; background: #f8fafc; color: #0f172a; font-weight: 700;
        font-size: 0.84rem; border: none; border-bottom: 1px solid #e2e8f0; cursor: pointer; text-align: left;
        transition: background-color 0.2s ease, color 0.2s ease;
    }
    .gx-class-btn:hover { background: #f0f6ff; color: #0a4282; }
    .dark .gx-class-btn { background: rgba(30, 41, 59, 0.75); color: #e2e8f0; border-bottom-color: rgba(255, 255, 255, 0.07); }
    .gx-table-wrap { overflow-x: auto; animation: gxExpandIn 0.2s ease-out; }
    .gx-table { width: 100%; border-collapse: collapse; font-size: 0.83rem; text-align: left; }
    .gx-table th {
        background: #f8fafc; color: #0a4282; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.04em; font-size: 0.7rem; padding: 0.6rem 0.7rem; border-bottom: 1px solid rgba(10, 66, 130, 0.12);
    }
    .dark .gx-table th { background: rgba(15, 23, 42, 0.75); color: #93c5fd; border-bottom-color: rgba(251, 185, 0, 0.2); }
    .gx-table td {
        padding: 0.62rem 0.7rem; border-bottom: 1px solid rgba(15, 23, 42, 0.05);
        vertical-align: middle; color: #0f172a; transition: background-color 0.18s ease;
    }
    .dark .gx-table td { border-bottom-color: rgba(255, 255, 255, 0.06); color: #e2e8f0; }
    .gx-table tbody tr:hover td { background: #f8fafc; }
    .dark .gx-table tbody tr:hover td { background: rgba(255, 255, 255, 0.035); }
    .gx-row-click { cursor: pointer; }
    .gx-avatar {
        width: 2.35rem; height: 2.35rem; border-radius: 9999px; object-fit: cover;
        border: 2px solid rgba(10, 66, 130, 0.14); flex-shrink: 0; background: #eff6ff;
        display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.78rem; color: #0a4282;
    }
    .gx-photo-preview {
        width: 5.5rem; height: 7rem; border-radius: 0.85rem; object-fit: cover;
        border: 2px solid rgba(10, 66, 130, 0.18); box-shadow: 0 6px 14px -4px rgba(15, 23, 42, 0.12);
        background: #eff6ff; display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.5rem; color: #0a4282; overflow: hidden;
    }
    .gx-section-title {
        font-size: 0.74rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em;
        color: #0a4282; border-bottom: 1px solid rgba(10, 66, 130, 0.12); padding-bottom: 0.35rem; margin: 0.9rem 0 0.6rem 0;
    }
    .dark .gx-section-title { color: #fbb900; border-bottom-color: rgba(251, 185, 0, 0.22); }
    .gx-mono { font-family: ui-monospace, monospace; font-variant-numeric: tabular-nums; font-size: 0.78rem; }
    .gx-btn {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.25rem;
        padding: 0.32rem 0.78rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700;
        border: none; cursor: pointer; text-decoration: none; transition: transform 0.18s ease, box-shadow 0.18s ease, opacity 0.15s ease;
    }
    .gx-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 10px -2px rgba(15, 23, 42, 0.16); }
    .gx-btn-edit { background: #fbb900; color: #0a4282; }
    .gx-btn-card { background: #0a4282; color: #ffffff; }
    .gx-btn-del { background: #f43f5e; color: #ffffff; }
    .gx-btn-save { background: #0a4282; color: #ffffff; padding: 0.48rem 1.15rem; font-size: 0.82rem; }
    .gx-btn-cancel { background: #e2e8f0; color: #0f172a; padding: 0.48rem 1rem; font-size: 0.82rem; }
    .dark .gx-btn-cancel { background: #334155; color: #f8fafc; }
    .gx-mobile-list { display: none; padding: 0.65rem; flex-direction: column; gap: 0.6rem; background: #f8fafc; animation: gxExpandIn 0.2s ease-out; }
    .dark .gx-mobile-list { background: #0f172a; }
    .gx-m-card {
        background: #ffffff; border: 1px solid rgba(15, 23, 42, 0.08); border-radius: 0.9rem; padding: 0.75rem 0.85rem;
        font-size: 0.8rem; box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04); transition: box-shadow 0.2s ease, border-color 0.2s ease;
    }
    .gx-m-card:hover { border-color: rgba(251, 185, 0, 0.5); }
    .dark .gx-m-card { background: #1e293b; border-color: rgba(255, 255, 255, 0.09); }
    .gx-kv {
        display: flex; justify-content: space-between; align-items: center; gap: 0.65rem;
        padding: 0.36rem 0; border-bottom: 1px dashed rgba(15, 23, 42, 0.08);
    }
    .dark .gx-kv { border-bottom-color: rgba(255, 255, 255, 0.08); }
    .gx-kv-k { color: #64748b; font-weight: 600; font-size: 0.75rem; flex-shrink: 0; }
    .dark .gx-kv-k { color: #94a3b8; }
    .gx-kv-v { font-weight: 600; text-align: right; color: #0f172a; word-break: break-word; }
    .dark .gx-kv-v { color: #f8fafc; }
    .gx-m-actions { display: flex; justify-content: flex-end; gap: 0.4rem; padding-top: 0.55rem; margin-top: 0.25rem; }
    .gx-edit-box {
        background: #ffffff; border: 1px solid rgba(10, 66, 130, 0.16); border-radius: 0.9rem; padding: 1rem; margin-top: 0.5rem;
        box-shadow: 0 8px 20px -6px rgba(10, 66, 130, 0.08); animation: gxExpandIn 0.24s ease-out;
    }
    .dark .gx-edit-box { background: #0f172a; border-color: rgba(251, 185, 0, 0.25); }
    .gx-edit-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 0.68rem; }
    .gx-edit-label { display: block; font-size: 0.73rem; font-weight: 700; color: #0a4282; margin-bottom: 0.22rem; }
    .dark .gx-edit-label { color: #93c5fd; }
    .gx-edit-input {
        width: 100%; padding: 0.46rem 0.72rem; border-radius: 0.65rem; border: 1px solid #cbd5e1;
        background: #f8fafc; color: #0f172a; font-size: 0.83rem; transition: border-color 0.2s ease, background-color 0.2s ease;
    }
    .gx-edit-input:focus { outline: none; background: #ffffff; border-color: #0a4282; box-shadow: 0 0 0 2px rgba(10, 66, 130, 0.14); }
    .dark .gx-edit-input { background: #1e293b; border-color: #475569; color: #f8fafc; }
    @media (max-width: 767px) {
        .gx-table-wrap { display: none; }
        .gx-mobile-list { display: flex; }
        .gx-edit-grid { grid-template-columns: 1fr; }
    }

</style>

<div id="lang-switch-overlay">
    <div class="spinner-elegant"></div>
    <div class="lang-text-elegant" id="lang-switch-text">Translating interface...</div>
</div>

<script>
    function hydrateFilamentTableLabels() {
        document.querySelectorAll('.fi-ta-table').forEach((table) => {
            const headers = Array.from(table.querySelectorAll('thead tr th')).map((th) =>
                (th.innerText || '').replace(/\s+/g, ' ').trim()
            );
            if (!headers.length) return;
            table.querySelectorAll('tbody tr.fi-ta-row').forEach((row) => {
                const cells = Array.from(row.children).filter((el) => el.tagName === 'TD');
                cells.forEach((td, idx) => {
                    if (td.classList.contains('fi-ta-selection-cell') || td.classList.contains('fi-ta-actions-cell')) {
                        return;
                    }
                    const label = headers[idx] || '';
                    if (label) {
                        td.setAttribute('data-label', label);
                    }
                });
            });
            if (!table.dataset.gxObserved) {
                table.dataset.gxObserved = '1';
                const tbody = table.querySelector('tbody');
                if (tbody) {
                    new MutationObserver(() => hydrateFilamentTableLabels()).observe(tbody, { childList: true });
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', hydrateFilamentTableLabels);
    document.addEventListener('livewire:navigated', hydrateFilamentTableLabels);

    document.addEventListener('livewire:init', () => {
        hydrateFilamentTableLabels();
        if (window.Livewire && typeof Livewire.hook === 'function') {
            Livewire.hook('commit', ({ succeed }) => {
                succeed(() => queueMicrotask(hydrateFilamentTableLabels));
            });
        }
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
