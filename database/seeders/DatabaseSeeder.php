<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Tutor;
use App\Models\ClassRoom;
use App\Models\Setting;
use App\Models\ClassSchedule;
use App\Models\StudentPayment;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data secara eksplisit agar bersih
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        User::truncate();
        Student::truncate();
        Tutor::truncate();
        ClassRoom::truncate();
        Subject::truncate();
        AcademicYear::truncate();
        Semester::truncate();
        StudentPayment::truncate();
        ClassSchedule::truncate();
        DB::table('student_parent')->truncate();
        DB::table('class_student')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // -- Admin User --
        User::create([
            'name' => 'Super Admin',
            'username' => 'GEADMIN48',
            'email' => 'superadmin@g-english.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // -- Tutor Users --
        $tutorData = [
            ['name' => 'Mr Bakeri', 'username' => 'GE48101', 'email' => 'bakeri@g-english.com'],
            ['name' => 'Mr Ichsan', 'username' => 'GE48102', 'email' => 'ichsan@g-english.com'],
            ['name' => 'Mr Rizhan', 'username' => 'GE48103', 'email' => 'rizhan@g-english.com'],
        ];

        $tutors = [];
        foreach ($tutorData as $index => $td) {
            $u = User::create([
                'name' => $td['name'],
                'username' => $td['username'],
                'email' => $td['email'],
                'password' => Hash::make('password'),
                'role' => 'tutor'
            ]);

            $tutors[$td['name']] = Tutor::create([
                'user_id' => $u->id,
                'nip' => $td['username'],
                'name' => $td['name'],
                'phone' => '08123456780' . ($index + 1),
                'specialization' => 'All Levels',
                'is_active' => true,
            ]);
        }

        // -- Academic Year --
        $ay = AcademicYear::create([
            'name' => '2025/2026',
            'is_active' => true,
        ]);

        Semester::create([
            'academic_year_id' => $ay->id,
            'name' => 'Semester 1',
            'start_date' => '2025-07-14',
            'end_date' => '2025-12-20',
            'is_active' => true,
        ]);

        // -- Subjects --
        $subject = Subject::create([
            'code' => 'SURVIVAL', 
            'name' => 'Survival English', 
            'level' => 'All Levels', 
            'description' => 'Materi bahasa Inggris dasar untuk bertahan hidup.'
        ]);

        // -- Students & Parents --
        // Total Fee 4jt, diskon 45% = 2,2jt.
        $studentData = [
            ['name' => 'Carmen', 'level' => 'SMA', 'paid_amount' => 2200000, 'username' => 'GES481101'],
            ['name' => 'Kyuhyun', 'level' => 'SD', 'paid_amount' => 2000000, 'username' => 'GES481102'],
            ['name' => 'IU', 'level' => 'SMP', 'paid_amount' => 2500000, 'username' => 'GES481103'],
            ['name' => 'Sang Il', 'level' => 'SD', 'paid_amount' => 2000000, 'username' => 'GES481104', 'father' => 'Kim San', 'mother' => 'Yoona', 'father_phone' => '08111222333', 'mother_phone' => '08111222444'],
            ['name' => 'Sunyg Il', 'level' => 'SMP', 'paid_amount' => 1500000, 'username' => 'GES481105', 'father' => 'Kim San', 'mother' => 'Yoona', 'father_phone' => '08111222333', 'mother_phone' => '08111222444'],
            ['name' => 'Yoon il', 'level' => 'SMA', 'paid_amount' => 1000000, 'username' => 'GES481106', 'father' => 'Kim San', 'mother' => 'Yoona', 'father_phone' => '08111222333', 'mother_phone' => '08111222444'],
        ];

        $students = [];
        foreach ($studentData as $index => $sd) {
            $fatherName = $sd['father'] ?? 'Ayah ' . $sd['name'];
            $fatherPhone = $sd['father_phone'] ?? '08122233300' . ($index + 1);
            
            $motherName = $sd['mother'] ?? 'Ibu ' . $sd['name'];
            $motherPhone = $sd['mother_phone'] ?? '08133344400' . ($index + 1);
            
            // Create Student User
            $studentUser = User::create([
                'name' => $sd['name'],
                'username' => $sd['username'],
                'email' => strtolower($sd['name']) . '@g-english.com',
                'password' => Hash::make('password'),
                'role' => 'siswa'
            ]);

            // The Student model boot method automatically creates parent accounts from father_phone and mother_phone!
            $students[$sd['name']] = Student::create([
                'user_id' => $studentUser->id,
                'nis' => $sd['username'],
                'name' => $sd['name'], 
                'gender' => $index % 2 == 0 ? 'P' : 'L', 
                'birth_date' => '2015-01-0' . ($index + 1), 
                'phone' => '08511122200' . ($index + 1), 
                'father_name' => $fatherName, 
                'father_phone' => $fatherPhone,
                'mother_name' => $motherName,
                'mother_phone' => $motherPhone
            ]);

            // Track Payments
            $totalFee = 3100000; // 4jt - 25% (1jt) + 100rb pendaftaran
            if ($sd['name'] == 'IU') {
                $totalFee = 2500000; 
            }
            
            $paid = $sd['paid_amount'];
            $remaining = $totalFee - $paid;

            // Record the paid amount
            StudentPayment::create([
                'student_id' => $students[$sd['name']]->id,
                'month' => null,
                'year' => null,
                'amount' => $paid,
                'status' => 'paid',
                'paid_at' => Carbon::now(),
                'notes' => 'Pembayaran Kursus. (Total Biaya Rp '.number_format($totalFee,0,',','.').' - Diskon 25% & Pendaftaran Rp 100.000)'
            ]);

            // Record the remaining balance if any
            if ($remaining > 0) {
                StudentPayment::create([
                    'student_id' => $students[$sd['name']]->id,
                    'month' => null,
                    'year' => null,
                    'due_date' => Carbon::now()->addMonths(4),
                    'amount' => $remaining,
                    'status' => 'unpaid',
                    'notes' => 'Sisa Tagihan / Saldo Pembayaran Kursus'
                ]);
            }
        }

        $classData = [
            ['name' => 'Survival English SD', 'tutor' => 'Mr Bakeri', 'students' => ['Kyuhyun', 'Sang Il']],
            ['name' => 'Survival English SMP', 'tutor' => 'Mr Ichsan', 'students' => ['IU', 'Sunyg Il']],
            ['name' => 'Survival English SMA', 'tutor' => 'Mr Rizhan', 'students' => ['Carmen', 'Yoon il']],
        ];

        foreach ($classData as $index => $cd) {
            $class = ClassRoom::create([
                'name' => $cd['name'],
                'academic_year_id' => $ay->id,
                'subject_id' => $subject->id,
                'tutor_id' => $tutors[$cd['tutor']]->id,
                'schedule' => 'Senin & Rabu, 14:00-15:30',
                'capacity' => 10,
                'is_active' => true,
            ]);

            // Assign students
            $studentsToSync = [];
            foreach ($cd['students'] as $studentName) {
                $studentsToSync[$students[$studentName]->id] = ['enrolled_at' => '2025-07-14'];
            }
            $class->students()->attach($studentsToSync);

            ClassSchedule::create([
                'class_id' => $class->id,
                'tutor_id' => $tutors[$cd['tutor']]->id,
                'schedule_items' => [
                    ['day' => 'Monday', 'start_time' => '14:00', 'end_time' => '15:30'],
                    ['day' => 'Wednesday', 'start_time' => '14:00', 'end_time' => '15:30']
                ],
            ]);
        }

        // -- Settings --
        if (Setting::count() == 0) {
            Setting::create(['key' => 'app_name', 'value' => 'G-English', 'group' => 'general']);
            Setting::create(['key' => 'app_description', 'value' => 'Sistem Administrasi G-English', 'group' => 'general']);
            Setting::create(['key' => 'institution_name', 'value' => 'G-ENGLISH', 'group' => 'general']);
            Setting::create(['key' => 'institution_address', 'value' => 'Jl. Pendidikan No. 1', 'group' => 'general']);
            Setting::create(['key' => 'institution_phone', 'value' => '021-1234567', 'group' => 'general']);
        }
    }
}
