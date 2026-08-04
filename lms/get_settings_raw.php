<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$envFile = __DIR__ . '/.env';
if (!file_exists($envFile)) {
    die("Error: .env file not found\n");
}

$env = [];
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
        $env[trim($parts[0])] = trim($parts[1]);
    }
}

try {
    $db_host = $env['DB_HOST'] ?? '127.0.0.1';
    $db_name = $env['DB_DATABASE'] ?? 'u181357068_Eduction2';
    $db_user = $env['DB_USERNAME'] ?? 'u181357068_Eduction2';
    $db_pass = $env['DB_PASSWORD'] ?? 'Eduction2@1234';

    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo "Call logs details:\n";
    $stmt = $pdo->query("SELECT * FROM call_logs ORDER BY id DESC LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($rows);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
