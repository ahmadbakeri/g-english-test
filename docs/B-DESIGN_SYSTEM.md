# G-English Design System (`docs/DESIGN_SYSTEM.md`)

Single source of truth for the visual design language across all 4 Filament panels (`admin`, `tutor`, `student`, `parent`), the public landing page (`welcome.blade.php`), and the CBT Exam Engine (`exam.css`).

---

## 1. Color Palette & Dark Mode

### Filament Panel Tokens (`AdminPanelProvider`, `TutorPanelProvider`, `StudentPanelProvider`)
| Token | Value | Hex / Tailwind | Usage |
| :--- | :--- | :--- | :--- |
| `primary` | `Color::hex('#0a4282')` | `#0a4282` (Genza Blue) | Brand primary, active states, focus rings, table headers |
| `secondary` | `Color::hex('#fbb900')` | `#fbb900` (Genza Gold) | Brand accent, dark-mode hover highlights, CTA badges |
| `danger` | `Color::Rose` | Tailwind `rose` (`#f43f5e` / `#ef4444`) | Unpaid, absent, errors, cheating warnings, delete actions |
| `gray` | `Color::Slate` | Tailwind `slate` (`#64748b` / `#0f172a`) | Neutral surfaces, muted text, dark mode backgrounds |
| `info` | `Color::Sky` | Tailwind `sky` (`#0ea5e9` / `#38bdf8`) | Informational badges, income stats, excused attendance |
| `success` | `Color::Emerald` | Tailwind `emerald` (`#10b981` / `#22c55e`) | Paid, present, active students, completed exams |
| `warning` | `Color::Amber` | Tailwind `amber` (`#f59e0b` / `#eab308`) | Late attendance, pending status, staff counts |

> **Note (`ParentPanelProvider`)**: Uses `primary => Color::hex('#1a368b')` (Navy Blue) and `secondary => Color::hex('#fbb900')` (Yellow Gold). Match `--color-brand-navy: #1a368b` and `--color-brand-gold: #fbb900` in `resources/css/exam.css`.

### Landing Page Tokens (`welcome.blade.php`)
- `genza.blue`: `#0a4282`
- `genza.yellow`: `#fbb900`
- `genza.dark`: `#0f172a` (`slate-900`)
- `genza.light`: `#f8fafc` (`slate-50`)

### Gradients & Glows
- **Welcome Widget Title**:
  - Light: `linear-gradient(to right, #1e3a8a, #3b82f6)` with `-webkit-background-clip: text`
  - Dark: `linear-gradient(to right, #60a5fa, #93c5fd)`
- **Welcome Date Pill Badge**:
  - Light: `background: linear-gradient(135deg, #eff6ff, #dbeafe); border: 1px solid #bfdbfe; color: #1d4ed8`
  - Dark: `background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); color: #93c5fd`
- **Scan QR Header**: `linear-gradient(135deg, #0a4282, #fbb900)`
- **Quick Links Icon Boxes (`.ql-icon-box`)**:
  - `.ql-emerald`: `linear-gradient(to bottom right, #34d399, #059669)` + `box-shadow: 0 4px 14px 0 rgba(16, 185, 129, 0.39)`
  - `.ql-violet`: `linear-gradient(to bottom right, #a78bfa, #7c3aed)` + `box-shadow: 0 4px 14px 0 rgba(139, 92, 246, 0.39)`
  - `.ql-amber`: `linear-gradient(to bottom right, #fbbf24, #d97706)` + `box-shadow: 0 4px 14px 0 rgba(245, 158, 11, 0.39)`
  - `.ql-sky`: `linear-gradient(to bottom right, #38bdf8, #0284c7)` + `box-shadow: 0 4px 14px 0 rgba(14, 165, 233, 0.39)`
  - `.ql-rose`: `linear-gradient(to bottom right, #fb7185, #e11d48)` + `box-shadow: 0 4px 14px 0 rgba(244, 63, 94, 0.39)`
  - `.ql-indigo`: `linear-gradient(to bottom right, #818cf8, #4f46e5)` + `box-shadow: 0 4px 14px 0 rgba(99, 102, 241, 0.39)`
  - `.ql-teal`: `linear-gradient(to bottom right, #2dd4bf, #0d9488)` + `box-shadow: 0 4px 14px 0 rgba(20, 184, 166, 0.39)`
  - `.ql-orange`: `linear-gradient(to bottom right, #fb923c, #ea580c)` + `box-shadow: 0 4px 14px 0 rgba(249, 115, 22, 0.39)`
  - `.ql-slate`: `linear-gradient(to bottom right, #94a3b8, #475569)` + `box-shadow: 0 4px 14px 0 rgba(100, 116, 139, 0.39)`
