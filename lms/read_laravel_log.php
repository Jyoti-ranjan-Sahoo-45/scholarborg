<?php
/**
 * Script to read the end of laravel.log with exception search
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

$logFile = __DIR__ . '/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    die("Log file not found at $logFile\n");
}

$lines = 150;
$data = file($logFile);
$count = count($data);
$start = max(0, $count - $lines);

echo "\n--- ERROR LOG SEARCH ---\n";
for ($i = $start; $i < $count; $i++) {
    if (strpos(strtolower($data[$i]), 'recording') !== false || strpos(strtolower($data[$i]), 'agora') !== false) {
        echo $data[$i];
    }
}
echo "\n----------------------------------------\n";
