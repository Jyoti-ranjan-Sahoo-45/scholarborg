<?php

namespace App\Http\Middleware;

use Closure;
use Jenssegers\Agent\Agent;
use App\Services\ZohoCrmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Process logging after response has been sent (silently, try-catch all)
        try {
            $this->track($request);
        } catch (\Exception $e) {
            Log::error('TrackUserActivity: Error tracking user activity.', [
                'error' => $e->getMessage()
            ]);
        }

        return $response;
    }

    /**
     * Track and save user search or page view activity.
     */
    protected function track(Request $request)
    {
        $path = $request->path();
        $actionType = null;
        $queryValue = null;

        // 1. Detect Action: Search
        if ($request->is('search') && $request->has('search')) {
            $actionType = 'search';
            $queryValue = $request->get('search');
        }
        // 2. Detect Action: Page View on specific resources
        elseif ($request->is('course/*') || $request->is('bundles/*') || $request->is('products/*') || $request->is('blog/*')) {
            $actionType = 'page_view';
            $queryValue = $request->getRequestUri();
        }

        if (empty($actionType) || empty($queryValue)) {
            return;
        }

        // Parse Device using Jenssegers Agent
        $agent = new Agent();
        $deviceType = 'Desktop';
        if ($agent->isTablet()) {
            $deviceType = 'Tablet';
        } elseif ($agent->isMobile()) {
            $deviceType = 'Mobile';
        }

        $platform = $agent->platform() ?: 'Unknown OS';
        $deviceBrand = $agent->device() ?: 'Generic Device';
        $browser = $agent->browser() ?: 'Unknown Browser';

        $deviceName = "{$platform} (Device: {$deviceBrand}) / {$browser}";

        // Resolve Location based on IP (Cached)
        $ip = $request->ip();
        $location = Cache::remember('ip_loc_' . $ip, 86400, function () use ($ip) {
            if ($ip === '127.0.0.1' || $ip === '::1') {
                return 'Localhost';
            }
            try {
                $res = Http::timeout(3)->get("http://ip-api.com/json/{$ip}");
                if ($res->successful()) {
                    $data = $res->json();
                    if (isset($data['status']) && $data['status'] === 'success') {
                        return "{$data['city']}, {$data['regionName']}, {$data['country']}";
                    }
                }
            } catch (\Exception $e) {
                // Silently ignore geolocation failure
            }
            return 'Unknown Location';
        });

        $user = auth()->user();
        $sessionId = session()->getId();

        // Save activity locally
        $activityId = DB::table('user_activities')->insertGetId([
            'user_id' => $user ? $user->id : null,
            'session_id' => $sessionId ?: '',
            'ip_address' => $ip,
            'location' => $location,
            'user_agent' => $request->userAgent(),
            'device_type' => $deviceType,
            'device_name' => $deviceName,
            'action_type' => $actionType,
            'query' => $queryValue,
            'synced_to_zoho' => false,
            'created_at' => time()
        ]);

        // If user is logged in and has Zoho Lead ID, sync in real-time
        if ($user && !empty($user->zoho_lead_id)) {
            $zohoService = new ZohoCrmService();
            $noteContent = "Action '{$actionType}' for '{$queryValue}' on device '{$deviceName}' ({$deviceType}) from location: {$location} (IP: {$ip}).";
            
            $synced = $zohoService->addLeadNote($user->zoho_lead_id, $noteContent);
            if ($synced) {
                DB::table('user_activities')
                    ->where('id', $activityId)
                    ->update(['synced_to_zoho' => true]);
            }
        }
    }
}
