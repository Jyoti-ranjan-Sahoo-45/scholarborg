<?php

namespace App\Sessions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MicrosoftTeams
{
    public function makeMeeting($session): bool
    {
        $settings = getFeaturesSettings();

        $clientId = !empty($settings['microsoft_teams_client_id']) ? trim($settings['microsoft_teams_client_id']) : '';
        $clientSecret = !empty($settings['microsoft_teams_client_secret']) ? trim($settings['microsoft_teams_client_secret']) : '';
        $tenantId = !empty($settings['microsoft_teams_tenant_id']) ? trim($settings['microsoft_teams_tenant_id']) : '';
        $fallbackEmail = !empty($settings['microsoft_teams_fallback_email']) ? trim($settings['microsoft_teams_fallback_email']) : '';

        if (empty($clientId) || empty($clientSecret) || empty($tenantId)) {
            Log::error('Microsoft Teams API Credentials are not configured.');
            return false;
        }

        // 1. Get Access Token via Client Credentials flow
        $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";
        $tokenResponse = Http::asForm()->post($tokenUrl, [
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'scope' => 'https://graph.microsoft.com/.default',
        ]);

        if (!$tokenResponse->successful()) {
            Log::error('Microsoft Teams OAuth Token Retrieval Failed: ' . $tokenResponse->body());
            return false;
        }

        $accessToken = $tokenResponse->json()['access_token'] ?? null;
        if (empty($accessToken)) {
            Log::error('Microsoft Teams Access Token not found in response.');
            return false;
        }

        // 2. Determine Organizer Email
        // Try the instructor's email first, fall back to configured fallback email if empty
        $organizerEmail = '';
        if ($session->creator && !empty($session->creator->email)) {
            $organizerEmail = trim($session->creator->email);
        } else {
            $organizerEmail = $fallbackEmail;
        }

        if (empty($organizerEmail)) {
            Log::error('No organizer email found for Microsoft Teams meeting creation.');
            return false;
        }

        // 3. Create the meeting via Microsoft Graph API
        $meetingUrl = "https://graph.microsoft.com/v1.0/users/{$organizerEmail}/onlineMeetings";
        
        // Format dates as UTC Zulu time (ISO 8601)
        $startDateTime = Carbon::createFromTimestamp($session->date)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
        $endDateTime = Carbon::createFromTimestamp($session->date)->addMinutes($session->duration)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

        $meetingBody = [
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'subject' => $session->title,
            'recordAutomatically' => true,
        ];

        $response = Http::withToken($accessToken)
            ->post($meetingUrl, $meetingBody);

        // If the instructor email is not in the AD Tenant, it returns 404 (Resource not found).
        // Try using the fallback email instead if they are different.
        if ($response->status() == 404 && !empty($fallbackEmail) && strtolower($organizerEmail) !== strtolower($fallbackEmail)) {
            Log::warning("Instructor email '{$organizerEmail}' not found in AD. Retrying with fallback email '{$fallbackEmail}'");
            $meetingUrl = "https://graph.microsoft.com/v1.0/users/{$fallbackEmail}/onlineMeetings";
            $response = Http::withToken($accessToken)
                ->post($meetingUrl, $meetingBody);
        }

        if (!$response->successful()) {
            Log::error('Microsoft Teams Meeting Creation Failed: ' . $response->body());
            return false;
        }

        $meetingData = $response->json();
        $joinUrl = $meetingData['joinWebUrl'] ?? null;
        $meetingId = $meetingData['id'] ?? null;

        if (!empty($joinUrl)) {
            unset($session->title, $session->locale);

            $session->update([
                'link' => $joinUrl,
                'zoom_id' => $meetingId,
            ]);

            return true;
        }

        return false;
    }
}
