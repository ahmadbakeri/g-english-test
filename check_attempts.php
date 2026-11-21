<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$attempts = \App\Models\CbtAttempt::with('exam', 'student.user')->get();
foreach ($attempts as $a) {
    echo "ID:{$a->id} | Exam:" . ($a->exam?->title ?? '?') . " | Student:" . ($a->student?->user?->name ?? '?') . " | Status:{$a->status} | Done:" . ($a->is_completed ? 'Y' : 'N') . " | AttemptNum:{$a->attempt_number}\n";
}
