<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$attempt = \App\Models\CbtAttempt::find(13);
echo "Questions: " . $attempt->exam->questions->count() . "\n";
