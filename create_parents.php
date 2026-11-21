<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$students = \App\Models\Student::all();
foreach ($students as $student) {
    if ($student->parent_phone) {
        $user = \App\Models\User::firstOrCreate(
            ['username' => $student->parent_phone],
            [
                'name' => $student->parent_name ?? 'Orang Tua ' . $student->name,
                'email' => 'parent_' . $student->parent_phone . '@g-english.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'parent'
            ]
        );
        // Ensure student_parent pivot is populated
        if (method_exists($student, 'parents')) {
            $student->parents()->syncWithoutDetaching([$user->id]);
        }
    }
}
echo "Parents created successfully.\n";
