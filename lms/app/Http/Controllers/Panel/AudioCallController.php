<?php

namespace App\Http\Controllers\Panel;

use App\Agora\RtcTokenBuilder;
use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\ChatConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AudioCallController extends Controller
{
    private $appId;
    private $appCertificate;
    private $customerId;
    private $customerSecret;

    public function __construct()
    {
        $settings = getFeaturesSettings();
        $this->appId = !empty($settings['agora_app_id']) ? trim($settings['agora_app_id']) : '';
        $this->appCertificate = !empty($settings['agora_app_certificate']) ? trim($settings['agora_app_certificate']) : '';
        $this->customerId = !empty($settings['agora_customer_id']) ? trim($settings['agora_customer_id']) : '';
        $this->customerSecret = !empty($settings['agora_customer_secret']) ? trim($settings['agora_customer_secret']) : '';
    }

    /**
     * Helper to build basic auth header for Agora REST API
     */
    private function getAgoraAuthHeader(): string
    {
        return 'Basic ' . base64_encode("{$this->customerId}:{$this->customerSecret}");
    }

    /**
     * Initiate a VoIP Call
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
        ]);

        $caller = auth()->user();
        $callerId = $caller->id;
        $receiverId = $request->input('receiver_id');

        $receiver = DB::table('users')->where('id', $receiverId)->first();

        if ($callerId == $receiverId) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot call yourself.'
            ], 400);
        }

        // Permission check: Admin can call anyone. Others need an accepted connection.
        if (!$caller->isAdmin()) {
            $isConnected = ChatConnection::isConnected($callerId, $receiverId);
            if (!$isConnected) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'You must be connected with this user before you can call them.',
                ], 403);
            }
        }

        $activeBooking = null; // No booking required with the new connection system

        // Generate Agora Connection Information
        $channelName = 'call_' . uniqid() . '_' . $callerId . '_' . $receiverId;
        $expireTime = 3600;
        $privilegeExpiredTs = time() + $expireTime;

        // Build tokens using numerical UIDs
        $callerToken = RtcTokenBuilder::buildTokenWithUid($this->appId, $this->appCertificate, $channelName, $callerId, RtcTokenBuilder::RolePublisher, $privilegeExpiredTs);
        $receiverToken = RtcTokenBuilder::buildTokenWithUid($this->appId, $this->appCertificate, $channelName, $receiverId, RtcTokenBuilder::RolePublisher, $privilegeExpiredTs);

        // Save Call Log
        $callLog = CallLog::create([
            'caller_id' => $callerId,
            'receiver_id' => $receiverId,
            'booking_id' => null,
            'channel_name' => $channelName,
            'status' => 'initiated',
            'created_at' => time()
        ]);

        return response()->json([
            'success' => true,
            'call_id' => $callLog->id,
            'channel_name' => $channelName,
            'rtc_token' => $callerToken,
            'receiver_rtc_token' => $receiverToken, // Expose so polling endpoint can pick it up
            'receiver_id' => $receiverId,
            'receiver_name' => $receiver->full_name,
            'receiver_avatar' => $receiver->avatar ? '/' . $receiver->avatar : '/assets/default/img/avatar.png'
        ]);
    }

    /**
     * Check for Incoming Calls (Polling Endpoint)
     */
    public function checkIncoming()
    {
        $user = auth()->user();

        // Find active call where current user is recipient
        $call = CallLog::where('receiver_id', $user->id)
            ->whereIn('status', ['initiated', 'ringing'])
            ->orderBy('id', 'desc')
            ->first();

        if ($call) {
            // Update status to ringing when recipient's client first detects it
            if ($call->status === 'initiated') {
                $call->update([
                    'status' => 'ringing',
                    'updated_at' => time()
                ]);
            }

            $caller = DB::table('users')->where('id', $call->caller_id)->first();
            
            // Build temporary RTC token for recipient to join channel
            $expireTime = 3600;
            $privilegeExpiredTs = time() + $expireTime;
            $rtcToken = RtcTokenBuilder::buildTokenWithUid($this->appId, $this->appCertificate, $call->channel_name, $user->id, RtcTokenBuilder::RolePublisher, $privilegeExpiredTs);

            return response()->json([
                'active' => true,
                'call_id' => $call->id,
                'channel_name' => $call->channel_name,
                'caller_name' => $caller->full_name,
                'caller_avatar' => $caller->avatar ? '/' . $caller->avatar : '/assets/default/img/avatar.png',
                'rtc_token' => $rtcToken
            ]);
        }

        return response()->json(['active' => false]);
    }

    /**
     * Accept Call & Start Agora Cloud Recording
     */
    public function accept($id)
    {
        $call = CallLog::findOrFail($id);

        if ($call->status !== 'ringing' && $call->status !== 'initiated') {
            return response()->json([
                'success' => false,
                'message' => 'Call is not in ringing state.'
            ], 400);
        }

        // Start Agora Cloud Recording composite mode
        $this->startCloudRecording($call);

        $call->update([
            'status' => 'connected',
            'started_at' => time(),
            'updated_at' => time()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Call connected.'
        ]);
    }

    /**
     * Reject Call
     */
    public function reject($id)
    {
        $call = CallLog::findOrFail($id);
        $call->update([
            'status' => 'rejected',
            'ended_at' => time(),
            'updated_at' => time()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Call rejected.'
        ]);
    }

    /**
     * Cancel Call (Caller hung up before answer)
     */
    public function cancel($id)
    {
        $call = CallLog::findOrFail($id);
        $call->update([
            'status' => 'rejected',
            'ended_at' => time(),
            'updated_at' => time()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Call cancelled.'
        ]);
    }

    /**
     * Hangup Call & Stop Agora Cloud Recording
     */
    public function hangup($id)
    {
        $call = CallLog::findOrFail($id);

        if ($call->status === 'connected') {
            $call->update([
                'status' => 'completed',
                'ended_at' => time(),
                'updated_at' => time()
            ]);

            // Stop Agora Cloud Recording
            $this->stopCloudRecording($call);

            // Execute the background job asynchronously via Artisan command exec
            $artisan = base_path('artisan');
            exec("php {$artisan} calls:upload {$call->id} > /dev/null 2>&1 &");
        } else {
            $call->update([
                'status' => 'completed',
                'ended_at' => time(),
                'updated_at' => time()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Call hung up successfully.'
        ]);
    }

    /**
     * Get Current Call Status
     */
    public function checkStatus($id)
    {
        $call = CallLog::findOrFail($id);
        return response()->json([
            'status' => $call->status,
            'started_at' => $call->started_at,
            'ended_at' => $call->ended_at
        ]);
    }

    /**
     * Start Agora Cloud Recording (Composite Mode, Audio Only)
     */
    private function startCloudRecording(CallLog $call)
    {
        if (empty($this->customerId) || empty($this->customerSecret) || empty($this->appId)) {
            Log::error("Agora Developer Credentials are not set. Recording skipped.");
            return;
        }

        try {
            // 1. Acquire Agora resource
            $acquireUrl = "https://api.agora.io/v1/apps/{$this->appId}/cloud_recording/acquire";
            $authHeader = $this->getAgoraAuthHeader();

            $acquireResponse = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
            ])->post($acquireUrl, [
                'cname' => $call->channel_name,
                'uid' => '10000', // Recorder numeric UID
                'clientRequest' => [
                    'resourceExpiredHour' => 24,
                    'scene' => 0
                ]
            ]);

            if (!$acquireResponse->successful()) {
                throw new \Exception("Agora acquire failed: " . $acquireResponse->body());
            }

            $resourceId = $acquireResponse->json()['resourceId'];

            // 2. Build RTC token for recording agent (UID 10000)
            $expireTime = 3600;
            $privilegeExpiredTs = time() + $expireTime;
            $recordingToken = RtcTokenBuilder::buildTokenWithUid($this->appId, $this->appCertificate, $call->channel_name, 10000, RtcTokenBuilder::RoleAttendee, $privilegeExpiredTs);

            // 3. Start Recording
            $startUrl = "https://api.agora.io/v1/apps/{$this->appId}/cloud_recording/resourceids/{$resourceId}/mode/composite/start";
            
            // Fetch credentials for Cloudflare R2 from .env
            $r2Key = env('MINIO_KEY');
            $r2Secret = env('MINIO_SECRET');
            $r2Bucket = env('MINIO_BUCKET');
            $r2Endpoint = env('MINIO_ENDPOINT');

            // Format R2 endpoint host (remove scheme: https://...)
            $endpointHost = parse_url($r2Endpoint, PHP_URL_HOST);

            $startResponse = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
            ])->post($startUrl, [
                'cname' => $call->channel_name,
                'uid' => '10000',
                'clientRequest' => [
                    'token' => $recordingToken,
                    'recordingConfig' => [
                        'maxIdleTime' => 30,
                        'streamTypes' => 0, // Audio only
                        'channelType' => 0, // Communication profile
                        'subscribeAudioUids' => ['#allstream#']
                    ],
                    'storageConfig' => [
                        'vendor' => 1, // S3 Compatible
                        'region' => 0, // Custom S3 Compatible
                        'bucket' => $r2Bucket,
                        'accessKey' => $r2Key,
                        'secretKey' => $r2Secret,
                        'endpoint' => $endpointHost,
                        'fileNamePrefix' => ['recordings']
                    ]
                ]
            ]);

            if (!$startResponse->successful()) {
                throw new \Exception("Agora start recording failed: " . $startResponse->body());
            }

            $sid = $startResponse->json()['sid'];

            // Save details to call log
            $call->update([
                'agora_resource_id' => $resourceId,
                'agora_sid' => $sid
            ]);

            Log::info("Agora Cloud Recording successfully started for channel: {$call->channel_name}");

        } catch (\Exception $e) {
            Log::error("Failed to start Agora Cloud Recording: " . $e->getMessage());
        }
    }

    /**
     * Stop Agora Cloud Recording
     */
    private function stopCloudRecording(CallLog $call)
    {
        if (empty($call->agora_resource_id) || empty($call->agora_sid)) {
            return;
        }

        try {
            $stopUrl = "https://api.agora.io/v1/apps/{$this->appId}/cloud_recording/resourceids/{$call->agora_resource_id}/sid/{$call->agora_sid}/mode/composite/stop";
            $authHeader = $this->getAgoraAuthHeader();

            $stopResponse = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
            ])->post($stopUrl, [
                'cname' => $call->channel_name,
                'uid' => '10000',
                'clientRequest' => (object)[]
            ]);

            if (!$stopResponse->successful()) {
                throw new \Exception("Agora stop recording failed: " . $stopResponse->body());
            }

            Log::info("Agora Cloud Recording stopped for call log #{$call->id}");

        } catch (\Exception $e) {
            Log::error("Failed to stop Agora Cloud Recording: " . $e->getMessage());
        }
    }
}
