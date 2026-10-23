<?php
/**
 * Script to list all projects under the Agora account using dev/v1
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use Illuminate\Support\Facades\Http;

$settings = getFeaturesSettings();
$customerId = !empty($settings['agora_customer_id']) ? trim($settings['agora_customer_id']) : '85b523942ce843899d8ff5d9a6adb4b8';
$customerSecret = !empty($settings['agora_customer_secret']) ? trim($settings['agora_customer_secret']) : '8a4f0ff244514f34b81b40cee792a995';

$authHeader = 'Basic ' . base64_encode("{$customerId}:{$customerSecret}");
$url = 'https://api.agora.io/dev/v1/projects';

echo "Querying Agora projects via dev/v1...\n";
try {
    $response = Http::withHeaders([
        'Authorization' => $authHeader,
    ])->get($url);

    echo "Status code: " . $response->status() . "\n";
    echo "Response body:\n";
    $json = $response->json();
    if (isset($json['projects'])) {
        foreach ($json['projects'] as $p) {
            echo "Project Name: " . $p['name'] . "\n";
            echo "  App ID: " . $p['id'] . "\n";
            echo "  Status: " . ($p['status'] ?? 'N/A') . "\n";
            echo "  Sign Key (Cert): " . ($p['sign_key'] ?? 'N/A') . "\n";
        }
    } else {
        print_r($json);
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
