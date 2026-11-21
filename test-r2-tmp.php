<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $url = Illuminate\Support\Facades\Storage::disk('r2')->temporaryUrl('test.txt', now()->addMinutes(5));
    echo 'SUCCESS: ' . $url;
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage();
}
