<?php

namespace Database\Seeders;

use App\Livewire\BrainChampionRegistration;
use App\Models\AcademicYear;
use App\Models\Admission;
use App\Models\Attendance;
use App\Models\CbtAttempt;
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
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentPayment;
use App\Models\StudyMaterial;
use App\Models\Subject;
use App\Models\Test;
use App\Models\TestScheduleRequest;
use App\Models\TestSession;
use App\Models\Tutor;
use App\Models\TutorSalary;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FullDemoSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        // 1. Admin & Agency Accounts
        $admin = User::updateOrCreate(
            ['email' => 'superadmin@g-english.com'],
            [
                'name' => 'Super Admin',
                'username' => 'GEADMIN48',
                'password' => $password,
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kemenag@g-english.com'],
            [
                'name' => 'Operator Kemenag',
                'username' => 'kemenag',
                'password' => $password,
                'role' => 'kemenag',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kemendikdasmen@g-english.com'],
            [
                'name' => 'Operator Kemendikdasmen',
                'username' => 'kemendikdasmen',
                'password' => $password,
                'role' => 'kemendikdasmen',
            ]
        );

        // 2. Tutors (termasuk tutor baru Ms Hana)
        $tutorSeeds = [
            ['name' => 'Mr Bakeri', 'username' => 'GE48101', 'email' => 'bakeri@g-english.com', 'phone' => '081234567801', 'spec' => 'Survival English', 'grade' => 'A'],
            ['name' => 'Mr Ichsan', 'username' => 'GE48102', 'email' => 'ichsan@g-english.com', 'phone' => '081234567802', 'spec' => 'Grammar & CBT', 'grade' => 'A'],
            ['name' => 'Mr Rizhan', 'username' => 'GE48103', 'email' => 'rizhan@g-english.com', 'phone' => '081234567803', 'spec' => 'Academic English', 'grade' => 'B'],
            ['name' => 'Ms Hana', 'username' => 'GE48104', 'email' => 'hana@g-english.com', 'phone' => '081234567804', 'spec' => 'Speaking & Kids English', 'grade' => 'A'],
        ];

        $tutors = [];
        $tutorUsers = [];
        foreach ($tutorSeeds as $td) {
            $u = User::updateOrCreate(
                ['email' => $td['email']],
                [
                    'name' => $td['name'],
                    'username' => $td['username'],
                    'password' => $password,
                    'role' => 'tutor',
                ]
            );
            $tutorUsers[$td['name']] = $u;

            $tutors[$td['name']] = Tutor::updateOrCreate(
                ['nip' => $td['username']],
                [
                    'user_id' => $u->id,
                    'name' => $td['name'],
                    'phone' => $td['phone'],
                    'specialization' => $td['spec'],
                    'grade' => $td['grade'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Academic Years, Semesters, Subjects
        AcademicYear::updateOrCreate(['name' => '2025/2026'], ['is_active' => false]);
        $ay = AcademicYear::updateOrCreate(['name' => '2026/2027'], ['is_active' => true]);

        Semester::updateOrCreate(
            ['academic_year_id' => $ay->id, 'name' => 'Semester 1 (Ganjil)'],
            ['start_date' => '2026-07-14', 'end_date' => '2026-12-20', 'is_active' => true]
        );
        Semester::updateOrCreate(
            ['academic_year_id' => $ay->id, 'name' => 'Semester 2 (Genap)'],
            ['start_date' => '2027-01-05', 'end_date' => '2027-06-20', 'is_active' => false]
        );

        $subSurvival = Subject::updateOrCreate(
            ['code' => 'SURVIVAL'],
            ['name' => 'Survival English', 'level' => 'All Levels', 'description' => 'Bahasa Inggris dasar untuk komunikasi harian.', 'is_active' => true]
        );
        $subSpeaking = Subject::updateOrCreate(
            ['code' => 'SPEAKING'],
            ['name' => 'Speaking & Conversation', 'level' => 'Kids & Junior', 'description' => 'Pelatihan percaya diri berbicara bahasa Inggris aktif.', 'is_active' => true]
        );
        $subGrammar = Subject::updateOrCreate(
            ['code' => 'GRAMMAR'],
            ['name' => 'Grammar Mastery', 'level' => 'SMP & SMA', 'description' => 'Penguasaan tata bahasa Inggris dan persiapan ujian.', 'is_active' => true]
        );

        // 4. Study Materials
        $materialSeeds = [
            ['subject_id' => $subSurvival->id, 'type' => 'video', 'title' => 'Daily Greeting & Self Introduction', 'description' => 'Video pengenalan diri dan percakapan sehari-hari.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'is_published' => true],
            ['subject_id' => $subSpeaking->id, 'type' => 'mixed', 'title' => 'Public Speaking for Kids — Ms Hana', 'description' => 'Panduan intonasi, ekspresi, dan storytelling untuk kelas Ms Hana.', 'youtube_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'is_published' => true],
            ['subject_id' => $subGrammar->id, 'type' => 'pdf', 'title' => '16 Tenses Ringkas & Latihan Soal', 'description' => 'Rangkuman praktis tenses dan contoh kalimat.', 'youtube_url' => null, 'is_published' => true],
        ];
        foreach ($materialSeeds as $ms) {
            StudyMaterial::updateOrCreate(['title' => $ms['title']], $ms);
        }

        // 5. Internal Students + Parents + Payments (Program tunggal: Survival English jenjang SD/MI, SMP/MTs, SMA/MA)
        $internalSeeds = [
            ['name' => 'Carmen', 'username' => 'GES481101', 'email' => 'carmen@g-english.com', 'gender' => 'P', 'class' => 'Survival English SMA/MA', 'paid' => 2200000, 'father' => 'Budi Santoso', 'father_phone' => '081222333001', 'mother' => 'Siti Aminah', 'mother_phone' => '081333444001'],
            ['name' => 'Kyuhyun', 'username' => 'GES481102', 'email' => 'kyuhyun@g-english.com', 'gender' => 'L', 'class' => 'Survival English SD/MI', 'paid' => 2000000, 'father' => 'Hendra Wijaya', 'father_phone' => '081222333002', 'mother' => 'Dewi Lestari', 'mother_phone' => '081333444002'],
            ['name' => 'IU', 'username' => 'GES481103', 'email' => 'iu@g-english.com', 'gender' => 'P', 'class' => 'Survival English SMP/MTs', 'paid' => 2500000, 'father' => 'Agus Salim', 'father_phone' => '081222333003', 'mother' => 'Rina Kartika', 'mother_phone' => '081333444003'],
            ['name' => 'Sang Il', 'username' => 'GES481104', 'email' => 'sangil@g-english.com', 'gender' => 'L', 'class' => 'Survival English SD/MI', 'paid' => 2000000, 'father' => 'Kim San', 'father_phone' => '08111222333', 'mother' => 'Yoona', 'mother_phone' => '08111222444'],
            ['name' => 'Sunyg Il', 'username' => 'GES481105', 'email' => 'sunygil@g-english.com', 'gender' => 'P', 'class' => 'Survival English SMP/MTs', 'paid' => 1500000, 'father' => 'Kim San', 'father_phone' => '08111222333', 'mother' => 'Yoona', 'mother_phone' => '08111222444'],
            ['name' => 'Yoon Il', 'username' => 'GES481106', 'email' => 'yoonil@g-english.com', 'gender' => 'L', 'class' => 'Survival English SMA/MA', 'paid' => 1000000, 'father' => 'Kim San', 'father_phone' => '08111222333', 'mother' => 'Yoona', 'mother_phone' => '08111222444'],
            ['name' => 'Alya Putri', 'username' => 'GES481107', 'email' => 'alya@g-english.com', 'gender' => 'P', 'class' => 'Survival English SD/MI', 'paid' => 3100000, 'father' => 'Rahmat Hidayat', 'father_phone' => '081222333007', 'mother' => 'Nadia Sari', 'mother_phone' => '081333444007'],
            ['name' => 'Rafka Pratama', 'username' => 'GES481108', 'email' => 'rafka@g-english.com', 'gender' => 'L', 'class' => 'Survival English SMP/MTs', 'paid' => 1800000, 'father' => 'Dedi Kurniawan', 'father_phone' => '081222333008', 'mother' => 'Lina Marlina', 'mother_phone' => '081333444008'],
        ];

        $students = [];
        foreach ($internalSeeds as $idx => $sd) {
            $su = User::updateOrCreate(
                ['username' => $sd['username']],
                [
                    'name' => $sd['name'],
                    'email' => $sd['email'],
                    'password' => $password,
                    'role' => 'siswa',
                ]
            );

            $student = Student::updateOrCreate(
                ['nis' => $sd['username']],
                [
                    'user_id' => $su->id,
                    'student_type' => 'internal',
                    'name' => $sd['name'],
                    'gender' => $sd['gender'],
                    'birth_date' => '2014-05-0' . ($idx + 1),
                    'phone' => '08511122200' . ($idx + 1),
                    'class_name' => $sd['class'],
                    'course_level' => 'Survival English',
                    'father_name' => $sd['father'],
                    'father_phone' => $sd['father_phone'],
                    'mother_name' => $sd['mother'],
                    'mother_phone' => $sd['mother_phone'],
                    'parent_name' => $sd['father'],
                    'parent_phone' => $sd['father_phone'],
                    'is_active' => true,
                ]
            );
            $students[$sd['name']] = $student;

            // Ensure known password on linked parent account
            User::where('username', $sd['father_phone'])->update(['password' => $password, 'role' => 'parent']);

            // Payments
            $totalFee = $sd['name'] === 'IU' ? 2500000 : 3100000;
            $remaining = max(0, $totalFee - $sd['paid']);

            StudentPayment::updateOrCreate(
                ['student_id' => $student->id, 'status' => 'paid'],
                [
                    'amount' => $sd['paid'],
                    'paid_at' => Carbon::now()->subDays(5),
                    'notes' => 'Pembayaran Kursus. (Total Biaya Rp ' . number_format($totalFee, 0, ',', '.') . ')',
                ]
            );

            if ($remaining > 0) {
                StudentPayment::updateOrCreate(
                    ['student_id' => $student->id, 'status' => 'unpaid'],
                    [
                        'amount' => $remaining,
                        'due_date' => Carbon::now()->addMonths(2),
                        'notes' => 'Sisa Tagihan / Saldo Pembayaran Kursus',
                    ]
                );
            }
        }

        // 6. Classes (1 program tunggal: Survival English jenjang SD/MI, SMP/MTs, SMA/MA)
        // Migrasi nama kelas lama agar tidak dobel
        foreach (['Survival English SD' => 'Survival English SD/MI', 'Survival English SMP' => 'Survival English SMP/MTs', 'Survival English SMA' => 'Survival English SMA/MA'] as $oldName => $newName) {
            if (ClassRoom::where('name', $oldName)->exists() && !ClassRoom::where('name', $newName)->exists()) {
                ClassRoom::where('name', $oldName)->update(['name' => $newName]);
            }
        }
        ClassRoom::where('name', 'Speaking Kids & Junior')->delete();

        $classDefs = [
            ['name' => 'Survival English SD/MI', 'subject_id' => $subSurvival->id, 'tutor' => 'Ms Hana', 'students' => ['Kyuhyun', 'Sang Il', 'Alya Putri'], 'days' => ['Monday', 'Wednesday'], 'time' => ['14:00', '15:30']],
            ['name' => 'Survival English SMP/MTs', 'subject_id' => $subSurvival->id, 'tutor' => 'Mr Ichsan', 'students' => ['IU', 'Sunyg Il', 'Rafka Pratama'], 'days' => ['Tuesday', 'Thursday'], 'time' => ['15:30', '17:00']],
            ['name' => 'Survival English SMA/MA', 'subject_id' => $subSurvival->id, 'tutor' => 'Mr Rizhan', 'students' => ['Carmen', 'Yoon Il'], 'days' => ['Monday', 'Thursday'], 'time' => ['16:00', '17:30']],
        ];

        $classes = [];
        foreach ($classDefs as $cd) {
            $cls = ClassRoom::updateOrCreate(
                ['name' => $cd['name']],
                [
                    'academic_year_id' => $ay->id,
                    'subject_id' => $cd['subject_id'],
                    'tutor_id' => $tutors[$cd['tutor']]->id,
                    'schedule' => implode(' & ', $cd['days']) . ', ' . $cd['time'][0] . '-' . $cd['time'][1],
                    'capacity' => 15,
                    'is_active' => true,
                ]
            );
            $classes[$cd['name']] = $cls;

            $sync = [];
            foreach ($cd['students'] as $sName) {
                $sync[$students[$sName]->id] = ['enrolled_at' => '2026-07-14'];
            }
            $cls->students()->sync($sync);

            ClassSchedule::updateOrCreate(
                ['class_id' => $cls->id, 'tutor_id' => $tutors[$cd['tutor']]->id],
                [
                    'schedule_items' => [
                        ['day' => $cd['days'][0], 'start_time' => $cd['time'][0], 'end_time' => $cd['time'][1]],
                        ['day' => $cd['days'][1], 'start_time' => $cd['time'][0], 'end_time' => $cd['time'][1]],
                    ],
                ]
            );

            Schedule::updateOrCreate(
                ['class_room_id' => $cls->id, 'user_id' => $tutorUsers[$cd['tutor']]->id, 'date' => Carbon::today()->toDateString()],
                ['start_time' => $cd['time'][0], 'end_time' => $cd['time'][1]]
            );
        }

        // 7. Schools (Master Data Sekolah) + External Students (SD, MI, SMP, MTs)
        $schoolSeeds = [
            ['code' => '48001', 'name' => 'SDN 1 Tanjung', 'email' => 'sdn1tanjung@g-english.com', 'level' => 'SD', 'grades' => ['Kelas 4', 'Kelas 5', 'Kelas 6']],
            ['code' => '48002', 'name' => 'MIN 1 Tabalong', 'email' => 'min1tabalong@g-english.com', 'level' => 'MI', 'grades' => ['Kelas 4', 'Kelas 5', 'Kelas 6']],
            ['code' => '48003', 'name' => 'SMPN 1 Tanjung', 'email' => 'smpn1tanjung@g-english.com', 'level' => 'SMP', 'grades' => ['Kelas 7', 'Kelas 8', 'Kelas 9']],
            ['code' => '48004', 'name' => 'MTsN 1 Tabalong', 'email' => 'mtsn1tabalong@g-english.com', 'level' => 'MTs', 'grades' => ['Kelas 7', 'Kelas 8', 'Kelas 9']],
        ];

        $extCounter = 1;
        foreach ($schoolSeeds as $sc) {
            $schoolUser = User::updateOrCreate(
                ['username' => $sc['code']],
                [
                    'name' => $sc['name'],
                    'email' => $sc['email'],
                    'password' => $password,
                    'role' => 'school',
                ]
            );

            foreach ($sc['grades'] as $grade) {
                $gradeNum = (int) preg_replace('/\D+/', '', $grade);
                $regCode = sprintf('BC-K%d-%03d', $gradeNum, $extCounter);
                $nisn = str_pad((string) (3010000000 + $extCounter), 10, '0', STR_PAD_LEFT);
                $studentName = "Peserta {$sc['level']} {$gradeNum} #" . $extCounter;

                Student::updateOrCreate(
                    ['nis' => $regCode],
                    [
                        'school_user_id' => $schoolUser->id,
                        'nisn' => $nisn,
                        'competition_code' => $regCode,
                        'student_type' => 'external',
                        'name' => $studentName,
                        'gender' => $extCounter % 2 === 0 ? 'P' : 'L',
                        'phone' => '08520000' . str_pad((string) $extCounter, 4, '0', STR_PAD_LEFT),
                        'education_level' => $sc['level'],
                        'course_level' => BrainChampionRegistration::EVENT_TAG,
                        'class_name' => $grade,
                        'school_name' => $sc['name'],
                        'parent_name' => 'Wali ' . $studentName,
                        'parent_phone' => '08530000' . str_pad((string) $extCounter, 4, '0', STR_PAD_LEFT),
                        'is_active' => true,
                    ]
                );

                Admission::updateOrCreate(
                    ['registration_number' => $regCode],
                    [
                        'nisn' => $nisn,
                        'competition_code' => $regCode,
                        'student_type' => 'external',
                        'school_user_id' => $schoolUser->id,
                        'name' => $studentName,
                        'phone' => '08520000' . str_pad((string) $extCounter, 4, '0', STR_PAD_LEFT),
                        'level' => $sc['level'],
                        'school_name' => $sc['name'],
                        'previous_school' => $sc['name'],
                        'class_name' => $grade,
                        'course_level' => BrainChampionRegistration::EVENT_TAG,
                        'education_level' => $sc['level'],
                        'parent_name' => 'Wali ' . $studentName,
                        'parent_phone' => '08530000' . str_pad((string) $extCounter, 4, '0', STR_PAD_LEFT),
                        'status' => 'approved',
                        'tuition_fee' => 0,
                        'registration_fee' => 0,
                        'total_fee' => 0,
                        'amount_paid' => 0,
                    ]
                );

                $extCounter++;
            }
        }

        // Sample Pending & Rejected Internal Admissions
        Admission::updateOrCreate(
            ['registration_number' => 'ADM-2026-001'],
            [
                'student_type' => 'internal',
                'name' => 'Nabila Zahra',
                'email' => 'nabila@example.com',
                'phone' => '081299990001',
                'level' => 'SMP',
                'school_name' => 'SMPN 2 Tanjung',
                'class_name' => 'Survival English SMP/MTs',
                'status' => 'pending',
                'tuition_fee' => 3000000,
                'registration_fee' => 100000,
                'has_discount' => true,
                'total_fee' => 3100000,
                'payment_method' => 'installment',
                'amount_paid' => 1000000,
                'notes' => 'Menunggu verifikasi admin.',
            ]
        );
        Admission::updateOrCreate(
            ['registration_number' => 'ADM-2026-002'],
            [
                'student_type' => 'internal',
                'name' => 'Dimas Saputra',
                'email' => 'dimas@example.com',
                'phone' => '081299990002',
                'level' => 'SD',
                'school_name' => 'SDN 3 Tanjung',
                'class_name' => 'Survival English SD/MI',
                'status' => 'rejected',
                'tuition_fee' => 4000000,
                'registration_fee' => 100000,
                'has_discount' => false,
                'total_fee' => 4100000,
                'payment_method' => 'installment',
                'amount_paid' => 0,
                'notes' => 'Batal mendaftar.',
            ]
        );

        // 8. Attendances (Students & Tutors)
        $statuses = ['present', 'present', 'late', 'excused'];
        $sIdx = 0;
        foreach ($students as $st) {
            $clsId = $classes[$st->class_name]->id ?? $classes['Survival English SD/MI']->id;
            $tutId = $classes[$st->class_name]->tutor_id ?? $tutors['Ms Hana']->id;
            Attendance::updateOrCreate(
                ['role_type' => 'student', 'student_id' => $st->id, 'date' => Carbon::today()->toDateString()],
                [
                    'class_id' => $clsId,
                    'tutor_id' => $tutId,
                    'status' => $statuses[$sIdx % count($statuses)],
                    'time_in' => '14:05:00',
                    'time_out' => '15:30:00',
                    'notes' => 'Sesi reguler',
                ]
            );
            $sIdx++;
        }
        foreach ($tutors as $tName => $tut) {
            Attendance::updateOrCreate(
                ['role_type' => 'tutor', 'tutor_id' => $tut->id, 'date' => Carbon::today()->toDateString()],
                [
                    'user_id' => $tutorUsers[$tName]->id,
                    'status' => 'present',
                    'time_in' => '13:45:00',
                    'time_out' => '17:00:00',
                    'notes' => 'Hadir mengajar tepat waktu',
                ]
            );
        }

        // 9. Journals & Journal Validations (termasuk jurnal Ms Hana!)
        $journalSeeds = [
            ['class' => 'Survival English SD/MI', 'tutor' => 'Mr Bakeri', 'topic' => 'Basic Vocabulary & Daily Routines', 'jp' => 2, 'status' => 'validated', 'daysAgo' => 3],
            ['class' => 'Survival English SMP/MTs', 'tutor' => 'Mr Ichsan', 'topic' => 'Simple Past vs Present Perfect', 'jp' => 2, 'status' => 'submitted', 'daysAgo' => 1],
            ['class' => 'Survival English SMA/MA', 'tutor' => 'Mr Rizhan', 'topic' => 'Academic Reading Comprehension', 'jp' => 2, 'status' => 'validated', 'daysAgo' => 2],
            ['class' => 'Survival English SD/MI', 'tutor' => 'Ms Hana', 'topic' => 'Fun Storytelling & Pronunciation Drill', 'jp' => 2, 'status' => 'validated', 'daysAgo' => 2],
            ['class' => 'Survival English SD/MI', 'tutor' => 'Ms Hana', 'topic' => 'Roleplay: At The Restaurant', 'jp' => 2, 'status' => 'submitted', 'daysAgo' => 0],
        ];

        $firstJournal = null;
        foreach ($journalSeeds as $js) {
            $j = Journal::updateOrCreate(
                ['class_id' => $classes[$js['class']]->id, 'topic' => $js['topic']],
                [
                    'tutor_id' => $tutors[$js['tutor']]->id,
                    'date' => Carbon::today()->subDays($js['daysAgo'])->toDateString(),
                    'description' => 'Pembelajaran aktif dengan latihan percakapan dan kuis singkat.',
                    'materials' => 'Modul G-English Unit ' . ($js['daysAgo'] + 1),
                    'jp_count' => $js['jp'],
                    'status' => $js['status'],
                ]
            );
            $firstJournal ??= $j;

            if ($js['status'] === 'validated') {
                JournalValidation::updateOrCreate(
                    ['journal_id' => $j->id],
                    [
                        'validated_by' => $admin->id,
                        'status' => 'approved',
                        'notes' => 'Disetujui sesuai laporan kegiatan.',
                        'validated_at' => Carbon::now(),
                    ]
                );
            }
        }

        // 10. Evaluations & Honors
        foreach ($students as $st) {
            Evaluation::updateOrCreate(
                ['student_id' => $st->id, 'subject_id' => $subSurvival->id, 'date' => Carbon::today()->subDays(1)->toDateString()],
                ['score' => rand(82, 98), 'notes' => 'Perkembangan sangat baik dan aktif di kelas.']
            );
        }

        Honor::updateOrCreate(
            ['student_id' => $students['Alya Putri']->id, 'title' => 'Best Speaker of The Month'],
            [
                'journal_id' => $firstJournal?->id,
                'type' => 'award',
                'description' => 'Penghargaan keaktifan berbicara di kelas Ms Hana.',
                'date' => Carbon::today()->toDateString(),
                'certificate_number' => 'CERT-GE-2026-001',
            ]
        );
        Honor::updateOrCreate(
            ['student_id' => $students['Carmen']->id, 'title' => 'Top Scorer Survival English'],
            [
                'journal_id' => $firstJournal?->id,
                'type' => 'achievement',
                'description' => 'Nilai tertinggi ujian evaluasi bulanan.',
                'date' => Carbon::today()->toDateString(),
                'certificate_number' => 'CERT-GE-2026-002',
            ]
        );

        // 11. Tutor Salaries & Expenses
        $month = (int) Carbon::now()->month;
        $year = (int) Carbon::now()->year;
        foreach ($tutors as $tName => $tut) {
            $rate = $tut->grade === 'A' ? 50000 : 35000;
            $jp = $tName === 'Ms Hana' ? 24 : 20;
            TutorSalary::updateOrCreate(
                ['tutor_id' => $tut->id, 'month' => $month, 'year' => $year],
                [
                    'total_jp' => $jp,
                    'total_amount' => $jp * $rate,
                    'status' => $tName === 'Ms Hana' ? 'paid' : 'pending',
                    'paid_at' => $tName === 'Ms Hana' ? Carbon::today() : null,
                ]
            );
        }

        $expenseSeeds = [
            ['category' => 'Operasional', 'amount' => 750000, 'description' => 'Listrik & Internet bulanan kantor G-English', 'daysAgo' => 4],
            ['category' => 'Modul & Cetak', 'amount' => 450000, 'description' => 'Cetak buku modul Speaking Kids Ms Hana', 'daysAgo' => 2],
            ['category' => 'Event Brain Champion', 'amount' => 1250000, 'description' => 'Perlengkapan piala dan kartu peserta', 'daysAgo' => 1],
        ];
        foreach ($expenseSeeds as $ex) {
            Expense::updateOrCreate(
                ['category' => $ex['category'], 'description' => $ex['description']],
                ['date' => Carbon::today()->subDays($ex['daysAgo'])->toDateString(), 'amount' => $ex['amount']]
            );
        }

        // 12. CBT Exams, Questions & Attempts (termasuk 1 locked attempt untuk tes Buka Kunci)
        $cbtReal = CbtExam::updateOrCreate(
            ['title' => 'English Quiz CBT — Survival English'],
            [
                'description' => 'Ujian evaluasi real CBT berbasis soal pilihan ganda.',
                'class_id' => $classes['Survival English SD/MI']->id,
                'subject_id' => $subSurvival->id,
                'cbt_mode' => 'internal_excel',
                'duration_minutes' => 45,
                'max_attempts' => 1,
                'is_active' => true,
            ]
        );

        $questions = [
            ['q' => 'She ___ to school every morning.', 'a' => 'go', 'b' => 'goes', 'c' => 'going', 'd' => 'gone', 'ans' => 'B'],
            ['q' => 'What is the opposite of "happy"?', 'a' => 'Joyful', 'b' => 'Glad', 'c' => 'Sad', 'd' => 'Excited', 'ans' => 'C'],
            ['q' => 'They ___ playing football right now.', 'a' => 'is', 'b' => 'am', 'c' => 'are', 'd' => 'be', 'ans' => 'C'],
            ['q' => 'Yesterday, I ___ a very interesting book.', 'a' => 'read', 'b' => 'reads', 'c' => 'reading', 'd' => 'will read', 'ans' => 'A'],
            ['q' => 'Choose the polite way to ask for help:', 'a' => 'Give me now!', 'b' => 'Could you help me, please?', 'c' => 'Do this fast!', 'd' => 'Move away!', 'ans' => 'B'],
        ];
        foreach ($questions as $q) {
            CbtQuestion::updateOrCreate(
                ['cbt_exam_id' => $cbtReal->id, 'question_text' => $q['q']],
                [
                    'option_a' => $q['a'],
                    'option_b' => $q['b'],
                    'option_c' => $q['c'],
                    'option_d' => $q['d'],
                    'correct_answer' => $q['ans'],
                    'points' => 20,
                ]
            );
        }

        CbtExam::updateOrCreate(
            ['title' => 'English Listening / Form — Survival English SD/MI'],
            [
                'description' => 'Ujian listening menggunakan Google Form.',
                'class_id' => $classes['Survival English SD/MI']->id,
                'subject_id' => $subSurvival->id,
                'cbt_mode' => 'google_form',
                'duration_minutes' => 30,
                'max_attempts' => 1,
                'is_active' => true,
            ]
        );

        CbtAttempt::updateOrCreate(
            ['cbt_exam_id' => $cbtReal->id, 'student_id' => $students['Alya Putri']->id, 'attempt_number' => 1],
            [
                'start_time' => Carbon::now()->subHours(2),
                'end_time' => Carbon::now()->subHour(),
                'status' => 'finished',
                'score' => 100,
                'is_completed' => true,
                'violation_count' => 0,
                'max_violations' => 3,
                'is_force_submitted' => false,
            ]
        );
        CbtAttempt::updateOrCreate(
            ['cbt_exam_id' => $cbtReal->id, 'student_id' => $students['Rafka Pratama']->id, 'attempt_number' => 1],
            [
                'start_time' => Carbon::now()->subMinutes(40),
                'end_time' => Carbon::now()->subMinutes(10),
                'status' => 'finished',
                'score' => 60,
                'is_completed' => true,
                'violation_count' => 3,
                'max_violations' => 3,
                'is_force_submitted' => true,
            ]
        );

        // 13. Tests, Server Sessions & Test Schedule Requests
        $testPlacement = Test::updateOrCreate(
            ['name' => 'Placement Test Survival English — Ms Hana'],
            [
                'class_id' => $classes['Survival English SD/MI']->id,
                'subject_id' => $subSurvival->id,
                'type' => 'placement',
                'date' => Carbon::today()->addDays(2)->toDateString(),
                'max_score' => 100,
            ]
        );
        $testCompetition = Test::updateOrCreate(
            ['name' => 'Tryout Brain Champion SD/MI'],
            [
                'class_id' => $classes['Survival English SD/MI']->id,
                'subject_id' => $subSurvival->id,
                'type' => 'competition',
                'date' => Carbon::today()->addDays(5)->toDateString(),
                'max_score' => 100,
            ]
        );

        $session1 = TestSession::updateOrCreate(
            ['title' => 'Sesi 1 Pagi — Brain Champion SD/MI'],
            [
                'cbt_exam_id' => $cbtReal->id,
                'test_id' => $testCompetition->id,
                'session_date' => Carbon::today()->addDays(5)->toDateString(),
                'start_time' => '08:00:00',
                'end_time' => '09:30:00',
                'server_name' => 'Server CBT-01 (Kapasitas 35)',
                'max_capacity' => 35,
                'is_active' => true,
                'notes' => 'Maksimal 35 peserta aktif bersamaan per sesi.',
            ]
        );
        $session2 = TestSession::updateOrCreate(
            ['title' => 'Sesi 2 Siang — Brain Champion SMP/MTs'],
            [
                'cbt_exam_id' => $cbtReal->id,
                'test_id' => $testCompetition->id,
                'session_date' => Carbon::today()->addDays(5)->toDateString(),
                'start_time' => '10:30:00',
                'end_time' => '12:00:00',
                'server_name' => 'Server CBT-02 (Kapasitas 25)',
                'max_capacity' => 25,
                'is_active' => true,
                'notes' => 'Khusus jenjang SMP/MTs.',
            ]
        );
        $session3 = TestSession::updateOrCreate(
            ['title' => 'Sesi Placement — Speaking Kids Ms Hana'],
            [
                'cbt_exam_id' => $cbtReal->id,
                'test_id' => $testPlacement->id,
                'session_date' => Carbon::today()->addDays(2)->toDateString(),
                'start_time' => '14:30:00',
                'end_time' => '16:00:00',
                'server_name' => 'Server Lab-A (Kapasitas 15)',
                'max_capacity' => 15,
                'is_active' => true,
                'notes' => 'Placement test kelas Ms Hana.',
            ]
        );

        $sdnUser = User::where('username', '48001')->first();
        $minUser = User::where('username', '48002')->first();
        if ($sdnUser) {
            TestScheduleRequest::updateOrCreate(
                ['test_session_id' => $session1->id, 'requester_user_id' => $sdnUser->id],
                [
                    'requester_role' => 'school',
                    'school_name' => $sdnUser->name,
                    'requested_seats' => 6,
                    'status' => 'approved',
                    'notes' => 'Delegasi 6 peserta SDN 1 Tanjung.',
                    'review_notes' => 'Disetujui sesuai kuota server.',
                    'reviewed_by' => $admin->id,
                    'reviewed_at' => now(),
                ]
            );
        }
        if ($minUser) {
            TestScheduleRequest::updateOrCreate(
                ['test_session_id' => $session1->id, 'requester_user_id' => $minUser->id],
                [
                    'requester_role' => 'school',
                    'school_name' => $minUser->name,
                    'requested_seats' => 6,
                    'status' => 'pending',
                    'notes' => 'Mohon approval jadwal sesi pagi untuk 6 peserta MIN 1 Tabalong.',
                ]
            );
        }
        if ($students['Alya Putri']->user_id) {
            TestScheduleRequest::updateOrCreate(
                ['test_session_id' => $session3->id, 'requester_user_id' => $students['Alya Putri']->user_id],
                [
                    'requester_role' => 'siswa',
                    'student_id' => $students['Alya Putri']->id,
                    'school_name' => 'Survival English SD/MI',
                    'requested_seats' => 1,
                    'status' => 'pending',
                    'notes' => 'Request jadwal tes placement kelas Ms Hana.',
                ]
            );
        }

        // Seed 2 external student attempts & evaluations so Eksternal hierarchy shows live data
        $extSample = Student::where('student_type', 'external')->take(2)->get();
        foreach ($extSample as $idx => $extSt) {
            CbtAttempt::updateOrCreate(
                ['cbt_exam_id' => $cbtReal->id, 'student_id' => $extSt->id, 'attempt_number' => 1],
                [
                    'start_time' => Carbon::now()->subHours(3),
                    'end_time' => Carbon::now()->subHours(2),
                    'status' => 'finished',
                    'score' => 85 + ($idx * 10),
                    'is_completed' => true,
                    'violation_count' => $idx,
                    'max_violations' => 3,
                    'is_force_submitted' => false,
                ]
            );
            Evaluation::updateOrCreate(
                ['student_id' => $extSt->id, 'subject_id' => $subSurvival->id, 'date' => Carbon::today()->toDateString()],
                ['score' => 88 + ($idx * 5), 'notes' => 'Peserta Brain Champion — hasil tryout sangat baik.']
            );
        }

        // 14. Sample Access Logs (hanya jika masih kosong)
        if (\App\Models\ActivityLog::count() === 0) {
            $sampleLogs = [
                [$sdnUser, 'Request Jadwal Tes', '182.8.130.91', 1],
                [$students['Alya Putri']->user, 'Kerjakan Ujian CBT', '182.2.105.134', 2],
                [$minUser, 'Kelola Peserta Sekolah', '36.75.52.237', 4],
                [$tutorUsers['Ms Hana'], 'Jurnal Mengajar', '140.213.57.55', 6],
                [$admin, 'Dashboard Admin', '114.122.232.219', 8],
            ];
            foreach ($sampleLogs as [$u, $page, $ip, $minsAgo]) {
                if ($u) {
                    \App\Models\ActivityLog::create([
                        'user_id' => $u->id,
                        'action' => 'access',
                        'page' => $page,
                        'description' => '/' . strtolower(str_replace(' ', '-', $page)),
                        'ip' => $ip,
                        'created_at' => now()->subMinutes($minsAgo),
                        'updated_at' => now()->subMinutes($minsAgo),
                    ]);
                }
            }
        }

        // 15. Settings
        $settings = [
            ['key' => 'app_name', 'value' => 'G-English', 'group' => 'general'],
            ['key' => 'app_description', 'value' => 'Sistem Administrasi G-English & Brain Champion', 'group' => 'general'],
            ['key' => 'institution_name', 'value' => 'GENZA ENGLISH', 'group' => 'general'],
            ['key' => 'institution_address', 'value' => 'Tanjung, Tabalong, Kalimantan Selatan', 'group' => 'general'],
            ['key' => 'institution_phone', 'value' => '0812-3456-7890', 'group' => 'general'],
        ];
        foreach ($settings as $st) {
            Setting::updateOrCreate(['key' => $st['key']], ['value' => $st['value'], 'group' => $st['group']]);
        }
    }
}
