<?php

namespace Database\Seeders;

use App\Models\CbtExam;
use App\Models\CbtQuestion;
use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CbtTestSeeder extends Seeder
{
    public function run(): void
    {
        // Create Test Student User
        $user = User::firstOrCreate(
            ['username' => 'teststudent'],
            [
                'name' => 'Test Student',
                'email' => 'student@test.com',
                'password' => Hash::make('password'),
                'role' => 'siswa', // The role in this project is 'siswa'
            ]
        );

        // Try to find a class and subject
        $class = ClassRoom::first() ?? ClassRoom::create(['name' => 'Kelas X CBT']);
        $subject = Subject::first() ?? Subject::create(['name' => 'Bahasa Inggris', 'code' => 'ENG']);

        // Create Student Profile
        $student = Student::firstOrCreate(
            ['user_id' => $user->id],
            [
                'name' => 'Test Student',
                'nis' => '12345678',
                'gender' => 'L',
            ]
        );

        // Attach student to class if not attached
        if (!$student->classes()->where('class_id', $class->id)->exists()) {
            $student->classes()->attach($class->id);
        }

        // Create Cbt Exam
        $exam = CbtExam::firstOrCreate(
            ['title' => 'Ujian Akhir Semester Test CBT'],
            [
                'description' => 'Ujian ini digunakan untuk testing fase 6.',
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'duration_minutes' => 5, // Durasi pendek untuk testing timer
                'start_time' => now()->subMinutes(10),
                'end_time' => now()->addDays(1),
                'is_active' => true,
                'max_attempts' => 3,
                'shuffle_questions' => true,
                'shuffle_options' => true,
            ]
        );

        // Create 3 simple questions
        if ($exam->questions()->count() == 0) {
            CbtQuestion::create([
                'cbt_exam_id' => $exam->id,
                'question_text' => '<p>Apa ibukota dari Indonesia?</p>',
                'option_a' => 'Jakarta',
                'option_b' => 'Bandung',
                'option_c' => 'Surabaya',
                'option_d' => 'Medan',
                'option_e' => 'Bali',
                'correct_answer' => 'A',
                'points' => 10,
            ]);

            CbtQuestion::create([
                'cbt_exam_id' => $exam->id,
                'question_text' => '<p>Manakah dari berikut ini yang merupakan bahasa pemrograman?</p>',
                'option_a' => 'HTML',
                'option_b' => 'PHP',
                'option_c' => 'CSS',
                'option_d' => 'MySQL',
                'option_e' => 'XML',
                'correct_answer' => 'B',
                'points' => 10,
            ]);

            CbtQuestion::create([
                'cbt_exam_id' => $exam->id,
                'question_text' => '<p>Berapakah hasil dari 2 + 2?</p>',
                'option_a' => '1',
                'option_b' => '2',
                'option_c' => '3',
                'option_d' => '4',
                'option_e' => '5',
                'correct_answer' => 'D',
                'points' => 10,
            ]);
        }
    }
}
