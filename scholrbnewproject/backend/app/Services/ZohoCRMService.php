<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZohoCRMService
{
    /**
     * Get OAuth Access Token from Zoho CRM using Refresh Token.
     */
    public static function getAccessToken()
    {
        $clientId = config('services.zoho.client_id');
        $clientSecret = config('services.zoho.client_secret');
        $refreshToken = config('services.zoho.refresh_token');
        $region = config('services.zoho.region', 'in');

        if (!$clientId || !$clientSecret || !$refreshToken) {
            Log::warning("Zoho CRM credentials missing from configuration.");
            return null;
        }

        $accountsUrl = ($region === 'in') ? 'https://accounts.zoho.in' : 'https://accounts.zoho.com';

        try {
            $response = Http::asForm()->post("{$accountsUrl}/oauth/v2/token", [
                'grant_type' => 'refresh_token',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'] ?? null;
            }

            Log::error("Zoho CRM Access Token request failed: " . $response->body());
            return null;
        } catch (\Exception $e) {
            Log::error("Exception requesting Zoho CRM Access Token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Sync registered user contact details to Zoho CRM Contacts module.
     */
    public static function syncContact($user)
    {
        $accessToken = self::getAccessToken();
        if (!$accessToken) {
            return [
                'success' => false,
                'message' => 'Failed to obtain Zoho access token.'
            ];
        }

        $region = config('services.zoho.region', 'in');
        $apiDomain = ($region === 'in') ? 'https://www.zohoapis.in' : 'https://www.zohoapis.com';

        try {
            $response = Http::withHeaders([
                'Authorization' => "Zoho-oauthtoken {$accessToken}",
                'Content-Type' => 'application/json'
            ])->post("{$apiDomain}/crm/v3/Contacts", [
                'data' => [
                    [
                        'Last_Name' => $user->name,
                        'Email' => $user->email,
                        'Lead_Source' => 'Teacher Marketplace Platform',
                        'Description' => "User registered as type: {$user->type}"
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info("User {$user->email} synced successfully to Zoho CRM.");
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            }

            Log::warning("Zoho CRM Contact sync failed: " . $response->body());
            return [
                'success' => false,
                'message' => 'Zoho CRM API rejected request.',
                'error' => $response->json()
            ];
        } catch (\Exception $e) {
            Log::error("Exception syncing contact to Zoho CRM: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Exception occurred during sync: ' . $e->getMessage()
            ];
        }
    }
}