- **Stat Card Soft Gradients (`.stat-*`)**:
  - Light: `linear-gradient(135deg, rgba(<RGB>, 0.08), rgba(255, 255, 255, 0))`
  - Dark: `linear-gradient(135deg, rgba(<RGB>, 0.15), rgba(255, 255, 255, 0.02))`
  - Variants: `.stat-emerald` (`16, 185, 129`), `.stat-sky` (`14, 165, 233`), `.stat-amber` (`245, 158, 11`), `.stat-rose` (`244, 63, 94`), `.stat-indigo` (`99, 102, 241`).

### Dark Mode Architecture
- **Class Strategy**: Toggled via `.dark` on `<html>` (`darkMode: 'class'`).
- **Filament Topbar Switcher**: Injected via `PanelsRenderHook::USER_MENU_BEFORE` (`filament.components.theme-toggle`). Hides default profile dropdown switcher via `.fi-dropdown-list:has(.fi-theme-switcher) { display: none !important; }`, stores preference in `localStorage.getItem('theme')`, and dispatches `$dispatch('theme-changed', this.theme)`.
- **Global Theme Transition**: `html, body, .fi-topbar, .fi-sidebar, .fi-main` transition `background-color`, `color`, `border-color`, and `box-shadow` over `0.4s ease-in-out`.
- **Surface Mapping**:
  - Page Body: Light `bg-slate-50` (`#f8fafc`) -> Dark `bg-slate-900` (`#0f172a`).
  - Glass Cards / Sections: Light `rgba(255, 255, 255, 0.85)` with `1px solid rgba(0, 0, 0, 0.05)` -> Dark `rgba(15, 23, 42, 0.75)` (or `rgba(30, 41, 59, 0.75)` in custom student views) with `1px solid rgba(255, 255, 255, 0.08)`.
  - Inputs: Light `#f8fafc` (`border: rgba(0,0,0,0.1)`) -> Dark `rgba(15, 23, 42, 0.6)` (`border: rgba(255,255,255,0.1)`).

---

## 2. Typography

### Font Families
- **Filament Panels (`->font('Outfit')`) & Landing Page (`welcome.blade.php`) & Exam Engine (`exam.css`)**:
  - Primary Display & UI Font: `'Outfit', sans-serif` (loaded via Bunny Fonts `outfit:400,500,600,700,800,900` or Google Fonts `300..700`).
- **Default App CSS (`resources/css/app.css` & `vite.config.js`)**:
  - `--font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif` (weights `400, 500, 600` via `laravel-vite-plugin/fonts`).
- **Monospace (CBT Timers & Countdowns)**:
  - `ui-monospace, monospace` (`font-weight: 800` to `900`).

### Weight & Scale Hierarchy
- **Landing Hero H1**: `text-5xl md:text-7xl font-black` (`900`), `tracking-tight`.
- **Section Headings (Landing)**: `text-3xl` to `text-4xl font-black` (`900`).
- **Welcome Widget Title (`.g-welcome-title`)**: `1.875rem` (`text-3xl`), `font-weight: 800`, `letter-spacing: -0.025em`.
- **Custom Page Titles (`.material-title`, `.cbt-title`, `.report-title`)**: `1.5rem` to `2rem`, `font-weight: 800`, `line-height: 1.2`.
- **Stat Values (`.stat-value`)**: `2.5rem` (`text-4xl`), `font-weight: 900`, `line-height: 1`.
- **Table Headers (`.fi-ta-table th`)**: `0.75rem` (`text-xs`), `font-weight: 700`, ` uppercase`, `letter-spacing: 0.05em`.
- **Eyebrow / Greeting Labels (`.g-welcome-greeting`, `.stat-label`)**: `0.875rem` (`text-sm`), `font-weight: 500`–`700`, `uppercase`, `letter-spacing: 0.05em`, color `#64748b` (dark `#94a3b8`).

