# G-English Product Context (`PRODUCT.md`)

## Product Overview
**G-English** is an integrated English Course & Competition Management Platform built on Laravel 13, Filament v5, and Livewire v4. It serves both daily internal tutoring operations and external school competition events (**G-English Brain Champion — "One Mind, One Champion"**).

## Target Users & Surfaces (6 Filament Panels + Public Pages)
1. **Public Surfaces (`/`, `/brain-champion`, `/pendaftaran-siswa`, `/login`):**
   - Mode: **Persuade** — Landing page, student admission, and Brain Champion competition registration (SD/MI Grades 4–6 & SMP/MTs Grades 7–9, 35-slot per-grade cap, Excel template download/upload).
2. **Admin Panel (`/admin`):**
   - Mode: **Operate** — Full academic, HR (tutors, employees), student/external participant management, finance (SPP, tutor honor/salary), journals, CBT exam builder & monitoring, and Brain Champion roster/card PDFs.
3. **Tutor Panel (`/tutor`):**
   - Mode: **Operate** — Daily teaching schedules, QR attendance, teaching journals, study materials, CBT management, and payslips.
4. **Student Panel (`/student`):**
   - Mode: **Operate** — Class schedules, QR attendance, learning journals, study materials, grade reports, and fullscreen anti-cheat CBT exams.
5. **Parent Panel (`/parent`):**
   - Mode: **Operate** — Child attendance history, learning journals, and payment receipts.
6. **School Coordinator Panel (`/school`):**
   - Mode: **Operate** — School delegation management, live quota overview, Excel bulk import/export, and QR participant pass printing.
7. **Agency Monitoring Panel (`/agency` — Kemenag & Kemendikdasmen):**
   - Mode: **Operate (Read-Only)** — Automatic school classification (`MI`/`MTs` → Kemenag, `SD`/`SMP` → Kemendikdasmen), participating schools overview, and participant list.

## Constraints & Technical Realities
- **Localization:** 8 active locales (`en`, `id`, `zh_CN`, `zh_PY`, `ja`, `ja_RM`, `ar`, `ar_RM`). All UI labels must use `__('...')`.
- **Theme:** Full Light & Dark mode (`.dark` class) across all panels and public pages.
- **Deployment Target:** 1GB RAM / 2GB Swap Ubuntu 24.04 VPS (`database` queue via Supervisor). Keep client assets lean and avoid heavy runtime dependencies.
