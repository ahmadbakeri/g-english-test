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
use App\Models\Attendance;
use App\Models\Journal;
use App\Models\Honor;
use App\Models\Test;
use App\Models\TestScore;
use App\Models\Setting;
use App\Models\ClassSchedule;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin User ──
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'GEADMIN48',
            'email' => 'superadmin@g-english.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $admin2 = User::create([
            'name' => 'Admin',
            'username' => 'GEA48001',
            'email' => 'admin@g-english.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // ── Tutor Users ──
        $tutorUsers = [
            User::create(['name' => 'Tutor Andi', 'username' => 'GE48101', 'email' => 'andi@g-english.com', 'password' => Hash::make('password'), 'role' => 'tutor']),
            User::create(['name' => 'Tutor Budi', 'username' => 'GE48102', 'email' => 'budi@g-english.com', 'password' => Hash::make('password'), 'role' => 'tutor']),
            User::create(['name' => 'Tutor Citra', 'username' => 'GE48103', 'email' => 'citra@g-english.com', 'password' => Hash::make('password'), 'role' => 'tutor']),
        ];

        // ── Academic Year ──
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

        // ── Subjects ──
        $subject = Subject::create([
            'code' => 'SURVIVAL', 
            'name' => 'Survival English', 
            'level' => 'All Levels', 
            'description' => 'Materi bahasa Inggris dasar untuk bertahan hidup.'
        ]);

        // ── Tutors ──
        $tutors = [];
        foreach ($tutorUsers as $index => $u) {
            $tutors[] = Tutor::create([
                'user_id' => $u->id,
                'nip' => 'GE4810' . ($index + 1), // "GE48101", "GE48102", "GE48103"
                'name' => $u->name,
                'phone' => '08123456780' . ($index + 1),
                'specialization' => 'Survival English',
                'is_active' => true,
            ]);
        }

        // ── Students ──
        $students = [];
        $studentNames = ['Siswa Doni', 'Siswa Eka', 'Siswa Fani'];
        foreach ($studentNames as $index => $name) {
            // Student username will also be their NIS for login
            $nis = 'GES48110' . ($index + 1); // "GES481101", etc.
            
            // Create user for student
            $studentUser = User::create([
                'name' => $name,
                'username' => $nis,
                'email' => strtolower(str_replace(' ', '', $name)) . '@g-english.com',
                'password' => Hash::make('password'),
                'role' => 'siswa'
            ]);

            $students[] = Student::create([
                'user_id' => $studentUser->id,
                'nis' => $nis,
                'name' => $name, 
                'gender' => $index % 2 == 0 ? 'L' : 'P', 
                'birth_date' => '2015-01-0' . ($index + 1), 
                'phone' => '08511122200' . ($index + 1), 
                'parent_name' => 'Ortu ' . $name, 
                'parent_phone' => '08133344400' . ($index + 1)
            ]);
        }

        // ── Classes ──
        $classData = [
            ['name' => 'Survival English SD', 'schedule' => 'Senin & Rabu, 14:00-15:30', 'days' => ['Monday', 'Wednesday']],
            ['name' => 'Survival English SMP', 'schedule' => 'Selasa & Kamis, 14:00-15:30', 'days' => ['Tuesday', 'Thursday']],
            ['name' => 'Survival English SMA', 'schedule' => 'Jumat & Sabtu, 14:00-15:30', 'days' => ['Friday', 'Saturday']],
        ];

        $classes = [];
        foreach ($classData as $index => $cd) {
            $class = ClassRoom::create([
                'name' => $cd['name'],
                'academic_year_id' => $ay->id,
                'subject_id' => $subject->id,
                'tutor_id' => $tutors[$index]->id,
                'schedule' => $cd['schedule'],
                'capacity' => 10,
                'is_active' => true,
            ]);

            // Assign 1 student per class
            $class->students()->attach([
                $students[$index]->id => ['enrolled_at' => '2025-07-14'],
            ]);

            // ── Class Schedules (Master Data) ──
            $scheduleItems = [];
            foreach ($cd['days'] as $day) {
                $scheduleItems[] = [
                    'day' => $day,
                    'start_time' => '14:00',
                    'end_time' => '15:30'
                ];
            }

            ClassSchedule::create([
                'class_id' => $class->id,
                'tutor_id' => $tutors[$index]->id,
                'schedule_items' => $scheduleItems,
            ]);

            $classes[] = $class;
        }

        // ── Settings ──
        Setting::create(['key' => 'app_name', 'value' => 'G-English', 'group' => 'general']);
        Setting::create(['key' => 'app_description', 'value' => 'Sistem Administrasi G-English', 'group' => 'general']);
        Setting::create(['key' => 'institution_name', 'value' => 'G-ENGLISH', 'group' => 'general']);
        Setting::create(['key' => 'institution_address', 'value' => 'Jl. Pendidikan No. 1', 'group' => 'general']);
        Setting::create(['key' => 'institution_phone', 'value' => '021-1234567', 'group' => 'general']);
    }
}
