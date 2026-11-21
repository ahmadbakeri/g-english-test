<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$subjects = \App\Models\Subject::all();
foreach($subjects as $subject) {
    // delete old dummy materials if any
    \App\Models\StudyMaterial::where('subject_id', $subject->id)->delete();
    
    for($i=1; $i<=3; $i++) {
        \App\Models\StudyMaterial::create([
            'subject_id' => $subject->id,
            'title' => 'Materi ' . $subject->name . ' - Bagian ' . $i,
            'description' => 'Ini adalah deskripsi materi pembelajaran online untuk mata pelajaran ' . $subject->name . ' bagian ke-' . $i . '. Materi ini mencakup video pembelajaran, visualisasi, dan ringkasan lengkap untuk menunjang proses belajar secara offline maupun online.',
            'youtube_url' => 'https://www.youtube.com/watch?v=lTRiuFIWV54',
            'is_published' => true,
        ]);
    }
}
echo "Dummy materials created successfully.\n";
