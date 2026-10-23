<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZohoCrmService
{
    protected $clientId;
    protected $clientSecret;
    protected $refreshToken;
    protected $region; // e.g. 'in', 'com', 'eu'
    
    protected $accountsUrl;
    protected $apiUrl;

    public function __construct()
    {
        $settingsModel = Setting::where('name', 'zoho_crm')->first();
        $settings = [];
        
        if (!empty($settingsModel)) {
            $value = $settingsModel->value;
            if (is_string($value)) {
                $settings = json_decode($value, true) ?: [];
            } else {
                $settings = $value ?: [];
            }
        }

        $this->clientId = $settings['client_id'] ?? '1000.0C9KIMBVDS7NPI94RLCTPF6KXJ9GTC';
        $this->clientSecret = $settings['client_secret'] ?? '64ca8f2729de498ad9b846c32443fc3cf4e3a3a748';
        $this->refreshToken = $settings['refresh_token'] ?? null;
        $this->region = $settings['region'] ?? 'in'; // Default to India 'in'

        $this->accountsUrl = "https://accounts.zoho." . $this->region;
        $this->apiUrl = "https://www.zohoapis." . $this->region;
    }

    /**
     * Get the active access token, refreshing it if expired.
     */
    public function getAccessToken()
    {
        $cacheKey = 'zoho_access_token';
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        if (empty($this->refreshToken)) {
            Log::error('Zoho CRM Service: Refresh token is missing.');
            return null;
        }

        try {
            $response = Http::asForm()->post($this->accountsUrl . "/oauth/v2/token", [
                'refresh_token' => $this->refreshToken,
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'refresh_token',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['access_token'])) {
                    $accessToken = $data['access_token'];
                    $expiresIn = $data['expires_in'] ?? 3600;
                    
                    // Cache it, expiring 60 seconds early to avoid race conditions
                    Cache::put($cacheKey, $accessToken, $expiresIn - 60);
                    return $accessToken;
                }
            }
            
            Log::error('Zoho CRM Service: Token refresh failed.', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Zoho CRM Service: Exception refreshing token.', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Update the WhatsApp/Email verification status of a lead in Zoho CRM.
     */
    public function updateLeadVerificationStatus($email, $phone = null)
    {
        $accessToken = $this->getAccessToken();
        if (empty($accessToken)) {
            return false;
        }

        // 1. Search for the lead in Zoho CRM
        $leadId = null;
        try {
            $searchQuery = "";
            if (!empty($email)) {
                $searchQuery = "email=" . urlencode($email);
            } elseif (!empty($phone)) {
                $searchQuery = "phone=" . urlencode($phone);
            }

            if (empty($searchQuery)) {
                return false;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
            ])->get($this->apiUrl . "/crm/v3/Leads/search?" . $searchQuery);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['data']) && isset($data['data'][0]['id'])) {
                    $leadId = $data['data'][0]['id'];
                }
            } else {
                Log::warning('Zoho CRM Service: Lead search returned empty/error.', ['response' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Zoho CRM Service: Exception searching lead.', ['error' => $e->getMessage()]);
            return false;
        }

        if (empty($leadId)) {
            Log::warning('Zoho CRM Service: No matching lead found in Zoho CRM to update.', ['email' => $email, 'phone' => $phone]);
            return false;
        }

        // 2. Update the Lead verification status fields
        try {
            // We update standard Lead_Status and custom verification status fields if they exist
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->put($this->apiUrl . "/crm/v3/Leads/" . $leadId, [
                'data' => [
                    [
                        'id' => $leadId,
                        'Lead_Status' => 'Verified',
                        'Email_Verification_Status' => 'Verified',
                        'WhatsApp_Verification_Status' => 'Verified'
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info('Zoho CRM Service: Lead verification status updated successfully in Zoho.', ['lead_id' => $leadId]);
                return $leadId;
            }

            Log::error('Zoho CRM Service: Lead update request failed.', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Zoho CRM Service: Exception updating lead.', ['error' => $e->getMessage()]);
        }

        return false;
    }

    /**
     * Create a new Lead silently in Zoho CRM upon registration.
     */
    public function createLead($name, $email, $phone = null, $role = 'student')
    {
        $accessToken = $this->getAccessToken();
        if (empty($accessToken)) {
            return null;
        }

        // Parse Name into First Name & Last Name (Zoho requires Last_Name)
        $parts = explode(' ', trim($name));
        $lastName = array_pop($parts);
        $firstName = implode(' ', $parts);

        if (empty($lastName)) {
            $lastName = $name;
            $firstName = '';
        }

        try {
            $payload = [
                'data' => [
                    [
                        'Last_Name' => $lastName,
                        'First_Name' => $firstName,
                        'Email' => $email,
                        'Lead_Source' => 'LMS Registration',
                        'Designation' => ucfirst($role), // e.g. "Student" or "Teacher"
                        'Description' => 'Silently registered ' . $role . ' lead from LMS.'
                    ]
                ]
            ];

            if (!empty($phone)) {
                $payload['data'][0]['Phone'] = $phone;
                $payload['data'][0]['Mobile'] = $phone;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . "/crm/v3/Leads", $payload);

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['data']) && isset($data['data'][0]['details']['id'])) {
                    $zohoLeadId = $data['data'][0]['details']['id'];
                    Log::info('Zoho CRM Service: Lead created successfully on registration.', ['lead_id' => $zohoLeadId]);
                    return $zohoLeadId;
                }
            }
            
            Log::error('Zoho CRM Service: Lead creation failed.', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Zoho CRM Service: Exception creating lead.', ['error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Add an activity Note to the Lead in Zoho CRM.
     */
    public function addLeadNote($leadId, $content)
    {
        $accessToken = $this->getAccessToken();
        if (empty($accessToken) || empty($leadId)) {
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Zoho-oauthtoken ' . $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . "/crm/v3/Notes", [
                'data' => [
                    [
                        'Note_Title' => 'LMS Activity Log',
                        'Note_Content' => $content,
                        'Parent_Id' => [
                            'id' => $leadId
                        ],
                        '$se_module' => 'Leads'
                    ]
                ]
            ]);

            if ($response->successful()) {
                Log::info('Zoho CRM Service: Activity note added to lead.', ['lead_id' => $leadId]);
                return true;
            }
            Log::error('Zoho CRM Service: Adding note failed.', ['response' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Zoho CRM Service: Exception adding note.', ['error' => $e->getMessage()]);
        }

        return false;
    }
}
