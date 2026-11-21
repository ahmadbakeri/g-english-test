<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$attempt = \App\Models\CbtAttempt::find(13);
$now = now();
echo $now->greaterThanOrEqualTo($attempt->end_time) ? 'TRUE' : 'FALSE';
echo "\nNow: " . $now . "\nEnd: " . $attempt->end_time;
