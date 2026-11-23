# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users
- **Academy Admins (`admin`):** Manage tutors, employees, internal & external students, academic schedules, SPP/honors, CBT exams, and Brain Champion delegations.
- **Tutors (`tutor`):** Manage class schedules, QR attendance, teaching journals, study materials, CBT exams, and view monthly salary slips.
- **Internal Students (`siswa`):** View schedules, scan QR attendance, read study materials, track learning journals & grades, and take fullscreen anti-cheat CBT exams.
- **Parents (`parent`):** Monitor child attendance history, learning journals, and payment receipts.
- **School Coordinators (`school`):** Register and manage Brain Champion student delegations (`SD/MI` Grades 4–6 & `SMP/MTs` Grades 7–9), bulk import/export Excel, and print QR participant passes.
- **Education Agencies (`kemenag`, `kemendikdasmen`):** Read-only monitoring of participating schools and students automatically classified by institution type (`MI`/`MTs` → Kemenag, `SD`/`SMP` → Kemendikdasmen).

## Product Purpose
G-English unifies daily English course administration (attendance, journals, payroll, billing, CBT) with public event management for **G-English Brain Champion ("One Mind, One Champion")** in one low-footprint web platform.

## Positioning
Single-codebase Laravel + Filament platform that separates internal course operations from external competition delegations, enforces strict per-grade/per-school competition quotas, auto-classifies schools for Kemenag and Kemendikdasmen oversight, and runs reliably on a 1GB RAM VPS.

## Operating Context
- **Daily Academy:** 2 internal students, 4 tutors, QR attendance, teaching journals, and CBT quizzes (internal question bank & Google Form embed mode with 3-violation fullscreen lock).
- **Competition Event:** Public registration (`/brain-champion`), 35 participants max per grade (Grades 4–9), offline whiteboard/paper knockout format with printable QR participant cards, and queued email credentials (`database` queue via Supervisor).
- **Infrastructure:** Ubuntu 24.04 LTS, Nginx, PHP 8.3-FPM, MySQL 8.0, 2GB swap, Supervisor.

## Capabilities and Constraints
- **8 Active Locales:** `en`, `id`, `zh_CN`, `zh_PY`, `ja`, `ja_RM`, `ar`, `ar_RM` via `lang/*.json`.
- **Light & Dark Mode:** Class-based `.dark` theme toggle persisted in `localStorage`.
- **Username Conventions:** School coordinators use `48xxx` (`48001`); external students use `48{school}{grade}{seq}` (`48001401`); internal students use `481001+`.
- **No Unverified Claims:** Deployment metrics, testimonials, or external partnerships must not be fabricated.

## Brand Commitments
- **Name:** G-English & G-English Brain Champion ("One Mind, One Champion").
- **Primary Brand Colors:** Genza Blue (`#0a4282`) and Genza Gold (`#fbb900`) with `Outfit` display/UI typography.

## Evidence on Hand
- Design tokens and component rules in `docs/DESIGN_SYSTEM.md` and `resources/views/filament/components/animations.blade.php`.
- Live competition registration in `app/Livewire/BrainChampionRegistration.php` and CBT engine in `app/Filament/Student/Pages/TakeCbtExam.php`.
- Absent: real third-party testimonials or external student competition scores (competition is offline whiteboard/paper).

## Product Principles
1. **Operate First on Internal Surfaces:** Clarity, keyboard/touch scanability, and tabular numeric alignment outrank decorative ornament in all 6 Filament panels.
2. **Strict Role Isolation:** Each user role sees only its own scoped data and workflows.
3. **Low-RAM Discipline:** Keep client assets lean, avoid heavy runtime bloat, and queue background email jobs cleanly.
4. **Full Localization & Theme Parity:** Every new label uses `__('...')` and works cleanly in both light and dark modes.
