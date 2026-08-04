<?php
/**
 * CLI script to dump Agora settings
 */
require __DIR__ . '/bootstrap/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$settings = getFeaturesSettings();
echo "\n--- AGORA SETTINGS ---\n";
print_r($settings);
echo "\n----------------------\n";