---

## 3. Layout, Borders & Cards

### Panel & Navigation Structure
- **Brand Logo**: `asset('images/logo.png')`, `brandLogoHeight('3rem')`, `brandName('G-English')` (`G-English Parent Portal` in Parent panel).
- **SPA Mode**: Enabled (`->spa()`) across all panels; `->globalSearch(false)` on Admin, Tutor, and Student panels.
- **Navigation Groups** (ordered): `Administration`, `Academic`, `Evaluation`, `Accounting`, `Reports`, `Master Data`, `System`.
- **Colorful Sidebar Icons (`animations.blade.php`)**:
  - Every sidebar icon is assigned a distinct vibrant hex color + matching `drop-shadow(0 0 4px rgba(<RGB>, 0.4))` via `a[href*="..."] .fi-sidebar-item-icon`.
  - Hovering any `.fi-sidebar-item` scales the icon `transform: scale(1.1)` with `filter: drop-shadow(0 0 4px currentColor)`.
  - Active item icon uses `rgb(var(--primary-600))` with `drop-shadow(0 0 6px rgba(var(--primary-600), 0.5))`.
- **Topbar Controls**:
  - `.fi-topbar .fi-icon-btn`: Frosted glass button (`background: rgba(255,255,255,0.6)`, dark `rgba(255,255,255,0.05)`, `backdrop-filter: blur(8px)`), lifts on hover (`translateY(-2px) scale(1.05)`).
  - `.fi-topbar .fi-avatar`: Scales and rotates on hover (`scale(1.1) rotate(5deg)`) with primary border glow.

### Glassmorphism Cards & Containers
- **Global Sections, Modals & Cards** (`.fi-section, .fi-fo-section, .fi-in-section, .fi-ta-record, .fi-modal-window, .glass-panel`):
  - **Border Radius**: `1.25rem` (`rounded-2xl` / `20px`).
  - **Backdrop Filter**: `blur(12px) saturate(180%)`.
  - **Borders**: `1px solid rgba(0, 0, 0, 0.05)` (Light) / `1px solid rgba(255, 255, 255, 0.08)` (Dark).
  - **Shadows**:
    - Light: `0 10px 25px -5px rgba(0, 0, 0, 0.03), 0 8px 10px -6px rgba(0, 0, 0, 0.01)`
    - Dark: `0 10px 30px -10px rgba(0, 0, 0, 0.5)`

### Floating Table Rows (`.fi-ta-table`)
- **Separated Row Layout**: `border-collapse: separate !important; border-spacing: 0 0.5rem !important; background: transparent !important;`.
- **Container Reset**: `.fi-ta-content` has `border: none`, `box-shadow: none`, `background: transparent`.
- **Row Styling (`.fi-ta-row`)**:
  - Rounded card rows (`border-radius: 1rem`, first/last `<td>` rounded `1rem` on outer corners).
  - Light background `#ffffff` with subtle shadow `0 2px 5px -1px rgba(0,0,0,0.03)`; Dark background `rgba(255,255,255,0.03)`.
  - Row Hover: `transform: translateY(-2px) scale(1.001)` with primary-tinted shadow in light mode and `rgba(0,0,0,0.5)` shadow in dark mode.
- **Responsive Table Split**: Resource tables wrap columns in `\Filament\Tables\Columns\Layout\Split::make([...])->from('md')` for mobile card stacking.

