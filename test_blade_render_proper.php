<?php
/**
 * Render step_2 blade template programmatically to verify is_demo value.
 */

$baseDir = __DIR__;
if (file_exists(__DIR__ . '/lms')) {
    $baseDir = __DIR__ . '/lms';
}

// Bootstrap Laravel
require $baseDir . '/vendor/autoload.php';
$app = require_once $baseDir . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Set query parameter in the request global
request()->query->set('is_demo', '1');
request()->offsetSet('is_demo', '1');

$meeting = \App\Models\Meeting::first();
$time = \App\Models\MeetingTime::first();
$instructor = \App\User::find(923);

$html = view('design_1.web.users.meeting.steps.step_2', [
    'meeting' => $meeting,
    'selectedTime' => $time,
    'selectedDate' => time(),
    'allowInPersonType' => true,
    'instructor' => $instructor
])->render();

echo "HTML length: " . strlen($html) . "\n";
echo "Has is_demo = 1 in input: " . (strpos($html, 'name="is_demo" value="1"') !== false ? "YES" : "NO") . "\n";
echo "Has is_demo = 1 in back link: " . (strpos($html, 'is_demo=1') !== false ? "YES" : "NO") . "\n";
