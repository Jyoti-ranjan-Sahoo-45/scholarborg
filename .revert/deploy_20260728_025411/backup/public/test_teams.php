<?php

// Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Sessions\MicrosoftTeams;
use App\Models\Session;

try {
    echo "<h1>Microsoft Teams Integration Backend Test</h1>";

    // 1. Check settings in DB
    $settings = getFeaturesSettings();
    $clientId = !empty($settings['microsoft_teams_client_id']) ? trim($settings['microsoft_teams_client_id']) : '';
    $clientSecret = !empty($settings['microsoft_teams_client_secret']) ? trim($settings['microsoft_teams_client_secret']) : '';
    $tenantId = !empty($settings['microsoft_teams_tenant_id']) ? trim($settings['microsoft_teams_tenant_id']) : '';
    $fallbackEmail = !empty($settings['microsoft_teams_fallback_email']) ? trim($settings['microsoft_teams_fallback_email']) : '';

    echo "<h3>1. Checking Configuration Settings in Database:</h3>";
    echo "<ul>";
    echo "<li><b>Client ID:</b> " . ($clientId ? "Configured (" . substr($clientId, 0, 5) . "...)" : "MISSING") . "</li>";
    echo "<li><b>Client Secret:</b> " . ($clientSecret ? "Configured (" . substr($clientSecret, 0, 5) . "...)" : "MISSING") . "</li>";
    echo "<li><b>Tenant ID:</b> " . ($tenantId ? "Configured (" . substr($tenantId, 0, 5) . "...)" : "MISSING") . "</li>";
    echo "<li><b>Fallback Email:</b> " . ($fallbackEmail ? $fallbackEmail : "MISSING") . "</li>";
    echo "</ul>";

    if (empty($clientId) || empty($clientSecret) || empty($tenantId) || empty($fallbackEmail)) {
        throw new Exception("Microsoft Teams credentials are not complete in settings.");
    }

    // 2. Create a temporary session record in DB
    echo "Creating temporary session record in database...<br>";
    $dbSession = Session::create([
        'creator_id' => 1, // Admin user
        'webinar_id' => 0,
        'chapter_id' => 0,
        'date' => time() + 3600, // Starts in 1 hour
        'duration' => 60,
        'session_api' => 'microsoft_teams',
        'status' => 'active',
        'created_at' => time()
    ]);
    
    // Set title
    $dbSession->update(['title' => 'Scholarb Backend Test Teams Meeting']);

    echo "Invoking MicrosoftTeams::makeMeeting()...<br>";
    $teams = new MicrosoftTeams();
    $result = $teams->makeMeeting($dbSession);

    if ($result) {
        $dbSession->refresh();
        echo "<h3>✅ SUCCESS! Meeting Created Successfully.</h3>";
        echo "<p><b>Teams Join Link:</b> <a href='" . $dbSession->link . "' target='_blank'>" . $dbSession->link . "</a></p>";
        echo "<p><b>Teams Meeting ID:</b> " . $dbSession->zoom_id . "</p>";
        echo "<p>Please click the link above to join and test the meeting features!</p>";
    } else {
        echo "<h3>❌ FAILED: Meeting was not created.</h3>";
    }

    // Clean up
    echo "Cleaning up temporary test session from database...<br>";
    $dbSession->delete();

} catch (Exception $e) {
    echo "<h3>❌ Error: " . $e->getMessage() . "</h3>";
    if (isset($dbSession)) {
        echo "Cleaning up temporary test session from database...<br>";
        $dbSession->delete();
    }
}
