<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$materials = \App\Models\StudyMaterial::all();
$types = ['video', 'pdf', 'mindmap'];
$i = 0;
foreach($materials as $material) {
    $material->type = $types[$i % 3];
    $material->save();
    $i++;
}
echo "Dummy types updated successfully.\n";
