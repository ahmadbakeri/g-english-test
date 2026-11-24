<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class CarmenTesterSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::updateOrCreate(
            ['username' => 'carmen'],
            [
                'name' => 'Carmen',
                'email' => 'carmen@g-english.com',
                'password' => 'h2hcarmen',
                'role' => 'siswa',
                'email_verified_at' => now(),
            ]
        );

        $student = Student::withTrashed()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'nis' => 'CARMEN-001',
                'nisn' => '9999000001',
                'student_type' => 'internal',
                'name' => 'Carmen',
                'gender' => 'P',
                'education_level' => 'SMA',
                'course_level' => 'UTBK-SNBT Tester',
                'class_name' => 'XII SMA',
                'school_name' => 'Genza Education QA',
                'is_active' => true,
                'deleted_at' => null,
            ]
        );

        $classIds = ClassRoom::pluck('id')->all();
        if (!empty($classIds)) {
            $syncData = [];
            foreach ($classIds as $classId) {
                $syncData[$classId] = ['enrolled_at' => now()->toDateString()];
            }
            $student->classes()->syncWithoutDetaching($syncData);
        }
    }
}
