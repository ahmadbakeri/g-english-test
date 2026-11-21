# Current Product Requirements Document (`docs/PRD_CURRENT.md`)

## 1. Existing Features by Role & Module

### Public & Authentication
- **Landing Page (`welcome.blade.php`)**: Marketing hero, program benefits, Survival English curriculum overview, WhatsApp CTA, light/dark mode toggle.
- **Online Student Admission (`AdmissionController`, `admission.blade.php`, `livewire/admission-form.blade.php`)**:
  - Public registration form (`/pendaftaran-siswa`, `POST /daftar`) generating unique `REG-YYYY-XXXX` numbers.
  - Captures student biodata, education/course level, father/mother contact & occupation, and fee/payment preferences (`full` vs `installment`, discounts).
- **Unified Login (`login.blade.php`, `Auth\LoginController`)**:
  - Username + password login (`POST /login-action`) with role-based redirect (`admin` -> `/admin`, `tutor` -> `/tutor`, `parent` -> `/parent`, `siswa` -> `/student`).

### Admin Panel (`/admin`)
- **Dashboard & Analytics**:
  - Welcome banner, Quick Menu grid, Stats Overview (Active Students, Monthly Income, Total Tutors & Staff), Monthly Attendance Pie Chart, Yearly Income Line Chart, Today's Class Schedule, Today's Access Activity Log.
- **Administration & HR**:
  - **Admissions**: Review applications, manage tuition/installment breakdowns, approve/convert applicants into enrolled Students.
  - **Students (`StudentResource`)**: CRUD with auto-provisioned student `User` (`role='siswa'`, username `481001+`) and auto-provisioned father/mother parent `User` accounts (`role='parent'`, username = phone number) linked via `student_parent`, QR code modal, Excel export, and R2 avatar cropping.
  - **Tutors (`TutorResource`)**: CRUD with auto-provisioned tutor `User` (`role='tutor'`, username `48001+`), specialization, grade (`A`/`B`/`C`), QR code modal.
  - **Users & Shield Roles (`UserResource`, `FilamentShield`)**: Manage accounts and Spatie role/permission assignments.
- **Academic & Daily Operations**:
  - **Master Data**: Academic Years (`AcademicYearResource`), Semesters (`SemesterResource`), Subjects (`SubjectResource`), ClassRooms (`ClassRoomResource` with student enrollment pivot), JSON Class Schedules (`ClassSchedule`), and Date Schedules (`Schedule`).
  - **Study Materials (`StudyMaterialResource`)**: Upload/manage YouTube videos, mindmap images, and PDF attachments per subject.
  - **QR Attendance & Logs (`ScanAttendance`, `AttendanceResource`)**: Live webcam `jsQR` scanner supporting Student, Tutor, and Employee check-in/out (`time_in`, `time_out`) + manual attendance CRUD.
  - **Teaching Journals & Validations (`JournalResource`, `JournalValidationResource`)**: Tutors log topics, materials, JP count, photos, and links; Admins approve/reject journals (auto-updating journal status).
- **Evaluation & CBT**:
  - **Manual Evaluations & Honors (`EvaluationResource`, `HonorResource`)**: Score logging per subject, student achievements/certificates (`printCertificate`, `printEvaluation`).
  - **CBT Exam Management (`CbtExamResource`, `CbtAttemptResource`)**: Create exams, import/export questions (`CbtQuestionImporter`/`Exporter`), configure duration, max attempts, shuffle, anti-cheat violation limits, and monitor/unlock student attempts.
- **Accounting & Reports**:
  - **Student Payments (`StudentPaymentResource`)**: Monthly/installment tuition billing, mark paid, print receipt PDF (`report.student-payment`), Excel export.
  - **Tutor Salaries (`TutorSalaryResource`)**: Auto-calculate JP from validated journals (26th previous month to 25th current month) multiplied by grade rate, mark paid, print salary slip PDF (`report.tutor-salary`).
  - **Expenses (`ExpenseResource`)**: Track operational expenses.
  - **Custom Reports**: Monthly Attendance Matrix + PDF (`AttendanceReport`), Student Raport Recap + PDF (`StudentReport`), Monthly Income/Expense/Profit Summary (`FinancialReport`).
- **System**: General settings (`SettingResource`) and activity monitoring.

### Tutor Panel (`/tutor`)
- **Dashboard**: Tutor welcome banner, stats (Active Classes, Total Students, Journals This Month), Today's teaching schedule.
- **Teaching Operations**: QR Attendance Scanner (`ScanAttendance`), Teaching Journals (`JournalResource`), Class Schedules (`ScheduleResource`), Study Materials (`StudyMaterialResource`).
- **Assessments**: Manage CBT Exams & Questions (`CbtExamResource`), log Student Evaluations (`EvaluationResource`).
- **Payroll**: View personal monthly salary slips & JP totals (`TutorSalaryResource`).

