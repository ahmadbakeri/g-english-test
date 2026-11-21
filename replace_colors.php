<?php

$files = [
    'resources/js/exam-engine.js',
    'resources/views/cbt/partials/navigation.blade.php',
    'resources/views/cbt/exam.blade.php',
    'resources/views/cbt/partials/question-card.blade.php'
];

foreach($files as $file) {
    $content = file_get_contents($file);
    $content = str_replace('brand-navy', '[#1a368b]', $content);
    $content = str_replace('brand-gold', '[#fbb900]', $content);
    file_put_contents($file, $content);
}

echo "Replaced colors successfully.\n";
