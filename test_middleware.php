<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$a = \App\Models\CbtAttempt::find(13);
$s = new \App\Services\ExamService();
echo 'Expired? ' . ($s->isTimeExpired($a) ? 'YES' : 'NO') . "\n";
echo 'Rem: ' . $s->getTimeRemaining($a) . "\n";
