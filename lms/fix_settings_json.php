<?php
/**
 * Script to fix invalid features settings JSON in the database
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Settings fix started.\n";

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

    // Reconstruct the exact settings dictionary with valid types
    $settings = [
        'agora_resolution' => null,
        'agora_max_bitrate' => 2260,
        'agora_min_bitrate' => 1130,
        'agora_frame_rate' => 15,
        'agora_live_streaming' => 0,
        'agora_chat' => 0,
        'agora_in_free_courses' => 0,
        'agora_for_meeting' => 1,
        'meeting_live_stream_type' => 'single',
        'course_live_stream_type' => 'single',
        'agora_app_id' => '53364d20895748c09c175abfeff7cdc0',
        'agora_app_certificate' => '1d53007551e24c94a45d83f41ffa0178',
        'new_interactive_file' => 0,
        'timezone_in_register' => 1,
        'timezone_in_create_webinar' => 1,
        'sequence_content_status' => 0,
        'webinar_assignment_status' => 0,
        'webinar_private_content_status' => 0,
        'disable_view_content_after_user_register' => 0,
        'course_forum_status' => 0,
        'direct_classes_payment_button_status' => 1,
        'direct_bundles_payment_button_status' => 1,
        'direct_products_payment_button_status' => 1,
        'cookie_settings_status' => 0,
        'mobile_app_status' => 0,
        'maintenance_status' => 0,
        'maintenance_access_key' => 'key',
        'extra_time_to_join_status' => 1,
        'extra_time_to_join_default_value' => 20,
        'show_other_register_method' => 1,
        'show_certificate_additional_in_register' => 0,
        'show_google_login_button' => 0,
        'show_facebook_login_button' => 0,
        'google_client_id' => null,
        'google_client_secret' => null,
        'facebook_client_id' => null,
        'facebook_client_secret' => null,
        'show_live_chat_widget' => 0,
        'cashback_active' => 0,
        'display_cashback_notice_in_the_product_page' => 0,
        'display_minimum_amount_cashback_notices' => 0,
        'available_session_apis' => ['local', 'big_blue_button', 'zoom', 'agora', 'jitsi', 'microsoft_teams'],
        'available_sources' => ['upload', 'youtube', 'vimeo', 'external_link', 'google_drive', 'iframe', 's3', 'secure_host'],
        'bunny_configs' => [],
        'select_the_role_during_registration' => ['teacher', 'organization'],
        'waitlist_status' => 0,
        'upcoming_courses_status' => 0,
        'user_register_form' => null,
        'instructor_register_form' => null,
        'organization_register_form' => null,
        'become_instructor_form' => null,
        'become_organization_form' => null,
        'frontend_coupons_status' => 1,
        'frontend_coupons_display_type' => 'before_content',
        'course_notes_status' => 0,
        'course_notes_attachment' => 0,
        'course_recent_reviews_status' => 0,
        'zoom_client_id' => null,
        'zoom_client_secret' => null,
        'zoom_account_id' => null,
        'bigbluebutton_server_base_url' => null,
        'bigbluebutton_security_salt' => null,
        'jitsi_live_url' => null,
        'microsoft_teams_client_id' => '8879ec02-43e0-4108-b1a3-1e06558ed2f7',
        'microsoft_teams_client_secret' => 'oaZ8Q~F4BVjqujDzIH8TriQXOsnfDafX_pGNaa.F',
        'microsoft_teams_tenant_id' => '5455bc11-263a-427d-a7e9-b7503852ec1b',
        'microsoft_teams_fallback_email' => 'admin@scholarb.org',
        'agora_customer_id' => '85b523942ce843899d8ff5d9a6adb4b8',
        'agora_customer_secret' => '8a4f0ff244514f34b81b40cee792a995',
        'agora_cloud_rec' => 1
    ];

    $json_value = json_encode($settings);
    
    // Update the database value for settings named 'features' (ID 31)
    $stmt = $pdo->prepare("
        UPDATE setting_translations st
        JOIN settings s ON st.setting_id = s.id
        SET st.value = :value
        WHERE s.name = 'features' AND st.locale = 'en'
    ");
    $stmt->execute(['value' => $json_value]);
    
    echo "SUCCESS: Database updated with valid JSON features string!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
