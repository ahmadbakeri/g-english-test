<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$attempts = \App\Models\CbtAttempt::all();
foreach($attempts as $a) {
    echo "Attempt {$a->id} - Exam {$a->cbt_exam_id} - Qs: " . $a->exam->questions->count() . "\n";
}