### Student Panel (`/student`)
- **Dashboard**: Student welcome banner, stats (Registered Classes, Monthly Attendance %, Latest CBT/Manual Score), Today's class schedule.
- **Learning & Schedules**: View enrolled schedules & journals (`StudentJournals`), browse & view/download Study Materials (`StudyMaterialsPage`, `ViewStudyMaterial`: embedded YouTube, mindmaps, files).
- **CBT Exams (`StudentCbtExams`, `TakeCbtExam`, `Cbt\ExamController`, `Cbt\ExamApiController`)**:
  - Take timed exams with question/option shuffling, image/audio attachments, question navigation grid, and tab-switch anti-cheat countdown modal (auto-cancels/submits on violation limit).
- **Grades & Finance**:
  - **Grade Report (`GradeReport`)**: Subject report cards calculating `Final Grade = (Attendance * 40%) + (CBT Average * 60%)` with evaluation breakdown.
  - **Attendance & Tuition**: View personal attendance history (`AttendanceResource`) and tuition payment status/receipts (`StudentPaymentResource`).

### Parent Panel (`/parent`)
- **Dashboard**: Parent welcome banner listing linked children, stats (Total Children, Monthly Attendance Count).
- **Web Push Notifications (`PushNotificationWidget`, `PushSubscriptionController`)**: Subscribe browser to VAPID WebPush notifications for real-time child check-in/check-out alerts.
- **Monitoring**: Child Attendance history table (`ChildAttendanceWidget`), Teaching Journals for children's classes (`JournalResource`), and Student Tuition Payments & PDF receipts (`StudentPaymentResource`).

---

## 2. Active Routes (`routes/web.php`)

- `GET /` -> `welcome` view (`landing`)
- `GET /login` -> `login` view (`login`)
- `POST /login-action` -> Role-based auth redirect (`login.action`)
- `POST /logout-action` -> Session logout (`logout.action`)
- `GET /pendaftaran-siswa` -> `AdmissionController@create` (`admission`)
- `POST /daftar` -> `AdmissionController@store` (`admission.store`)
- `GET /reports/tutor-salary/{id}` -> `ReportController@printTutorSalary` (`report.tutor-salary`, `auth`)
- `GET /reports/student-payment/{id}` -> `ReportController@printStudentPayment` (`report.student-payment`, `auth`)
- `GET /reports/certificate/{id}` -> `ReportController@printCertificate` (`report.certificate`, `auth`)
- `GET /reports/evaluation/{id}` -> `ReportController@printEvaluation` (`report.evaluation`, `auth`)
- `GET /avatar-proxy` -> Cloudflare R2 avatar stream proxy (`avatar.proxy`, `web`)
- **Filament Panel Prefixes**: `/admin`, `/tutor`, `/student`, `/parent` (Note: `routes/api.php` is not present; controllers `PushSubscriptionController` and `Cbt\ExamController` / `Cbt\ExamApiController` exist in `app/Http/Controllers/`).

---

## 3. Installed Packages (`composer.json`)

### Production (`require`)
- `php`: `^8.3`
- `laravel/framework`: `^13.8`
- `filament/filament`: `^5.6`
- `bezhansalleh/filament-shield`: `^4.2` (Role & permission UI for Filament)
- `spatie/laravel-permission`: `^7.4` (RBAC engine)
- `bezhansalleh/filament-language-switch`: `*` (Multi-language switcher)
- `barryvdh/laravel-dompdf`: `^3.1` (PDF generation for receipts, salaries, certificates, raports)
- `maatwebsite/excel`: `^3.1` & `pxlrbt/filament-excel`: `^3.6` (Excel import/export)
- `simplesoftwareio/simple-qrcode`: `*` (Student/Tutor QR code generation)
- `laravel-notification-channels/webpush`: `^11.0` (Parent browser push notifications)
- `league/flysystem-aws-s3-v3`: `3.0` (Cloudflare R2 / S3 storage)
- `predis/predis`: `^3.5` (Redis client)
- `laravel/tinker`: `^3.0`

### Development (`require-dev`)
- `fakerphp/faker`: `^1.23`
- `laravel/pail`: `^1.2.5`
- `laravel/pao`: `^1.0.6`
- `laravel/pint`: `^1.27`
- `mockery/mockery`: `^1.6`
- `nunomaduro/collision`: `^8.6`
- `phpunit/phpunit`: `^12.5.12`
