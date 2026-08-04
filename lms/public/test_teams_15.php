<?php

if (function_exists('opcache_reset')) {
    opcache_reset();
}

// Bootstrap Laravel dynamically depending on directory depth
if (file_exists(__DIR__.'/vendor/autoload.php')) {
    require __DIR__.'/vendor/autoload.php';
    $app = require_once __DIR__.'/bootstrap/app.php';
} else {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
}

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Sessions\MicrosoftTeams;
use App\Models\Session;

try {
    echo "<h1>Microsoft Teams - 15 Simultaneous Meetings Creation Test</h1>";

    $webinar = \App\Models\Webinar::first();
    if (!$webinar) {
        throw new Exception("No webinars found in the database. Please create a course/webinar in your admin panel before running this test.");
    }

    echo "Using Webinar ID: " . $webinar->id . "<br>";
    echo "Generating 15 meetings... Please wait...<br><br>";

    $links = [];
    $teams = new MicrosoftTeams();

    for ($i = 1; $i <= 15; $i++) {
        $dbSession = Session::create([
            'creator_id' => $webinar->creator_id,
            'webinar_id' => $webinar->id,
            'chapter_id' => null,
            'date' => time() + 3600 + ($i * 60), // unique start time
            'duration' => 60,
            'session_api' => 'microsoft_teams',
            'status' => 'active',
            'created_at' => time()
        ]);

        $dbSession->update(['title' => "Scholarb Test Meeting #{$i}"]);

        $result = $teams->makeMeeting($dbSession);
        if ($result) {
            $dbSession->refresh();
            $links[] = [
                'number' => $i,
                'link' => $dbSession->link,
                'zoom_id' => $dbSession->zoom_id
            ];
        } else {
            echo "❌ Failed to generate Meeting #{$i}<br>";
        }

        // Clean up immediately from DB (the Teams link remains active on MS servers)
        $dbSession->delete();
    }

    echo "<h2>✅ SUCCESS! Generated 15 Active Microsoft Teams Meetings:</h2>";
    echo "<ol>";
    foreach ($links as $item) {
        echo "<li style='margin-bottom: 8px;'><b>Meeting #{$item['number']}:</b> <a href='{$item['link']}' target='_blank'>Click to Join Meeting #{$item['number']}</a><br><small style='color: #666;'>Teams ID: {$item['zoom_id']}</small></li>";
    }
    echo "</ol>";

} catch (Exception $e) {
    echo "<h3>❌ Error: " . $e->getMessage() . "</h3>";
}
