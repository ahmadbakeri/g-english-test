<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$action = \Filament\Actions\CreateAction::make();
echo "modalSubmitActionLabel: " . (method_exists($action, 'modalSubmitActionLabel') ? "YES\n" : "NO\n");
echo "modalCancelActionLabel: " . (method_exists($action, 'modalCancelActionLabel') ? "YES\n" : "NO\n");
echo "createAnotherAction: " . (method_exists($action, 'createAnotherAction') ? "YES\n" : "NO\n");