### Buttons, Inputs & Tabs
- **Buttons (`.fi-btn`)**: Strictly pill-shaped (`border-radius: 9999px !important`), hover lift `translateY(-2px)` with shadow `0 4px 6px -1px rgba(10, 66, 130, 0.2)`.
- **Inputs (`.fi-input, .fi-select, .fi-textarea`)**: `border-radius: 0.75rem !important` (`rounded-xl`), focus ring `0 0 0 4px rgba(var(--primary-500), 0.15)` and `translateY(-1px)`.
- **Tabs (`.fi-tabs`)**: Pill container (`border-radius: 1rem`, `padding: 0.375rem`), items `border-radius: 0.75rem`, active tab `#ffffff` (dark `#0f172a`) with elevation shadow.

### Responsive Grids & Spacing Rhythm
- **Quick Links Grid (`.ql-grid`)**: `grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1rem;`.
- **Report Filter Grids (`.filter-grid`)**: `grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem;`.
- **Landing & Custom Page Grids**:
  - 4-column feature grid: `grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8`
  - 3-column exam/stats grid: `grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6` (or `2rem`)
  - 4-column CBT exam interface: `lg:grid-cols-4` (`col-span-3` question panel + `col-span-1` sticky navigation grid).

---

## 4. Icons & Badges

### Heroicon Naming Conventions
- **Sidebar Navigation & Page Headers**: Always Outline (`heroicon-o-*` or `Heroicon::Outlined*`), e.g.:
  - `heroicon-o-user-group` (Students), `heroicon-o-academic-cap` (Tutors / Student Report), `heroicon-o-users` (Users), `heroicon-o-clipboard-document-list` (Attendance), `heroicon-o-camera` (Scan QR), `heroicon-o-document-chart-bar` (Attendance Report), `heroicon-o-currency-dollar` (Financial Report), `heroicon-o-banknotes` (Payments / Salaries), `heroicon-o-trophy` (Honors), `heroicon-o-building-library` (ClassRooms), `heroicon-o-bookmark` (Subjects), `heroicon-o-book-open` (Journals / Study Materials), `heroicon-o-calendar` (Academic Years), `heroicon-o-calendar-days` (Semesters).
- **Quick Links & Feature Cards**: Solid icons (`heroicon-s-*`) inside gradient `.ql-icon-box` containers (`1.5rem x 1.5rem`).
- **Stat Card Description Icons & Inline Button Icons**: Mini Solid (`heroicon-m-*`), e.g., `heroicon-m-user-group`, `heroicon-m-banknotes`, `heroicon-m-academic-cap`, `heroicon-m-arrow-right`.

### Badges (`.fi-badge`)
- **Shape & Typography**: Pill-shaped (`border-radius: 9999px !important`), `padding: 0.25rem 0.75rem !important`, `font-weight: 600 !important`, `letter-spacing: 0.025em !important`.
- **Semantic Color Mapping**:
  - **Attendance Status**: `present` / `Present` -> `success`, `late` / `Late` -> `warning`, `absent` / `Absent` -> `danger`, `excused` / `Excused` -> `info`.
  - **Payment / Salary Status**: `paid` -> `success`, `pending` -> `warning`, `unpaid` -> `danger`.
  - **User / Attendance Role**: `admin` -> `danger`, `tutor` -> `success` (or `info` in activity logs), `student` -> `info`, `employee` -> `warning`.
  - **Tutor Grade**: `A` -> `success`, `B` -> `warning`, `C` -> `danger`.
  - **Gender**: `L` -> `info`, `P` -> `danger`.
  - **Academic Metadata (Subject, Level, Specialization)**: `info`.
  - **CBT Attempt Status**: `finished` -> `success`, `in_progress` -> `warning`, `violation_count > 0` -> `danger` (else `gray`).

---

## 5. Charts & Widgets

