<?php
require 'vendor/autoload.php';
$start = \Carbon\Carbon::now();
$end = (clone $start)->addMinutes(90);
echo 'end->diffInSeconds(start): ' . $end->diffInSeconds($start) . "\n";
echo 'start->diffInSeconds(end): ' . $start->diffInSeconds($end) . "\n";
