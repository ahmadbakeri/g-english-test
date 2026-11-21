<?php

namespace Tests\Feature;

use App\Exports\CbtExamResultsExport;
use App\Exports\CbtQuestionTemplateExport;
use App\Filament\Exports\EvaluationExporter;
use App\Filament\Exports\StudentExporter;
use App\Filament\Exports\StudentPaymentExporter;
use App\Filament\Imports\CbtQuestionImporter;
use App\Filament\Pages\AttendanceReport;
use App\Filament\Pages\StudentReport;
use App\Filament\Resources\Finance\StudentPayments\Pages\ManageStudentPayments;
use App\Filament\Resources\HR\StudentResource\Pages\ListStudents;
use App\Imports\CbtQuestionsExcelImport;
use App\Models\AcademicYear;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\CbtAttempt;
use App\Models\CbtExam;
use App\Models\CbtQuestion;
use App\Models\ClassRoom;
use App\Models\Evaluation;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\Subject;
use App\Models\Test;
use App\Models\TestScore;
use App\Models\Tutor;
use App\Models\TutorSalary;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Console\Scheduling\Schedule as ConsoleSchedule;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class GlobalSystemAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected User $adminUser;
    protected Tutor $tutor;
    protected User $tutorUser;
    protected Student $student;
    protected User $studentUser;
    protected User $parentUser;
    protected AcademicYear $academicYear;
    protected Subject $subject;
    protected ClassRoom $classRoom;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->adminUser = User::create([
            'username' => 'global_admin_' . rand(10000, 99999),
            'name' => 'Global Admin',
            'email' => 'global_admin_' . uniqid() . '@g-english.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        if (class_exists(Role::class)) {
            $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
            $this->adminUser->assignRole($role);
        }

        $this->tutor = Tutor::create([
            'nip' => 'NIP-GLB-' . rand(100000, 999999),
            'name' => 'Global Tutor',
            'grade' => 'A',
            'is_active' => true,
        ]);
        $this->tutorUser = $this->tutor->user;

        $parentPhone = '0818' . rand(10000000, 99999999);
        $this->student = Student::create([
            'nis' => 'GES' . rand(300000, 399999),
            'student_type' => 'internal',
            'name' => 'Global Student',
            'gender' => 'L',
            'mother_name' => 'Global Parent',
            'mother_phone' => $parentPhone,
            'is_active' => true,
        ]);
        $this->studentUser = $this->student->user;
        $this->parentUser = $this->student->parents()->firstOrFail();

        $this->academicYear = AcademicYear::create([
            'name' => '2095/2096',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'code' => 'GLB-SUB-' . rand(1000, 9999),
            'name' => 'Global Audit Subject',
            'level' => 'Advanced',
            'is_active' => true,
        ]);

        $this->classRoom = ClassRoom::create([
            'name' => 'Global Class 1',
            'academic_year_id' => $this->academicYear->id,
            'subject_id' => $this->subject->id,
            'tutor_id' => $this->tutor->id,
            'capacity' => 20,
            'is_active' => true,
        ]);

        $this->classRoom->students()->attach($this->student->id);
    }

    public function test_public_and_auth_flows_for_all_four_roles_and_admissions(): void
    {
        // 1. Public Landing & Login views
        $this->get('/')->assertStatus(200)->assertSee('Genza English');
        $this->get('/login')->assertStatus(200);

        // 2. Invalid credentials handling
        $this->from('/login')->post('/login-action', [
            'username' => $this->adminUser->username,
            'password' => 'wrong-password',
        ])->assertRedirect('/login')->assertSessionHasErrors('username');

        // 3. Valid login for all 4 roles + logout
        $roleMap = [
            ['user' => $this->adminUser, 'pass' => 'password123', 'redirect' => '/admin'],
            ['user' => $this->tutorUser, 'pass' => 'password', 'redirect' => '/tutor'],
            ['user' => $this->studentUser, 'pass' => 'password', 'redirect' => '/student'],
            ['user' => $this->parentUser, 'pass' => $this->parentUser->username, 'redirect' => '/parent'],
        ];

        foreach ($roleMap as $entry) {
            $this->post('/login-action', [
                'username' => $entry['user']->username,
                'password' => $entry['pass'],
            ])->assertRedirect($entry['redirect']);

            $this->assertAuthenticatedAs($entry['user']);

            $this->post('/logout-action')->assertRedirect('/');
            $this->assertGuest();
        }

        // 4. Admission Form GET & POST (internal & external, duplicate registration number safety, validation errors)
        $this->get('/pendaftaran-siswa')->assertStatus(200);

        // Validation error check
        $this->from('/pendaftaran-siswa')->post('/daftar', [])
            ->assertRedirect('/pendaftaran-siswa')
            ->assertSessionHasErrors(['name', 'birth_place', 'birth_date', 'gender', 'phone']);

        $basePayload = [
            'birth_place' => 'Tanjung',
            'birth_date' => '2012-01-20',
            'gender' => 'L',
            'religion' => 'Islam',
            'email' => 'adm_test@example.com',
            'phone' => '081200001234',
            'address' => 'Jl. PHM Noor',
            'education_level' => 'SMA',
            'course_level' => 'Advanced',
            'class_name' => '10A',
            'school_name' => 'SMAN 1 Tanjung',
            'mother_name' => 'Ibu Test',
            'mother_phone' => '081200001235',
            'father_name' => 'Ayah Test',
            'father_phone' => '081200001236',
        ];

        // Internal Admission
        $this->post('/daftar', array_merge($basePayload, [
            'name' => 'Applicant Internal One',
            'student_type' => 'internal',
        ]))->assertRedirect(route('landing'));

        // External Admission (also verifies sequential unique registration_number without duplicate collision)
        $this->post('/daftar', array_merge($basePayload, [
            'name' => 'Applicant External Two',
            'student_type' => 'external',
        ]))->assertRedirect(route('landing'));

        $adm1 = Admission::where('name', 'Applicant Internal One')->firstOrFail();
        $adm2 = Admission::where('name', 'Applicant External Two')->firstOrFail();

        $this->assertEquals('internal', $adm1->student_type);
        $this->assertEquals('external', $adm2->student_type);
        $this->assertNotEquals($adm1->registration_number, $adm2->registration_number);
    }

    public function test_all_pdf_reports_and_custom_page_pdf_exports(): void
    {
        $this->actingAs($this->adminUser);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $salary = TutorSalary::create([
            'tutor_id' => $this->tutor->id,
            'month' => (int) now()->month,
            'year' => (int) now()->year,
            'total_jp' => 14,
            'total_amount' => 700000,
            'status' => 'paid',
            'paid_at' => now()->toDateString(),
        ]);

        $payment = StudentPayment::create([
            'student_id' => $this->student->id,
            'month' => (int) now()->month,
            'year' => (int) now()->year,
            'amount' => 300000,
            'due_date' => now()->toDateString(),
            'status' => 'paid',
            'paid_at' => now(),
            'notes' => 'SPP Lunas PDF Test',
        ]);

        $evaluation = Evaluation::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 96,
            'notes' => 'Outstanding performance',
            'date' => now()->toDateString(),
        ]);

        // 1. 4 ReportController PDF endpoints
        $pdfRoutes = [
            route('report.tutor-salary', $salary->id),
            route('report.student-payment', $payment->id),
            route('report.certificate', $this->student->id),
            route('report.evaluation', $evaluation->id),
        ];

        foreach ($pdfRoutes as $url) {
            $res = $this->get($url);
            $res->assertStatus(200);
            $this->assertStringContainsString('application/pdf', (string) $res->headers->get('Content-Type'));
        }

        // 2. AttendanceReport & StudentReport PDF downloads via Livewire
        Attendance::create([
            'role_type' => 'student',
            'class_id' => $this->classRoom->id,
            'student_id' => $this->student->id,
            'user_id' => $this->studentUser->id,
            'date' => now()->toDateString(),
            'status' => 'present',
            'time_in' => '08:00:00',
        ]);

        $test = Test::create([
            'class_id' => $this->classRoom->id,
            'subject_id' => $this->subject->id,
            'name' => 'Midterm Test 1',
            'type' => 'midterm',
            'date' => now()->toDateString(),
            'max_score' => 100,
        ]);

        TestScore::create([
            'test_id' => $test->id,
            'student_id' => $this->student->id,
            'score' => 91,
        ]);

        Livewire::test(AttendanceReport::class)
            ->set('classId', $this->classRoom->id)
            ->set('month', now()->month)
            ->set('year', now()->year)
            ->call('generateReport')
            ->assertNotSet('reportData', [])
            ->call('exportPdf')
            ->assertFileDownloaded();

        Livewire::test(StudentReport::class)
            ->set('classId', $this->classRoom->id)
            ->call('generateReport')
            ->assertNotSet('reportData', [])
            ->call('exportPdf')
            ->assertFileDownloaded();
    }

    public function test_excel_template_chunked_import_and_exporters(): void
    {
        $this->actingAs($this->adminUser);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $exam = CbtExam::create([
            'title' => 'Global Excel CBT Exam',
            'class_id' => $this->classRoom->id,
            'subject_id' => $this->subject->id,
            'cbt_mode' => 'internal_excel',
            'duration_minutes' => 60,
            'max_attempts' => 2,
            'is_active' => true,
        ]);

        // 1. CBT Question Template Export & Chunked Excel Import
        $templateExport = new CbtQuestionTemplateExport();
        $this->assertEquals(
            ['no', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_answer', 'points'],
            $templateExport->headings()
        );

        $filePath = 'cbt_imports/global_audit_questions.xlsx';
        Excel::store($templateExport, $filePath, 'local');
        $fullPath = Storage::disk('local')->path($filePath);

        $importer = new CbtQuestionsExcelImport($exam->id);
        $this->assertEquals(50, $importer->chunkSize());
        Excel::import($importer, $fullPath);

        $this->assertEquals(2, $importer->importedCount);
        $this->assertEquals(2, CbtQuestion::where('cbt_exam_id', $exam->id)->count());
        Storage::disk('local')->delete($filePath);

        // 2. CBT Exam Results Export
        CbtAttempt::create([
            'cbt_exam_id' => $exam->id,
            'student_id' => $this->student->id,
            'attempt_number' => 1,
            'start_time' => now()->subMinutes(30),
            'end_time' => now(),
            'status' => 'finished',
            'is_completed' => true,
            'score' => 95,
            'sync_status' => 'internal',
        ]);

        $resultsExport = new CbtExamResultsExport($exam->id);
        $this->assertCount(1, $resultsExport->collection());

        // 3. Filament Exporters & Importers column definitions
        $this->assertNotEmpty(StudentExporter::getColumns());
        $this->assertNotEmpty(StudentPaymentExporter::getColumns());
        $this->assertNotEmpty(EvaluationExporter::getColumns());
        $this->assertNotEmpty(CbtQuestionImporter::getColumns());
    }

    public function test_avatar_proxy_security_and_webpush_subscription_lifecycle(): void
    {
        Storage::fake('r2');
        Storage::disk('r2')->put('avatars/test-avatar.png', 'fake-png-bytes');

        // 1. Valid path
        $this->get(route('avatar.proxy', ['path' => 'avatars/test-avatar.png']))
            ->assertStatus(200);

        // 2. Missing path fallback
        $this->get(route('avatar.proxy'))
            ->assertStatus(404);
        $this->get(route('avatar.proxy', ['path' => 'avatars/non-existent.png']))
            ->assertStatus(404);

        // 3. Path traversal protection
        $this->get(route('avatar.proxy', ['path' => '../../.env']))
            ->assertStatus(404);
        $this->get(route('avatar.proxy', ['path' => '/etc/passwd']))
            ->assertStatus(404);

        // 4. WebPush Subscribe & Unsubscribe
        $this->actingAs($this->parentUser);
        $endpoint = 'https://fcm.googleapis.com/fcm/send/global-' . uniqid();

        $this->postJson('/api/push/subscribe', [
            'endpoint' => $endpoint,
            'keys' => [
                'p256dh' => 'BNcRdreALRFXTkOOUHK1EtK2wtaz5Ry4YfYCA_0QTpQtUbVlUls0VJXg7A8u-Ts1XbjhazAkj7I99e8QcYP7DkM',
                'auth' => 'tBHItJI5svbpez7KI4CCXg',
            ],
        ])->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $this->parentUser->id,
            'endpoint' => $endpoint,
        ]);

        $this->postJson('/api/push/unsubscribe', [
            'endpoint' => $endpoint,
        ])->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseMissing('push_subscriptions', [
            'subscribable_id' => $this->parentUser->id,
            'endpoint' => $endpoint,
        ]);
    }

    public function test_artisan_route_list_gform_sync_command_and_scheduler(): void
    {
        // 1. Verify route:list runs with 0 errors
        $exitCode = Artisan::call('route:list');
        $this->assertEquals(0, $exitCode);

        // 2. Verify cbt:sync-gform command with Http::fake() CSV response & deduplication
        $csvUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-FakeSheet/pub?output=csv';
        $gformExam = CbtExam::create([
            'title' => 'Global GForm Sync Exam',
            'class_id' => $this->classRoom->id,
            'subject_id' => $this->subject->id,
            'cbt_mode' => 'google_form',
            'gform_embed_url' => 'https://docs.google.com/forms/d/e/1FAIpQLSeFake/viewform?embedded=true',
            'gform_sheet_csv_url' => $csvUrl,
            'gform_score_column' => 'Skor',
            'duration_minutes' => 60,
            'max_attempts' => 1,
            'is_active' => true,
        ]);

        $csvBody = implode("\n", [
            'Timestamp,Skor,NIS,Nama Lengkap',
            "2026/11/21 10:00:00,85 / 100,{$this->student->nis},{$this->student->name}",
        ]);

        Http::fake([
            $csvUrl => Http::response($csvBody, 200),
        ]);

        // Run first sync
        $this->artisan('cbt:sync-gform', ['--exam_id' => $gformExam->id])
            ->assertExitCode(0);

        $attempt = CbtAttempt::where('cbt_exam_id', $gformExam->id)
            ->where('student_id', $this->student->id)
            ->firstOrFail();

        $this->assertEquals(85.00, (float) $attempt->score);
        $this->assertEquals('finished', $attempt->status);
        $this->assertEquals('synced', $attempt->sync_status);
        $this->assertNotNull($attempt->external_submission_id);

        // Run second sync with identical CSV to verify deduplication (still 1 record)
        $this->artisan('cbt:sync-gform', ['--exam_id' => $gformExam->id])
            ->assertExitCode(0);

        $this->assertEquals(
            1,
            CbtAttempt::where('cbt_exam_id', $gformExam->id)
                ->where('student_id', $this->student->id)
                ->count()
        );

        // 3. Verify console schedule definitions
        $schedule = app(ConsoleSchedule::class);
        $events = collect($schedule->events())->map(fn ($e) => $e->command ?? $e->description ?? '')->implode(' | ');

        $this->assertStringContainsString('queue:work --stop-when-empty --max-time=50 --tries=3', $events);
        $this->assertStringContainsString('cbt:sync-gform', $events);
    }
}
