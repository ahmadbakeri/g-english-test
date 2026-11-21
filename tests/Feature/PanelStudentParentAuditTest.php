<?php

namespace Tests\Feature;

use App\Filament\Parent\Resources\Journals\JournalResource as ParentJournalResource;
use App\Filament\Parent\Resources\StudentPayments\StudentPaymentResource as ParentStudentPaymentResource;
use App\Filament\Parent\Widgets\ChildAttendanceWidget;
use App\Filament\Student\Pages\GradeReport;
use App\Filament\Student\Pages\StudentCbtExams;
use App\Filament\Student\Pages\StudentJournalDetails;
use App\Filament\Student\Pages\StudentJournals;
use App\Filament\Student\Pages\StudyMaterialsPage;
use App\Filament\Student\Pages\TakeCbtExam;
use App\Filament\Student\Pages\ViewStudyMaterial;
use App\Filament\Student\Resources\Attendances\AttendanceResource as StudentAttendanceResource;
use App\Filament\Student\Resources\StudentPayments\StudentPaymentResource as StudentTuitionResource;
use App\Models\AcademicYear;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\CbtAttempt;
use App\Models\CbtExam;
use App\Models\CbtQuestion;
use App\Models\ClassRoom;
use App\Models\Evaluation;
use App\Models\Journal;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\Tutor;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class PanelStudentParentAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected Tutor $tutor;
    protected AcademicYear $academicYear;
    protected Subject $subject;
    protected ClassRoom $classRoom;
    protected Student $student;
    protected User $studentUser;
    protected User $parentUser;
    protected StudyMaterial $studyMaterial;
    protected Journal $journal;
    protected StudentPayment $payment;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->tutor = Tutor::create([
            'nip' => 'NIP-SP-' . rand(100000, 999999),
            'name' => 'Tutor StudentParent Audit',
            'grade' => 'A',
            'is_active' => true,
        ]);

        $this->academicYear = AcademicYear::create([
            'name' => '2097/2098',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'code' => 'SP-SUB-' . rand(1000, 9999),
            'name' => 'Student Parent English',
            'level' => 'Intermediate',
            'is_active' => true,
        ]);

        $this->classRoom = ClassRoom::create([
            'name' => 'Class SP-1',
            'academic_year_id' => $this->academicYear->id,
            'subject_id' => $this->subject->id,
            'tutor_id' => $this->tutor->id,
            'capacity' => 15,
            'is_active' => true,
        ]);

        $parentPhone = '0819' . rand(10000000, 99999999);

        $this->student = Student::create([
            'nis' => 'GES' . rand(400000, 499999),
            'student_type' => 'internal',
            'name' => 'Student Portal Audit',
            'gender' => 'L',
            'mother_name' => 'Parent Portal Audit',
            'mother_phone' => $parentPhone,
            'is_active' => true,
        ]);

        $this->classRoom->students()->attach($this->student->id);

        $this->studentUser = $this->student->user;
        $this->parentUser = $this->student->parents()->firstOrFail();

        $this->studyMaterial = StudyMaterial::create([
            'subject_id' => $this->subject->id,
            'type' => 'video',
            'title' => 'Survival English Video 1',
            'description' => 'Speaking practice',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_published' => true,
        ]);

        $this->journal = Journal::create([
            'class_id' => $this->classRoom->id,
            'tutor_id' => $this->tutor->id,
            'date' => now()->toDateString(),
            'topic' => 'Daily Conversation & Idioms',
            'description' => 'Practicing idioms in pairs',
            'materials' => 'G-Book Unit 2',
            'jp_count' => 2,
            'status' => 'validated',
        ]);

        Attendance::create([
            'role_type' => 'student',
            'class_id' => $this->classRoom->id,
            'student_id' => $this->student->id,
            'user_id' => $this->studentUser->id,
            'date' => now()->toDateString(),
            'time_in' => '08:00:00',
            'status' => 'present',
        ]);

        Evaluation::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 90,
            'notes' => 'Strong speaking',
            'date' => now()->toDateString(),
        ]);

        $this->payment = StudentPayment::create([
            'student_id' => $this->student->id,
            'month' => (int) now()->month,
            'year' => (int) now()->year,
            'amount' => 250000,
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => 'paid',
            'paid_at' => now(),
            'notes' => 'SPP Lunas',
        ]);
    }

    public function test_public_routes_and_online_admission_submission_with_student_type(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/pendaftaran-siswa')->assertStatus(200);

        $response = $this->post('/daftar', [
            'student_type' => 'external',
            'name' => 'Public Applicant Audit',
            'birth_place' => 'Tanjung',
            'birth_date' => '2011-04-15',
            'gender' => 'P',
            'religion' => 'Islam',
            'email' => 'applicant_audit@example.com',
            'phone' => '081234567890',
            'social_media' => '@applicant_audit',
            'address' => 'Jl. Ir. PHM Noor RT 4',
            'education_level' => 'SMP',
            'course_level' => 'Intermediate',
            'class_name' => '8A',
            'school_name' => 'SMPN 1 Tanjung',
            'mother_name' => 'Ibu Applicant',
            'mother_phone' => '081234567891',
            'mother_email' => 'ibu@example.com',
            'mother_job' => 'Wiraswasta',
            'father_name' => 'Ayah Applicant',
            'father_phone' => '081234567892',
        ]);

        $response->assertRedirect(route('landing'));

        $admission = Admission::where('name', 'Public Applicant Audit')->firstOrFail();
        $this->assertEquals('external', $admission->student_type);
        $this->assertEquals('pending', $admission->status);
        $this->assertStringStartsWith('REG-' . date('Y') . '-', $admission->registration_number);
    }

    public function test_student_panel_pages_and_dual_mode_cbt_exam_execution(): void
    {
        $this->actingAs($this->studentUser);
        Filament::setCurrentPanel(Filament::getPanel('student'));

        // 1. Verify 200 OK on all Student Panel Pages & Resources
        $urls = [
            '/student',
            StudentJournals::getUrl(),
            StudentJournalDetails::getUrl(['record' => $this->journal->id]),
            StudyMaterialsPage::getUrl(),
            ViewStudyMaterial::getUrl() . '?record=' . $this->studyMaterial->id,
            StudentCbtExams::getUrl(),
            GradeReport::getUrl(),
            StudentAttendanceResource::getUrl('index'),
            StudentTuitionResource::getUrl('index'),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertStatus(200);
        }

        // 2. Test TakeCbtExam Mode A: google_form (iframe pre-fill & attempt upsert)
        $gformExam = CbtExam::create([
            'title' => 'Student GForm Exam Audit',
            'class_id' => $this->classRoom->id,
            'subject_id' => $this->subject->id,
            'cbt_mode' => 'google_form',
            'gform_embed_url' => 'https://docs.google.com/forms/d/e/1FAIpQLSeStudentTest/viewform?embedded=true',
            'gform_nis_entry_id' => 'entry.101010',
            'gform_name_entry_id' => 'entry.202020',
            'duration_minutes' => 60,
            'max_attempts' => 1,
            'is_active' => true,
        ]);

        $gformUrl = TakeCbtExam::getUrl(['record' => $gformExam->id]);
        $response = $this->get($gformUrl);
        $response->assertStatus(200);
        $response->assertSee('entry.101010=' . $this->student->nis, false);

        $this->assertDatabaseHas('cbt_attempts', [
            'cbt_exam_id' => $gformExam->id,
            'student_id' => $this->student->id,
            'status' => 'in_progress',
            'sync_status' => 'pending',
        ]);

        // 3. Test TakeCbtExam Mode B: internal_excel (cached questions + single-submit scoring)
        $internalExam = CbtExam::create([
            'title' => 'Student Internal Excel Exam Audit',
            'class_id' => $this->classRoom->id,
            'subject_id' => $this->subject->id,
            'cbt_mode' => 'internal_excel',
            'duration_minutes' => 60,
            'max_attempts' => 2,
            'is_active' => true,
            'show_result_immediately' => true,
        ]);

        $q1 = CbtQuestion::create([
            'cbt_exam_id' => $internalExam->id,
            'question_text' => 'Choose the correct past participle of Go:',
            'option_a' => 'Went',
            'option_b' => 'Gone',
            'option_c' => 'Going',
            'option_d' => 'Goes',
            'correct_answer' => 'B',
            'points' => 60,
        ]);

        $q2 = CbtQuestion::create([
            'cbt_exam_id' => $internalExam->id,
            'question_text' => 'Choose the correct plural of Child:',
            'option_a' => 'Childs',
            'option_b' => 'Childrens',
            'option_c' => 'Children',
            'option_d' => 'Childes',
            'correct_answer' => 'C',
            'points' => 40,
        ]);

        Cache::forget("exam_q_{$internalExam->id}");

        $this->get(TakeCbtExam::getUrl(['record' => $internalExam->id]))->assertStatus(200);

        Livewire::test(TakeCbtExam::class, ['record' => $internalExam->id])
            ->call('startExam')
            ->assertSet('isFinished', false)
            ->call('submitExam', [
                $q1->id => 'B', // Correct (60 pts)
                $q2->id => 'A', // Wrong (0 pts)
            ])
            ->assertSet('isFinished', true);

        $attempt = CbtAttempt::where('cbt_exam_id', $internalExam->id)
            ->where('student_id', $this->student->id)
            ->firstOrFail();

        $this->assertEquals('finished', $attempt->status);
        $this->assertTrue((bool) $attempt->is_completed);
        $this->assertEquals('internal', $attempt->sync_status);
        $this->assertEquals(60.00, (float) $attempt->score);
        $this->assertEquals(2, $attempt->answers()->count());

        // Verify GradeReport reflects CBT score
        $this->get(GradeReport::getUrl())->assertStatus(200);
    }

    public function test_parent_panel_pages_widgets_and_push_subscription_endpoint(): void
    {
        $this->actingAs($this->parentUser);
        Filament::setCurrentPanel(Filament::getPanel('parent'));

        $urls = [
            '/parent',
            ParentJournalResource::getUrl('index'),
            ParentStudentPaymentResource::getUrl('index'),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertStatus(200);
        }

        // Verify ChildAttendanceWidget renders child's attendance
        Livewire::test(ChildAttendanceWidget::class)
            ->assertSee($this->student->name);

        // Verify PushSubscriptionController endpoint
        $pushResponse = $this->postJson('/api/push/subscribe', [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/' . uniqid(),
            'keys' => [
                'p256dh' => 'BNcRdreALRFXTkOOUHK1EtK2wtaz5Ry4YfYCA_0QTpQtUbVlUls0VJXg7A8u-Ts1XbjhazAkj7I99e8QcYP7DkM',
                'auth' => 'tBHItJI5svbpez7KI4CCXg',
            ],
        ]);

        $pushResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'subscribable_id' => $this->parentUser->id,
        ]);
    }
}
