# Product Requirements Document V2 (Dual-Mode CBT + 1GB RAM VPS)

## 0. Design System Compliance (MANDATORY)
- Strictly follow `docs/DESIGN_SYSTEM.md`.
- NEVER modify or remove `PanelsRenderHook::HEAD_END` (`filament.components.animations`) or `PanelsRenderHook::USER_MENU_BEFORE` (`filament.components.theme-toggle`) in any `PanelProvider`.
- Keep all existing vibrant colors (`#0a4282`, `#fbb900`, `emerald`, `sky`, `amber`, `rose`), `Outfit` font, floating table rows, pill buttons, dashboard charts (`AttendanceChartWidget`, `IncomeChartWidget`), and `.dark` mode overrides intact.

## 1. Infrastructure & Low-Memory Rules (VPS 1 vCPU, 1GB RAM)
- **Drivers**: Set `CACHE_STORE=file`, `SESSION_DRIVER=file`, `QUEUE_CONNECTION=database` in `.env.example`. Do NOT require Redis.
- **Polling**: Remove or set any `wire:poll` interval to `>= 30s` across all 4 panels (`/admin`, `/tutor`, `/student`, `/parent`).
- **Scheduler (`routes/console.php`)**:
  - Run `queue:work --stop-when-empty --max-time=50 --tries=3` every minute (`withoutOverlapping()`).
  - Run `cbt:sync-gform` every 3 minutes (`withoutOverlapping()`).

## 2. Dual-Mode CBT Creation (Admin & Tutor `CbtExamResource`)
When creating or editing an exam in `CbtExamResource` (`/admin` & `/tutor`), Admin/Tutor chooses `cbt_mode` via reactive ToggleButtons/Radio:

### Mode A: Google Form (`cbt_mode = 'google_form'`)
- **Fields Shown**: `gform_embed_url` (Required URL), `gform_sheet_csv_url` (Optional CSV URL), `gform_nis_entry_id`, `gform_name_entry_id`, `gform_score_column` (default `'Skor'`).
- **Hide**: `cbt_questions` relation manager/tab when `cbt_mode == 'google_form'`.
- **Actions**: Add "Sync Nilai GForm" action button (colored `info` with `heroicon-o-arrow-path`) to run score sync immediately.

### Mode B: Real CBT + Excel Question Upload (`cbt_mode = 'internal_excel'`)
- **Actions**:
  - **"Download Template Excel (.xlsx)"** (`color('success')`, `heroicon-o-arrow-down-tray`): generates `.xlsx` with columns `no`, `question_text`, `option_a`, `option_b`, `option_c`, `option_d`, `option_e`, `correct_answer` (`A`-`E`), `points`.
  - **"Import Soal Excel (.xlsx)"** (`color('primary')`, `heroicon-o-arrow-up-tray`): uses `maatwebsite/excel` (`WithChunkReading` 50 rows) to import into `cbt_questions` for that `cbt_exam_id`.
- **Relation Manager**: Show `cbt_questions` manager so Tutor/Admin can inspect or edit imported questions.

## 3. Student CBT Exam Taker Behavior (`/student`)
- **If `cbt_mode == 'google_form'`**:
  - Do NOT query `cbt_questions`.
  - Construct pre-filled `<iframe>` URL appending `&{gform_nis_entry_id}={student->nis}&{gform_name_entry_id}={urlencode(student->name)}` to `gform_embed_url`.
  - Render inside a `.glass-panel` container (`rounded-2xl`, `.dark` mode compliant) with a pure client-side **Alpine.js countdown timer** (`ui-monospace`) based on `duration_minutes`. Hide `<iframe>` and show time-up notice at `00:00`.
  - Upsert `cbt_attempts` (`status = 'in_progress'`, `sync_status = 'pending'`) once on open.
- **If `cbt_mode == 'internal_excel'` (200-User Low-RAM Optimization)**:
  - Cache exam questions via `Cache::remember("exam_q_{$exam->id}", 3600, ...)` (`file` store) so 200 students don't repeatedly query MariaDB.
  - Store selected answers (`A`-`E`) and countdown timer in browser `localStorage` via Alpine.js / `exam-engine.js` (zero per-click network requests).
  - Submit all answers in **one single payload** on "Selesai / Submit Ujian" (or timeout), calculate score, and save to `cbt_answers` + `cbt_attempts` (`status = 'finished'`, `sync_status = 'internal'`).

## 4. Automated Google Form Score Sync (`app/Console/Commands/SyncGformScores.php`)
- Fetch published CSV for active `google_form` exams (`Http::timeout(15)->get(...)`), process in chunks of 50 rows.
- Match `students` by `nis`, compute `external_submission_id = md5($exam->id . $timestamp . $nis)`, and `updateOrCreate` `cbt_attempts` (`score`, `status = 'finished'`, `sync_status = 'synced'`). Update `last_synced_at`.

## 5. Student Segmentation & QR Attendance Hardening
- **Students & Admissions**: Add `student_type` (`internal` / `external`) select field, badge (`info` for `internal`, `warning` for `external`), and filter tab in `StudentResource`, `AdmissionResource`, and `AdmissionController`. Carry `student_type` when converting Admission -> Student.
- **QR Attendance**: Reject duplicate scans on the same `(date, role_type, student_id/tutor_id)` and dispatch WebPush notifications asynchronously via `database` queue.
