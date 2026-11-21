<?php

namespace Tests\Feature;

use App\Exports\CbtQuestionTemplateExport;
use App\Filament\Tutor\Pages\ScanAttendance;
use App\Filament\Tutor\Resources\CbtExams\CbtExamResource;
use App\Filament\Tutor\Resources\CbtExams\Pages\CreateCbtExam;
use App\Filament\Tutor\Resources\CbtExams\Pages\EditCbtExam;
use App\Filament\Tutor\Resources\CbtExams\Pages\ListCbtExams;
use App\Filament\Tutor\Resources\Evaluations\EvaluationResource;
use App\Filament\Tutor\Resources\Evaluations\Pages\CreateEvaluation;
use App\Filament\Tutor\Resources\Evaluations\Pages\EditEvaluation;
use App\Filament\Tutor\Resources\Journals\JournalResource;
use App\Filament\Tutor\Resources\Journals\Pages\ManageJournals;
use App\Filament\Tutor\Resources\Schedules\ScheduleResource;
use App\Filament\Tutor\Resources\StudyMaterialResource;
use App\Filament\Tutor\Resources\StudyMaterialResource\Pages\CreateStudyMaterial;
use App\Filament\Tutor\Resources\StudyMaterialResource\Pages\EditStudyMaterial;
use App\Filament\Tutor\Resources\TutorSalaries\TutorSalaryResource;
use App\Imports\CbtQuestionsExcelImport;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\CbtExam;
use App\Models\CbtQuestion;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Evaluation;
use App\Models\Journal;
use App\Models\Schedule;
use App\Models\Student;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\Tutor;
use App\Models\TutorSalary;
use App\Models\User;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelTutorAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected Tutor $tutor;
    protected User $tutorUser;
    protected AcademicYear $academicYear;
    protected Subject $subject;
    protected ClassRoom $classRoom;
    protected Student $student;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->tutor = Tutor::create([
            'nip' => 'NIP-TUT-' . rand(100000, 999999),
            'name' => 'Tutor Panel Audit',
            'phone' => '081277776666',
            'specialization' => 'TOEFL & Speaking',
            'grade' => 'A',
            'is_active' => true,
        ]);

        $this->tutorUser = $this->tutor->user;

        if (class_exists(Role::class)) {
            $role = Role::firstOrCreate(['name' => 'tutor', 'guard_name' => 'web']);
            $this->tutorUser->assignRole($role);
        }

        $this->academicYear = AcademicYear::create([
            'name' => '2098/2099',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'code' => 'TUT-SUB-' . rand(1000, 9999),
            'name' => 'Tutor Audit English',
            'level' => 'Intermediate',
            'is_active' => true,
        ]);

        $this->classRoom = ClassRoom::create([
            'name' => 'Tutor Audit Class A',
            'academic_year_id' => $this->academicYear->id,
            'subject_id' => $this->subject->id,
            'tutor_id' => $this->tutor->id,
            'capacity' => 15,
            'is_active' => true,
        ]);

        ClassSchedule::create([
            'class_id' => $this->classRoom->id,
            'tutor_id' => $this->tutor->id,
            'schedule_items' => [
                ['day' => now()->format('l'), 'start_time' => '08:00', 'end_time' => '09:40'],
            ],
        ]);

        $this->student = Student::create([
            'nis' => 'GES' . rand(500000, 599999),
            'student_type' => 'internal',
            'name' => 'Student In Tutor Class',
            'gender' => 'L',
            'is_active' => true,
        ]);

        $this->classRoom->students()->attach($this->student->id);

        $this->actingAs($this->tutorUser);
        Filament::setCurrentPanel(Filament::getPanel('tutor'));
    }

    public function test_all_tutor_panel_pages_and_resources_return_200_ok(): void
    {
        $material = StudyMaterial::create([
            'subject_id' => $this->subject->id,
            'type' => 'video',
            'title' => 'Tutor Study Material',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_published' => true,
        ]);

        $exam = CbtExam::create([
            'title' => 'Tutor Panel Exam',
            'class_id' => $this->classRoom->id,
            'subject_id' => $this->subject->id,
            'cbt_mode' => 'internal_excel',
            'duration_minutes' => 90,
            'max_attempts' => 2,
            'is_active' => true,
        ]);

        $evaluation = Evaluation::create([
            'student_id' => $this->student->id,
            'subject_id' => $this->subject->id,
            'score' => 90,
            'notes' => 'Initial Tutor Eval',
            'date' => now()->toDateString(),
        ]);

        Schedule::create([
            'class_room_id' => $this->classRoom->id,
            'user_id' => $this->tutorUser->id,
            'date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '09:40:00',
        ]);

        TutorSalary::create([
            'tutor_id' => $this->tutor->id,
            'month' => (int) now()->month,
            'year' => (int) now()->year,
            'total_jp' => 8,
            'total_amount' => 400000,
            'status' => 'pending',
        ]);

        $urls = [
            '/tutor',
            ScanAttendance::getUrl(),
            JournalResource::getUrl('index'),
            ScheduleResource::getUrl('index'),
            StudyMaterialResource::getUrl('index'),
            StudyMaterialResource::getUrl('create'),
            StudyMaterialResource::getUrl('edit', ['record' => $material]),
            CbtExamResource::getUrl('index'),
            CbtExamResource::getUrl('create'),
            CbtExamResource::getUrl('edit', ['record' => $exam]),
            CbtExamResource::getUrl('view', ['record' => $exam]),
            EvaluationResource::getUrl('index'),
            EvaluationResource::getUrl('create'),
            EvaluationResource::getUrl('edit', ['record' => $evaluation]),
            TutorSalaryResource::getUrl('index'),
        ];

        foreach ($urls as $url) {
            $this->get($url)->assertStatus(200);
        }
    }

    public function test_tutor_can_submit_and_edit_teaching_journal(): void
    {
        Livewire::test(ManageJournals::class)
            ->callAction('create', data: [
                'class_id' => $this->classRoom->id,
                'tutor_id' => $this->tutor->id,
                'date' => now()->toDateString(),
                'topic' => 'Conditional Sentences Type 1 & 2',
                'description' => 'Roleplay and worksheet exercises',
                'materials' => 'G-Book Chapter 4',
                'status' => 'draft',
                'jp_count' => 2,
            ])
            ->assertHasNoActionErrors();

        $journal = Journal::where('tutor_id', $this->tutor->id)
            ->where('topic', 'Conditional Sentences Type 1 & 2')
            ->firstOrFail();

        $this->assertEquals(2, $journal->jp_count);
        $this->assertEquals('G-Book Chapter 4', $journal->materials);

        Livewire::test(ManageJournals::class)
            ->callTableAction('edit', $journal, data: [
                'class_id' => $this->classRoom->id,
                'tutor_id' => $this->tutor->id,
                'date' => now()->toDateString(),
                'topic' => 'Conditional Sentences Type 1, 2 & 3',
                'description' => 'Updated worksheet exercises',
                'materials' => 'G-Book Chapter 4 & 5',
                'status' => 'submitted',
                'jp_count' => 3,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'topic' => 'Conditional Sentences Type 1, 2 & 3',
            'materials' => 'G-Book Chapter 4 & 5',
            'jp_count' => 3,
            'status' => 'submitted',
        ]);
    }

    public function test_tutor_can_create_edit_dual_mode_cbt_exams_and_import_excel_questions(): void
    {
        // 1. Mode A: google_form
        Livewire::test(CreateCbtExam::class)
            ->fillForm([
                'cbt_mode' => 'google_form',
                'title' => 'Tutor GForm Quiz',
                'description' => 'Weekly quiz via Google Form',
                'class_id' => $this->classRoom->id,
                'subject_id' => $this->subject->id,
                'gform_embed_url' => 'https://docs.google.com/forms/d/e/1FAIpQLSeTutorQuiz/viewform?embedded=true',
                'gform_sheet_csv_url' => 'https://docs.google.com/spreadsheets/d/e/2PACX-TutorQuiz/pub?output=csv',
                'gform_nis_entry_id' => 'entry.123456',
                'gform_name_entry_id' => 'entry.654321',
                'gform_score_column' => 'Skor',
                'duration_minutes' => 45,
                'max_attempts' => 1,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $gformExam = CbtExam::where('title', 'Tutor GForm Quiz')->firstOrFail();
        $this->assertEquals('google_form', $gformExam->cbt_mode);

        // 2. Mode B: internal_excel
        Livewire::test(CreateCbtExam::class)
            ->fillForm([
                'cbt_mode' => 'internal_excel',
                'title' => 'Tutor Real CBT Exam',
                'description' => 'Internal CBT exam with Excel questions',
                'class_id' => $this->classRoom->id,
                'subject_id' => $this->subject->id,
                'duration_minutes' => 60,
                'max_attempts' => 2,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $internalExam = CbtExam::where('title', 'Tutor Real CBT Exam')->firstOrFail();
        $this->assertEquals('internal_excel', $internalExam->cbt_mode);

        // Edit internal_excel exam
        Livewire::test(EditCbtExam::class, ['record' => $internalExam->getRouteKey()])
            ->fillForm([
                'title' => 'Tutor Real CBT Exam Edited',
                'duration_minutes' => 75,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('cbt_exams', [
            'id' => $internalExam->id,
            'title' => 'Tutor Real CBT Exam Edited',
            'duration_minutes' => 75,
        ]);

        // 3. Test Excel Question Template & Chunked Import validation
        $relativeExcelPath = 'cbt_imports/test_tutor_questions.xlsx';
        Excel::store(new CbtQuestionTemplateExport(), $relativeExcelPath, 'local');
        $fullPath = Storage::disk('local')->path($relativeExcelPath);

        $importer = new CbtQuestionsExcelImport($internalExam->id);
        Excel::import($importer, $fullPath);

        $this->assertEquals(2, $importer->importedCount);
        $this->assertEquals(2, CbtQuestion::where('cbt_exam_id', $internalExam->id)->count());

        Storage::disk('local')->delete($relativeExcelPath);
    }

    public function test_tutor_can_record_and_update_student_evaluation_score(): void
    {
        Livewire::test(CreateEvaluation::class)
            ->fillForm([
                'student_id' => $this->student->id,
                'subject_id' => $this->subject->id,
                'score' => 86,
                'notes' => 'Good speaking confidence',
                'date' => now()->toDateString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $evaluation = Evaluation::where('student_id', $this->student->id)
            ->where('subject_id', $this->subject->id)
            ->where('score', 86)
            ->firstOrFail();

        Livewire::test(EditEvaluation::class, ['record' => $evaluation->getRouteKey()])
            ->fillForm([
                'score' => 93,
                'notes' => 'Excellent improvement in pronunciation',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('evaluations', [
            'id' => $evaluation->id,
            'score' => 93,
            'notes' => 'Excellent improvement in pronunciation',
        ]);
    }

    public function test_tutor_qr_attendance_check_in_and_duplicate_scan_rejection(): void
    {
        $qrPayload = 'student-' . $this->student->id;

        // First scan: should create attendance and dispatch success
        Livewire::test(ScanAttendance::class)
            ->call('processQrCode', $qrPayload)
            ->assertDispatched('scan-result', function (string $name, array $params): bool {
                $payload = $params[0] ?? $params;
                return ($payload['type'] ?? '') === 'success';
            });

        $this->assertEquals(
            1,
            Attendance::where('date', now()->toDateString())
                ->where('role_type', 'student')
                ->where('student_id', $this->student->id)
                ->count()
        );

        // Second scan on same date: should reject duplicate and keep count = 1
        Livewire::test(ScanAttendance::class)
            ->call('processQrCode', $qrPayload)
            ->assertDispatched('scan-result', function (string $name, array $params): bool {
                $payload = $params[0] ?? $params;
                return ($payload['type'] ?? '') === 'error';
            });

        $this->assertEquals(
            1,
            Attendance::where('date', now()->toDateString())
                ->where('role_type', 'student')
                ->where('student_id', $this->student->id)
                ->count()
        );
    }
}