### Stat Cards (`StatsOverviewWidget`, `TutorStatsWidget`, `StudentStatsWidget`)
- **Required Structure**:
  ```php
  Stat::make($label, $value)
      ->description($description)
      ->descriptionIcon('heroicon-m-*')
      ->icon('heroicon-o-*')
      ->chart([...]) // Sparkline array
      ->color('success' | 'info' | 'warning' | 'danger' | 'primary')
      ->extraAttributes(['class' => 'stat-card stat-<emerald|sky|amber|rose|indigo>'])
  ```
- **Hover Micro-interaction**: `.stat-card:hover` lifts `translateY(-5px)` and applies `filter: drop-shadow(0 4px 6px currentColor); transform: scaleY(1.05)` to the internal sparkline `<canvas>`.
- **Eager Loading**: All dashboard widgets set `protected static bool $isLazy = false;`.

### Chart Widgets
- **Attendance Chart (`AttendanceChartWidget`)**:
  - Type: `'pie'`
  - Palette (`backgroundColor`): `['#22c55e', '#eab308', '#3b82f6', '#ef4444']` corresponding to `[Present, Sick, Excused, Absent]`.
- **Income Chart (`IncomeChartWidget`)**:
  - Type: `'line'`
  - Palette: `borderColor => '#0ea5e9'` (Sky 500), `backgroundColor => 'rgba(14, 165, 233, 0.1)'`.

### Welcome Banner Widgets
- Full-width `<x-filament::section class="g-welcome-section">` placed at the top of each panel dashboard.
- Left column: Emoji greeting (`👋 WELCOME...`), gradient `h2.g-welcome-title`, muted `p.g-welcome-subtitle`.
- Right column: Pill date badge (`.g-welcome-date-badge`) with `x-heroicon-o-calendar` and `now()->translatedFormat('l, d F Y')`.

---

## 6. Strict Guardrails (DO NOT BREAK)

1. **NEVER Remove Render Hooks in PanelProviders**:
   - Every panel MUST register `PanelsRenderHook::HEAD_END` -> `@include("filament.components.animations")` and `PanelsRenderHook::USER_MENU_BEFORE` -> `@include("filament.components.theme-toggle")`. Removing `animations.blade.php` destroys all glassmorphism, floating table rows, pill buttons, stat gradients, and colorful sidebar icons.
2. **NEVER Rely on Uncompiled Tailwind Classes in Custom Filament Views**:
   - Filament panels do NOT load a custom `viteTheme()` by default. Custom classes in `resources/views/filament/**` MUST either use standard Filament utility classes, inline `<style>` blocks (like `.ql-*`, `.stat-*`, `.g-welcome-*`, `.glass-panel`), or explicit CSS rules in `filament/components/animations.blade.php` to prevent Tailwind purge issues.
3. **ALWAYS Provide `.dark` Counterparts for Custom CSS**:
   - Any custom `<style>` block added to a Blade view MUST include `.dark <selector>` rules for background, border, text color, and box-shadow. Never hardcode light-only `#ffffff` backgrounds or `#1f2937` text without a `.dark` override.
4. **PRESERVE Tailwind v4 Setup in `vite.config.js` & CSS Entrypoints**:
   - This project uses `@tailwindcss/vite` with `@import 'tailwindcss';` and `@theme` blocks in `resources/css/app.css` and `resources/css/exam.css`. Do NOT downgrade to Tailwind v3 `tailwind.config.js` syntax or remove `'resources/css/exam.css'` / `'resources/js/exam-engine.js'` from `vite.config.js` inputs.
5. **MAINTAIN Stat Card Extra Attributes**:
   - Every new `Stat::make()` card MUST include `->extraAttributes(['class' => 'stat-card stat-<color>'])` matching its semantic color (`stat-emerald`, `stat-sky`, `stat-amber`, `stat-rose`, `stat-indigo`) so dark mode borders and hover glows remain consistent.
6. **MAINTAIN Sidebar Icon Color Selectors**:
   - When adding a new Filament Resource or Page, register its URL slug in `resources/views/filament/components/animations.blade.php` under `/* Specific Colors based on URL */` with a vibrant hex color and matching `drop-shadow(0 0 4px rgba(..., 0.4))`.
