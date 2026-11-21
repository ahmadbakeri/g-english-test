<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$start = now();
$end = (clone $start)->addMinutes(90);
echo "diffInSeconds = " . $start->diffInSeconds($end) . "\n";
echo "diffInSeconds(false) = " . $start->diffInSeconds($end, false) . "\n";
