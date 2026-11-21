<?php

namespace Tests\Feature;

use App\Filament\Pages\AttendanceReport;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\FinancialReport;
use App\Filament\Pages\ScanAttendance;
use App\Filament\Pages\StudentReport;
use App\Filament\Resources\Academic\AcademicYearResource;
use App\Filament\Resources\Academic\AcademicYearResource\Pages\ListAcademicYears;
use App\Filament\Resources\Academic\CbtExams\CbtExamResource;
use App\Filament\Resources\Academic\CbtExams\Pages\CreateCbtExam;
use App\Filament\Resources\Academic\CbtExams\Pages\EditCbtExam;
use App\Filament\Resources\Academic\CbtExams\Pages\ListCbtExams;
use App\Filament\Resources\Academic\CbtExams\RelationManagers\QuestionsRelationManager;
use App\Filament\Resources\Academic\ClassRoomResource;
use App\Filament\Resources\Academic\ClassRoomResource\Pages\ListClassRooms;
use App\Filament\Resources\Academic\ClassSchedules\ClassScheduleResource;
use App\Filament\Resources\Academic\SemesterResource;
use App\Filament\Resources\Academic\SemesterResource\Pages\ListSemesters;
use App\Filament\Resources\Academic\StudyMaterialResource;
use App\Filament\Resources\Academic\StudyMaterialResource\Pages\CreateStudyMaterial;
use App\Filament\Resources\Academic\StudyMaterialResource\Pages\EditStudyMaterial;
use App\Filament\Resources\Academic\SubjectResource;
use App\Filament\Resources\Academic\SubjectResource\Pages\ListSubjects;
use App\Filament\Resources\Evaluation\CbtAttemptResource;
use App\Filament\Resources\Finance\Expenses\ExpenseResource;
use App\Filament\Resources\Finance\Expenses\Pages\CreateExpense;
use App\Filament\Resources\Finance\Expenses\Pages\EditExpense;
use App\Filament\Resources\Finance\HonorResource;
use App\Filament\Resources\Finance\HonorResource\Pages\ListHonors;
use App\Filament\Resources\Finance\StudentPayments\Pages\ManageStudentPayments;
use App\Filament\Resources\Finance\StudentPayments\StudentPaymentResource;
use App\Filament\Resources\Finance\TutorSalaryResource;
use App\Filament\Resources\Finance\TutorSalaryResource\Pages\ManageTutorSalaries;
use App\Filament\Resources\HR\AttendanceResource;
use App\Filament\Resources\HR\AttendanceResource\Pages\ListAttendances;
use App\Filament\Resources\HR\Evaluations\EvaluationResource;
use App\Filament\Resources\HR\Evaluations\Pages\CreateEvaluation;
use App\Filament\Resources\HR\Evaluations\Pages\EditEvaluation;
use App\Filament\Resources\HR\StudentResource;
use App\Filament\Resources\HR\StudentResource\Pages\ListStudents;
use App\Filament\Resources\HR\TutorResource;
use App\Filament\Resources\HR\TutorResource\Pages\ListTutors;
use App\Filament\Resources\HR\UserResource;
use App\Filament\Resources\Operations\Admissions\AdmissionResource;
use App\Filament\Resources\Operations\Admissions\Pages\ManageAdmissions;
use App\Filament\Resources\Operations\JournalResource;
use App\Filament\Resources\Operations\JournalResource\Pages\ListJournals;
use App\Filament\Resources\Operations\JournalValidationResource;
use App\Filament\Resources\Operations\JournalValidationResource\Pages\ListJournalValidations;
use App\Filament\Resources\Operations\Schedules\ScheduleResource;
use App\Filament\Resources\Settings\SettingResource;
use App\Models\AcademicYear;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\CbtExam;
use App\Models\CbtQuestion;
use App\Models\ClassRoom;
use App\Models\ClassSchedule;
use App\Models\Evaluation;
use App\Models\Expense;
use App\Models\Honor;
use App\Models\Journal;
use App\Models\JournalValidation;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\Tutor;
use App\Models\TutorSalary;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PanelAdminAuditTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();

        $this->admin = User::create([
            'username' => 'audit_admin_' . uniqid(),
            'name' => 'Audit Admin',
            'email' => 'audit_admin_' . uniqid() . '@g-english.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        if (class_exists(Role::class)) {
            $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
            $this->admin->assignRole($role);
        }

        $this->actingAs($this->admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    public function test_all_admin_resource_pages_and_custom_pages_return_200_ok(): void
    {
        $academicYear = AcademicYear::create(['name' => '2099/2100', 'is_active' => true]);
        $subject = Subject::create(['code' => 'AUD-' . uniqid(), 'name' => 'Audit English', 'level' => 'Beginner', 'is_active' => true]);
        $tutor = Tutor::create(['nip' => 'NIP-' . uniqid(), 'name' => 'Audit Tutor', 'grade' => 'A', 'is_active' => true]);
        $classRoom = ClassRoom::create([
            'name' => 'Audit Class 1',
            'academic_year_id' => $academicYear->id,
            'subject_id' => $subject->id,
            'tutor_id' => $tutor->id,
            'capacity' => 15,
            'is_active' => true,
        ]);
        $student = Student::create([
            'nis' => 'GES999' . rand(100, 999),
            'student_type' => 'internal',
            'name' => 'Audit Student',
            'gender' => 'L',
            'is_active' => true,
        ]);
        $studyMaterial = StudyMaterial::create([
            'subject_id' => $subject->id,
            'type' => 'video',
            'title' => 'Audit Material',
            'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'is_published' => true,
        ]);
        $cbtExam = CbtExam::create([
            'title' => 'Audit Exam',
            'class_id' => $classRoom->id,
            'subject_id' => $subject->id,
            'cbt_mode' => 'internal_excel',
            'duration_minutes' => 60,
            'max_attempts' => 2,
            'is_active' => true,
        ]);
        $classSchedule = ClassSchedule::create([
            'class_id' => $classRoom->id,
            'tutor_id' => $tutor->id,
            'schedule_items' => [['day' => 'Monday', 'start_time' => '08:00', 'end_time' => '09:30']],
        ]);
        $schedule = Schedule::create([
            'class_room_id' => $classRoom->id,
            'user_id' => $tutor->user_id,
            'date' => now()->toDateString(),
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ]);
        $evaluation = Evaluation::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'score' => 95,
            'date' => now()->toDateString(),
        ]);
        $expense = Expense::create([
            'date' => now()->toDateString(),
            'category' => 'Operasional',
            'amount' => 150000,
            'description' => 'Audit Expense',
        ]);

        $urls = [
            // Dashboard & Custom Pages
            Dashboard::getUrl(),
            ScanAttendance::getUrl(),
            AttendanceReport::getUrl(),
            StudentReport::getUrl(),
            FinancialReport::getUrl(),

            // Academic & Master Data Resources
            AcademicYearResource::getUrl('index'),
            SemesterResource::getUrl('index'),
            SubjectResource::getUrl('index'),
            ClassRoomResource::getUrl('index'),
            StudyMaterialResource::getUrl('index'),
            StudyMaterialResource::getUrl('create'),
            StudyMaterialResource::getUrl('edit', ['record' => $studyMaterial]),
            ClassScheduleResource::getUrl('index'),
            ClassScheduleResource::getUrl('create'),
            ClassScheduleResource::getUrl('edit', ['record' => $classSchedule]),

            // HR & Operations Resources
            StudentResource::getUrl('index'),
            TutorResource::getUrl('index'),
            UserResource::getUrl('index'),
            AdmissionResource::getUrl('index'),
            AttendanceResource::getUrl('index'),
            JournalResource::getUrl('index'),
            JournalValidationResource::getUrl('index'),
            ScheduleResource::getUrl('index'),
            ScheduleResource::getUrl('create'),
            ScheduleResource::getUrl('edit', ['record' => $schedule]),

            // Evaluation & CBT Resources
            EvaluationResource::getUrl('index'),
            EvaluationResource::getUrl('create'),
            EvaluationResource::getUrl('edit', ['record' => $evaluation]),
            HonorResource::getUrl('index'),
            CbtExamResource::getUrl('index'),
            CbtExamResource::getUrl('create'),
            CbtExamResource::getUrl('edit', ['record' => $cbtExam]),
            CbtExamResource::getUrl('view', ['record' => $cbtExam]),
            CbtAttemptResource::getUrl('index'),

            // Finance & Settings Resources
            StudentPaymentResource::getUrl('index'),
            TutorSalaryResource::getUrl('index'),
            ExpenseResource::getUrl('index'),
            ExpenseResource::getUrl('create'),
            ExpenseResource::getUrl('edit', ['record' => $expense]),
            SettingResource::getUrl('index'),
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }
    }

    public function test_academic_master_data_create_and_update_flows(): void
    {
        // 1. AcademicYear
        Livewire::test(ListAcademicYears::class)
            ->callAction('create', data: [
                'name' => '2035/2036',
                'is_active' => true,
            ])
            ->assertHasNoActionErrors();

        $ay = AcademicYear::where('name', '2035/2036')->firstOrFail();

        Livewire::test(ListAcademicYears::class)
            ->callTableAction('edit', $ay, data: [
                'name' => '2035/2036-Updated',
                'is_active' => false,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('academic_years', ['id' => $ay->id, 'name' => '2035/2036-Updated']);

        // 2. Semester
        Livewire::test(ListSemesters::class)
            ->callAction('create', data: [
                'academic_year_id' => $ay->id,
                'name' => 'Semester Ganjil Audit',
                'start_date' => '2035-07-01',
                'end_date' => '2035-12-31',
                'is_active' => true,
            ])
            ->assertHasNoActionErrors();

        $semester = Semester::where('name', 'Semester Ganjil Audit')->firstOrFail();

        Livewire::test(ListSemesters::class)
            ->callTableAction('edit', $semester, data: [
                'academic_year_id' => $ay->id,
                'name' => 'Semester Genap Audit',
                'start_date' => '2036-01-01',
                'end_date' => '2036-06-30',
                'is_active' => false,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('semesters', ['id' => $semester->id, 'name' => 'Semester Genap Audit']);

        // 3. Subject
        $subjectCode = 'SUB-' . rand(1000, 9999);
        Livewire::test(ListSubjects::class)
            ->callAction('create', data: [
                'code' => $subjectCode,
                'name' => 'Survival English 101',
                'level' => 'Beginner',
                'description' => 'Basic course',
                'is_active' => true,
            ])
            ->assertHasNoActionErrors();

        $subject = Subject::where('code', $subjectCode)->firstOrFail();

        Livewire::test(ListSubjects::class)
            ->callTableAction('edit', $subject, data: [
                'code' => $subjectCode,
                'name' => 'Survival English 102',
                'level' => 'Intermediate',
                'description' => 'Updated course',
                'is_active' => true,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('subjects', ['id' => $subject->id, 'name' => 'Survival English 102']);

        // 4. ClassRoom
        $tutor = Tutor::create(['nip' => 'T-' . uniqid(), 'name' => 'Homeroom Tutor', 'grade' => 'A', 'is_active' => true]);
        Livewire::test(ListClassRooms::class)
            ->callAction('create', data: [
                'name' => 'Class Alpha Audit',
                'academic_year_id' => $ay->id,
                'subject_id' => $subject->id,
                'tutor_id' => $tutor->id,
                'capacity' => 20,
                'is_active' => true,
                'schedules' => [],
                'students' => [],
            ])
            ->assertHasNoActionErrors();

        $classRoom = ClassRoom::where('name', 'Class Alpha Audit')->firstOrFail();

        Livewire::test(ListClassRooms::class)
            ->callTableAction('edit', $classRoom, data: [
                'name' => 'Class Beta Audit',
                'academic_year_id' => $ay->id,
                'subject_id' => $subject->id,
                'tutor_id' => $tutor->id,
                'capacity' => 25,
                'is_active' => true,
                'schedules' => [],
                'students' => [],
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('classes', ['id' => $classRoom->id, 'name' => 'Class Beta Audit', 'capacity' => 25]);

        // 5. StudyMaterial
        Livewire::test(CreateStudyMaterial::class)
            ->fillForm([
                'subject_id' => $subject->id,
                'type' => 'video',
                'title' => 'Pronunciation Video',
                'description' => 'Watch and practice',
                'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'is_published' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $material = StudyMaterial::where('title', 'Pronunciation Video')->firstOrFail();

        Livewire::test(EditStudyMaterial::class, ['record' => $material->getRouteKey()])
            ->fillForm([
                'title' => 'Pronunciation Video Updated',
                'is_published' => false,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('study_materials', ['id' => $material->id, 'title' => 'Pronunciation Video Updated', 'is_published' => 0]);
    }

    public function test_student_tutor_and_admission_create_update_and_user_provisioning(): void
    {
        // 1. Student (internal & external + auto user + parent user provisioning)
        $nis = 'GES' . rand(800000, 899999);
        $parentPhone = '0812' . rand(10000000, 99999999);

        Livewire::test(ListStudents::class)
            ->callAction('create', data: [
                'nis' => $nis,
                'student_type' => 'internal',
                'name' => 'Student Internal Audit',
                'gender' => 'L',
                'birth_date' => '2012-05-10',
                'address' => 'Jl. PHM Noor',
                'phone' => '081100001111',
                'mother_name' => 'Ibu Audit',
                'mother_phone' => $parentPhone,
                'is_active' => true,
            ])
            ->assertHasNoActionErrors();

        $student = Student::where('nis', $nis)->firstOrFail();
        $this->assertEquals('internal', $student->student_type);
        $this->assertNotNull($student->user_id);
        $this->assertEquals('siswa', $student->user->role);
        $this->assertTrue($student->parents()->where('username', $parentPhone)->exists());

        // Update Student to external
        Livewire::test(ListStudents::class)
            ->callTableAction('edit', $student, data: [
                'nis' => $nis,
                'student_type' => 'external',
                'name' => 'Student External Audit',
                'gender' => 'P',
                'is_active' => true,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'student_type' => 'external',
            'name' => 'Student External Audit',
        ]);

        // 2. Tutor (Create & Update + auto user provisioning)
        $nip = 'NIP' . rand(100000, 999999);
        Livewire::test(ListTutors::class)
            ->callAction('create', data: [
                'nip' => $nip,
                'name' => 'Tutor Audit One',
                'phone' => '081322223333',
                'specialization' => 'Speaking',
                'grade' => 'B',
                'is_active' => true,
            ])
            ->assertHasNoActionErrors();

        $tutor = Tutor::where('nip', $nip)->firstOrFail();
        $this->assertNotNull($tutor->user_id);
        $this->assertEquals('tutor', $tutor->user->role);

        Livewire::test(ListTutors::class)
            ->callTableAction('edit', $tutor, data: [
                'nip' => $nip,
                'name' => 'Tutor Audit Updated',
                'grade' => 'A',
                'is_active' => true,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('tutors', ['id' => $tutor->id, 'name' => 'Tutor Audit Updated', 'grade' => 'A']);

        // 3. Admission (Create, Update, and Approve conversion carrying student_type)
        Livewire::test(ManageAdmissions::class)
            ->callAction('create', data: [
                'student_type' => 'external',
                'name' => 'Applicant External Audit',
                'gender' => 'L',
                'phone' => '081299998888',
                'education_level' => 'SMP',
                'tuition_fee' => 4000000,
                'registration_fee' => 100000,
                'has_discount' => false,
                'total_fee' => 4100000,
                'payment_method' => 'installment',
                'amount_paid' => 600000,
                'status' => 'pending',
            ])
            ->assertHasNoActionErrors();

        $admission = Admission::where('name', 'Applicant External Audit')->firstOrFail();
        $this->assertEquals('external', $admission->student_type);

        Livewire::test(ManageAdmissions::class)
            ->callTableAction('edit', $admission, data: [
                'student_type' => 'external',
                'name' => 'Applicant External Edited',
                'payment_method' => 'installment',
                'amount_paid' => 750000,
                'status' => 'pending',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('admissions', ['id' => $admission->id, 'name' => 'Applicant External Edited', 'amount_paid' => 750000]);

        // Approve & Convert to Student
        Livewire::test(ManageAdmissions::class)
            ->callTableAction('approve', $admission)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('admissions', ['id' => $admission->id, 'status' => 'approved']);
        $this->assertDatabaseHas('students', ['name' => 'Applicant External Edited', 'student_type' => 'external']);
    }

    public function test_operations_and_evaluations_create_and_update_flows(): void
    {
        $ay = AcademicYear::create(['name' => '2040/2041', 'is_active' => true]);
        $subject = Subject::create(['code' => 'OP-' . rand(100, 999), 'name' => 'Operations English', 'is_active' => true]);
        $tutor = Tutor::create(['nip' => 'T-OP-' . rand(100, 999), 'name' => 'Tutor Ops', 'grade' => 'A', 'is_active' => true]);
        $classRoom = ClassRoom::create([
            'name' => 'Ops Class',
            'academic_year_id' => $ay->id,
            'subject_id' => $subject->id,
            'tutor_id' => $tutor->id,
            'is_active' => true,
        ]);
        $student = Student::create([
            'nis' => 'GES' . rand(700000, 799999),
            'student_type' => 'internal',
            'name' => 'Student Ops',
            'gender' => 'L',
            'is_active' => true,
        ]);
        $classRoom->students()->attach($student->id);

        // 1. Attendance
        Livewire::test(ListAttendances::class)
            ->callAction('create', data: [
                'role_type' => 'student',
                'class_id' => $classRoom->id,
                'student_id' => $student->id,
                'date' => now()->toDateString(),
                'time_in' => '08:00:00',
                'status' => 'present',
                'notes' => 'On time',
            ])
            ->assertHasNoActionErrors();

        $attendance = Attendance::where('student_id', $student->id)->firstOrFail();

        Livewire::test(ListAttendances::class)
            ->callTableAction('edit', $attendance, data: [
                'role_type' => 'student',
                'class_id' => $classRoom->id,
                'student_id' => $student->id,
                'date' => now()->toDateString(),
                'time_in' => '08:15:00',
                'status' => 'late',
                'notes' => 'Late 15m',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('attendances', ['id' => $attendance->id, 'status' => 'late']);

        // 2. Journal
        Livewire::test(ListJournals::class)
            ->callAction('create', data: [
                'class_id' => $classRoom->id,
                'tutor_id' => $tutor->id,
                'date' => now()->toDateString(),
                'topic' => 'Past Continuous Tense',
                'description' => '<p>Interactive speaking</p>',
                'materials' => 'G-Book Unit 3',
                'status' => 'draft',
                'jp_count' => 2,
            ])
            ->assertHasNoActionErrors();

        $journal = Journal::where('topic', 'Past Continuous Tense')->firstOrFail();

        Livewire::test(ListJournals::class)
            ->callTableAction('edit', $journal, data: [
                'class_id' => $classRoom->id,
                'tutor_id' => $tutor->id,
                'date' => now()->toDateString(),
                'topic' => 'Past Continuous Tense Updated',
                'status' => 'submitted',
                'jp_count' => 3,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('journals', ['id' => $journal->id, 'status' => 'submitted', 'jp_count' => 3]);

        // 3. JournalValidation (Approve Action)
        Livewire::test(ListJournalValidations::class)
            ->callTableAction('approve', $journal)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('journals', ['id' => $journal->id, 'status' => 'validated']);
        $this->assertDatabaseHas('journal_validations', ['journal_id' => $journal->id, 'status' => 'approved']);

        // 4. Evaluation
        Livewire::test(CreateEvaluation::class)
            ->fillForm([
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'score' => 88,
                'notes' => 'Good fluency',
                'date' => now()->toDateString(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $evaluation = Evaluation::where('student_id', $student->id)->where('score', 88)->firstOrFail();

        Livewire::test(EditEvaluation::class, ['record' => $evaluation->getRouteKey()])
            ->fillForm([
                'score' => 94,
                'notes' => 'Excellent fluency',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('evaluations', ['id' => $evaluation->id, 'score' => 94]);

        // 5. Honor
        Livewire::test(ListHonors::class)
            ->callAction('create', data: [
                'student_id' => $student->id,
                'journal_id' => $journal->id,
                'type' => 'achievement',
                'title' => 'Best Speaker of the Month',
                'description' => 'Top score in debate',
                'date' => now()->toDateString(),
                'certificate_number' => 'CERT-2026-001',
            ])
            ->assertHasNoActionErrors();

        $honor = Honor::where('title', 'Best Speaker of the Month')->firstOrFail();

        Livewire::test(ListHonors::class)
            ->callTableAction('edit', $honor, data: [
                'student_id' => $student->id,
                'journal_id' => $journal->id,
                'type' => 'certificate',
                'title' => 'Best Speaker Certificate',
                'date' => now()->toDateString(),
                'certificate_number' => 'CERT-2026-002',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('honors', ['id' => $honor->id, 'type' => 'certificate', 'certificate_number' => 'CERT-2026-002']);
    }

    public function test_cbt_exam_dual_modes_and_cbt_question_create_update(): void
    {
        $ay = AcademicYear::create(['name' => '2041/2042', 'is_active' => true]);
        $subject = Subject::create(['code' => 'CBT-' . rand(100, 999), 'name' => 'CBT English', 'is_active' => true]);
        $classRoom = ClassRoom::create([
            'name' => 'CBT Class',
            'academic_year_id' => $ay->id,
            'subject_id' => $subject->id,
            'is_active' => true,
        ]);

        // 1. CbtExam Mode A: google_form
        Livewire::test(CreateCbtExam::class)
            ->fillForm([
                'cbt_mode' => 'google_form',
                'title' => 'Midterm GForm Exam',
                'description' => 'Google form mode test',
                'class_id' => $classRoom->id,
                'subject_id' => $subject->id,
                'gform_embed_url' => 'https://docs.google.com/forms/d/e/1FAIpQLSeTest/viewform?embedded=true',
                'gform_sheet_csv_url' => 'https://docs.google.com/spreadsheets/d/e/2PACX-Test/pub?output=csv',
                'gform_nis_entry_id' => 'entry.111111',
                'gform_name_entry_id' => 'entry.222222',
                'gform_score_column' => 'Skor',
                'duration_minutes' => 90,
                'max_attempts' => 1,
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $gformExam = CbtExam::where('title', 'Midterm GForm Exam')->firstOrFail();
        $this->assertEquals('google_form', $gformExam->cbt_mode);

        // 2. CbtExam Mode B: internal_excel
        Livewire::test(CreateCbtExam::class)
            ->fillForm([
                'cbt_mode' => 'internal_excel',
                'title' => 'Final Internal Excel Exam',
                'description' => 'Internal mode test',
                'class_id' => $classRoom->id,
                'subject_id' => $subject->id,
                'duration_minutes' => 120,
                'max_attempts' => 2,
                'is_active' => true,
                'auto_save_interval' => 30,
                'shuffle_questions' => true,
                'shuffle_options' => false,
                'show_result_immediately' => true,
                'allow_review' => false,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $internalExam = CbtExam::where('title', 'Final Internal Excel Exam')->firstOrFail();
        $this->assertEquals('internal_excel', $internalExam->cbt_mode);

        Livewire::test(EditCbtExam::class, ['record' => $internalExam->getRouteKey()])
            ->fillForm([
                'title' => 'Final Internal Excel Exam Updated',
                'duration_minutes' => 100,
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('cbt_exams', ['id' => $internalExam->id, 'title' => 'Final Internal Excel Exam Updated', 'duration_minutes' => 100]);

        // 3. CbtQuestion via QuestionsRelationManager
        Livewire::test(QuestionsRelationManager::class, [
            'ownerRecord' => $internalExam,
            'pageClass' => EditCbtExam::class,
        ])
            ->callTableAction('create', data: [
                'question_text' => '<p>What is the synonym of Happy?</p>',
                'option_a' => 'Sad',
                'option_b' => 'Joyful',
                'option_c' => 'Angry',
                'option_d' => 'Tired',
                'option_e' => 'Bored',
                'correct_answer' => 'B',
                'points' => 10,
            ])
            ->assertHasNoTableActionErrors();

        $question = CbtQuestion::where('cbt_exam_id', $internalExam->id)->firstOrFail();

        Livewire::test(QuestionsRelationManager::class, [
            'ownerRecord' => $internalExam,
            'pageClass' => EditCbtExam::class,
        ])
            ->callTableAction('edit', $question, data: [
                'question_text' => '<p>What is the antonym of Happy?</p>',
                'option_a' => 'Sad',
                'option_b' => 'Joyful',
                'option_c' => 'Angry',
                'option_d' => 'Tired',
                'option_e' => 'Bored',
                'correct_answer' => 'A',
                'points' => 15,
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('cbt_questions', ['id' => $question->id, 'correct_answer' => 'A', 'points' => 15]);
    }

    public function test_finance_modules_and_pdf_report_routes(): void
    {
        $student = Student::create([
            'nis' => 'GES' . rand(600000, 699999),
            'student_type' => 'internal',
            'name' => 'Student Finance Audit',
            'gender' => 'L',
            'is_active' => true,
        ]);
        $tutor = Tutor::create([
            'nip' => 'T-FIN-' . rand(100, 999),
            'name' => 'Tutor Finance Audit',
            'grade' => 'A',
            'is_active' => true,
        ]);
        $subject = Subject::create([
            'code' => 'FIN-' . rand(100, 999),
            'name' => 'Finance English',
            'is_active' => true,
        ]);
        $evaluation = Evaluation::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'score' => 92,
            'notes' => 'Great job',
            'date' => now()->toDateString(),
        ]);

        // 1. StudentPayment
        Livewire::test(ManageStudentPayments::class)
            ->callAction('create', data: [
                'student_id' => $student->id,
                'due_date' => now()->addDays(7)->toDateString(),
                'amount' => 350000,
                'status' => 'unpaid',
                'notes' => 'SPP November',
            ])
            ->assertHasNoActionErrors();

        $payment = StudentPayment::where('student_id', $student->id)->firstOrFail();

        Livewire::test(ManageStudentPayments::class)
            ->callTableAction('edit', $payment, data: [
                'student_id' => $student->id,
                'amount' => 400000,
                'status' => 'paid',
                'paid_at' => now()->toDateTimeString(),
                'notes' => 'SPP November Paid',
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('student_payments', ['id' => $payment->id, 'status' => 'paid', 'amount' => 400000]);

        // 2. TutorSalary
        Livewire::test(ManageTutorSalaries::class)
            ->callAction('create', data: [
                'tutor_id' => $tutor->id,
                'month' => 11,
                'year' => 2026,
                'total_jp' => 10,
                'total_amount' => 500000,
                'status' => 'pending',
            ])
            ->assertHasNoActionErrors();

        $salary = TutorSalary::where('tutor_id', $tutor->id)->firstOrFail();

        Livewire::test(ManageTutorSalaries::class)
            ->callTableAction('edit', $salary, data: [
                'tutor_id' => $tutor->id,
                'month' => 11,
                'year' => 2026,
                'total_jp' => 12,
                'total_amount' => 600000,
                'status' => 'paid',
                'paid_at' => now()->toDateString(),
            ])
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseHas('tutor_salaries', ['id' => $salary->id, 'status' => 'paid', 'total_amount' => 600000]);

        // 3. Expense
        Livewire::test(CreateExpense::class)
            ->fillForm([
                'date' => now()->toDateString(),
                'category' => 'Operasional',
                'amount' => 250000,
                'description' => 'Electricity bill',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $expense = Expense::where('description', 'Electricity bill')->firstOrFail();

        Livewire::test(EditExpense::class, ['record' => $expense->getRouteKey()])
            ->fillForm([
                'amount' => 275000,
                'description' => 'Electricity & Water bill',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'amount' => 275000]);

        // 4. All 4 PDF Report Routes
        $this->get(route('report.tutor-salary', $salary->id))->assertStatus(200);
        $this->get(route('report.student-payment', $payment->id))->assertStatus(200);
        $this->get(route('report.certificate', $student->id))->assertStatus(200);
        $this->get(route('report.evaluation', $evaluation->id))->assertStatus(200);
    }
}
