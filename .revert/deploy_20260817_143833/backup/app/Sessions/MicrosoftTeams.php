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
            throw new \Exception('Microsoft Teams API Credentials (Client ID, Client Secret, Tenant ID) are not configured in General Settings.');
        }

        if (empty($fallbackEmail)) {
            throw new \Exception('No fallback organizer email (e.g. admin@scholarb.org) configured for Microsoft Teams in General Settings.');
        }

        // 1. Get Access Token via Client Credentials flow
        $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";
        
        try {
            $tokenResponse = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'https://graph.microsoft.com/.default',
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Failed to connect to Microsoft OAuth token endpoint: ' . $e->getMessage());
        }

        if (!$tokenResponse->successful()) {
            $errorData = $tokenResponse->json();
            $errDescription = $errorData['error_description'] ?? $tokenResponse->body();
            throw new \Exception('Microsoft Teams OAuth Token Retrieval Failed: ' . $errDescription);
        }

        $accessToken = $tokenResponse->json()['access_token'] ?? null;
        if (empty($accessToken)) {
            throw new \Exception('Microsoft Teams Access Token not found in OAuth response.');
        }

        // 2. Determine Organizer Email
        // Always use the fallback email (admin@scholarb.org) for meetings,
        // since instructors are platform-only users and do not exist in Azure AD.
        $organizerEmail = $fallbackEmail;

        // Microsoft Graph Application-only calls require the user's Object ID (GUID)
        // rather than their UPN email address. We resolve the email to the GUID first.
        try {
            $userLookupResponse = Http::withToken($accessToken)
                ->get("https://graph.microsoft.com/v1.0/users/{$organizerEmail}");
        } catch (\Exception $e) {
            throw new \Exception('Failed to connect to Microsoft Graph API for user lookup: ' . $e->getMessage());
        }

        if (!$userLookupResponse->successful()) {
            $errorData = $userLookupResponse->json();
            $errorMessage = $errorData['error']['message'] ?? $userLookupResponse->body();
            throw new \Exception("Failed to locate user '{$organizerEmail}' in Azure AD: " . $errorMessage);
        }

        $userGuid = $userLookupResponse->json()['id'] ?? null;
        if (empty($userGuid)) {
            throw new \Exception("Could not retrieve Azure GUID for user '{$organizerEmail}'.");
        }

        // 3. Create the meeting via Microsoft Graph API using the GUID
        $meetingUrl = "https://graph.microsoft.com/v1.0/users/{$userGuid}/onlineMeetings";
        
        // Format dates as UTC Zulu time (ISO 8601)
        $startDateTime = Carbon::createFromTimestamp($session->date)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
        $endDateTime = Carbon::createFromTimestamp($session->date)->addMinutes($session->duration)->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');

        $meetingBody = [
            'startDateTime' => $startDateTime,
            'endDateTime' => $endDateTime,
            'subject' => $session->title,
            'recordAutomatically' => true,
            'allowedPresenters' => 'everyone',
            'lobbyBypassSettings' => [
                'scope' => 'everyone',
                'isPlayExitNotification' => false
            ]
        ];

        try {
            $response = Http::withToken($accessToken)->post($meetingUrl, $meetingBody);
        } catch (\Exception $e) {
            throw new \Exception('Failed to connect to Microsoft Graph API: ' . $e->getMessage());
        }

        if (!$response->successful()) {
            $errorData = $response->json();
            $errorMessage = $errorData['error']['message'] ?? $response->body();
            throw new \Exception('Microsoft Teams Meeting Creation Failed for ' . $organizerEmail . ': ' . $errorMessage);
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

        throw new \Exception('Microsoft Teams Meeting Join URL not found in API response.');
    }
}
