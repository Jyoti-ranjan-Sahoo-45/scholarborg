<?php
/**
 * Test Agora Cloud Recording acquire request
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
$appId = !empty($settings['agora_app_id']) ? trim($settings['agora_app_id']) : '';
$customerId = !empty($settings['agora_customer_id']) ? trim($settings['agora_customer_id']) : '';
$customerSecret = !empty($settings['agora_customer_secret']) ? trim($settings['agora_customer_secret']) : '';

echo "App ID: $appId\n";
echo "Customer ID: $customerId\n";

$acquireUrl = "https://api.agora.io/v1/apps/{$appId}/cloud_recording/acquire";
$authHeader = 'Basic ' . base64_encode("{$customerId}:{$customerSecret}");

echo "Auth Header: $authHeader\n";

try {
    $response = Http::withHeaders([
        'Authorization' => $authHeader,
        'Content-Type' => 'application/json',
    ])->post($acquireUrl, [
        'cname' => 'test_channel_' . time(),
        'uid' => '10000',
        'clientRequest' => [
            'resourceExpiredHour' => 24,
            'scene' => 0
        ]
    ]);

    echo "Status code: " . $response->status() . "\n";
    echo "Response body:\n" . $response->body() . "\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
