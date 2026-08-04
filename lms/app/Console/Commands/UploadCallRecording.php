<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CallLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class UploadCallRecording extends Command
{
    protected $signature = 'calls:upload {id}';
    protected $description = 'Syncs Agora Cloud Recording from Cloudflare R2 to OneDrive';

    public function handle()
    {
        $logId = $this->argument('id');
        $log = CallLog::find($logId);

        if (!$log) {
            $this->error("Call log #{$logId} not found.");
            return 1;
        }

        if (empty($log->agora_resource_id) || empty($log->agora_sid)) {
            $this->error("Call log #{$logId} does not have Agora resource ID or SID.");
            return 1;
        }

        $this->info("Processing recording upload for call log #{$logId}...");

        try {
            // 1. Get Azure AD & Teams Settings
            $settings = getFeaturesSettings();
            $clientId = !empty($settings['microsoft_teams_client_id']) ? trim($settings['microsoft_teams_client_id']) : '';
            $clientSecret = !empty($settings['microsoft_teams_client_secret']) ? trim($settings['microsoft_teams_client_secret']) : '';
            $tenantId = !empty($settings['microsoft_teams_tenant_id']) ? trim($settings['microsoft_teams_tenant_id']) : '';
            $fallbackEmail = !empty($settings['microsoft_teams_fallback_email']) ? trim($settings['microsoft_teams_fallback_email']) : '';

            if (empty($clientId) || empty($clientSecret) || empty($tenantId) || empty($fallbackEmail)) {
                throw new \Exception('Microsoft Graph API / Teams credentials are not configured in settings.');
            }

            // 2. Fetch Agora REST Credentials from settings
            $agoraAppId = !empty($settings['agora_app_id']) ? trim($settings['agora_app_id']) : '';
            $customerId = !empty($settings['agora_customer_id']) ? trim($settings['agora_customer_id']) : '';
            $customerSecret = !empty($settings['agora_customer_secret']) ? trim($settings['agora_customer_secret']) : '';

            if (empty($agoraAppId) || empty($customerId) || empty($customerSecret)) {
                throw new \Exception('Agora Developer credentials (Customer ID & Secret) are not configured.');
            }

            // 3. Query Agora REST API for file list
            $this->info("Querying Agora recording status...");
            $authHeader = 'Basic ' . base64_encode("{$customerId}:{$customerSecret}");
            $queryUrl = "https://api.agora.io/v1/apps/{$agoraAppId}/cloud_recording/resourceids/{$log->agora_resource_id}/sid/{$log->agora_sid}/mode/composite/query";

            $queryResponse = Http::withHeaders([
                'Authorization' => $authHeader,
                'Content-Type' => 'application/json',
            ])->get($queryUrl);

            if (!$queryResponse->successful()) {
                throw new \Exception("Agora query failed: " . $queryResponse->body());
            }

            $queryData = $queryResponse->json();
            $serverResponse = $queryData['serverResponse'] ?? [];
            $fileList = $serverResponse['fileList'] ?? [];

            if (empty($fileList) || !is_array($fileList)) {
                $this->warn("No recording files found for call log #{$logId}. Rechecking status...");
                // If status is 4 or 5, recording finished but maybe no files generated (no audio stream)
                $log->update([
                    'status' => 'completed',
                    'updated_at' => time()
                ]);
                return 0;
            }

            // Sort files to concatenate in proper sequence
            usort($fileList, function ($a, $b) {
                return strcmp($a['fileName'], $b['fileName']);
            });

            // 4. Download files from Cloudflare R2 and merge them
            $this->info("Downloading and merging TS recording slices from Cloudflare R2...");
            $tempDir = storage_path('app/temp_calls');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $localMergedPath = $tempDir . "/call_{$logId}.ts";
            $localFileHandle = fopen($localMergedPath, 'wb');

            if (!$localFileHandle) {
                throw new \Exception("Failed to open local temporary file: {$localMergedPath}");
            }

            $downloadedFiles = [];
            foreach ($fileList as $fileInfo) {
                $fileName = $fileInfo['fileName'];
                $this->info("Reading slice from R2: {$fileName}");

                if (Storage::disk('minio')->exists($fileName)) {
                    $sliceContent = Storage::disk('minio')->get($fileName);
                    fwrite($localFileHandle, $sliceContent);
                    $downloadedFiles[] = $fileName;
                } else {
                    $this->warn("Slice file {$fileName} not found in R2 bucket.");
                }
            }

            fclose($localFileHandle);

            if (empty($downloadedFiles)) {
                throw new \Exception("No recording slices were successfully downloaded from Cloudflare R2.");
            }

            // 5. Authenticate with Microsoft Graph
            $this->info("Authenticating with Microsoft Graph API...");
            $tokenUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token";
            $tokenResponse = Http::asForm()->post($tokenUrl, [
                'grant_type' => 'client_credentials',
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'scope' => 'https://graph.microsoft.com/.default',
            ]);

            if (!$tokenResponse->successful()) {
                throw new \Exception("Microsoft OAuth failed: " . $tokenResponse->body());
            }

            $accessToken = $tokenResponse->json()['access_token'];

            // Resolve admin user GUID in Azure AD
            $userGuidResponse = Http::withToken($accessToken)
                ->get("https://graph.microsoft.com/v1.0/users/{$fallbackEmail}");

            if (!$userGuidResponse->successful()) {
                throw new \Exception("Failed to resolve user GUID for {$fallbackEmail}: " . $userGuidResponse->body());
            }

            $userGuid = $userGuidResponse->json()['id'];
            $fileSize = filesize($localMergedPath);
            $fileNameOneDrive = "call_{$logId}_" . time() . ".ts";

            // 6. Upload file to OneDrive via Upload Session (supports any file size chunked)
            $this->info("Creating OneDrive upload session...");
            $uploadSessionUrl = "https://graph.microsoft.com/v1.0/users/{$userGuid}/drive/root:/scholarb_recordings/{$fileNameOneDrive}:/createUploadSession";
            
            $sessionResponse = Http::withToken($accessToken)->post($uploadSessionUrl, [
                'item' => [
                    '@microsoft.graph.conflictBehavior' => 'rename',
                    'name' => $fileNameOneDrive
                ]
            ]);

            if (!$sessionResponse->successful()) {
                throw new \Exception("Failed to create upload session: " . $sessionResponse->body());
            }

            $uploadUrl = $sessionResponse->json()['uploadUrl'];

            // Stream file in chunks of ~3.125 MB (must be multiple of 327,680 bytes)
            $this->info("Uploading call recording to OneDrive in chunks...");
            $chunkSize = 3276800; // 327680 * 10
            $fileHandle = fopen($localMergedPath, 'rb');
            
            $startBytes = 0;
            while (!feof($fileHandle)) {
                $chunkData = fread($fileHandle, $chunkSize);
                if (empty($chunkData)) {
                    break;
                }
                $chunkLength = strlen($chunkData);
                $endBytes = $startBytes + $chunkLength - 1;

                $chunkResponse = Http::withHeaders([
                    'Content-Length' => $chunkLength,
                    'Content-Range' => "bytes {$startBytes}-{$endBytes}/{$fileSize}",
                ])->put($uploadUrl, $chunkData);

                if (!$chunkResponse->successful() && $chunkResponse->status() !== 201) {
                    throw new \Exception("Chunk upload failed at bytes {$startBytes}-{$endBytes}: " . $chunkResponse->body());
                }

                $startBytes = $endBytes + 1;
                
                // If final chunk upload is completed, save metadata
                if ($chunkResponse->status() === 201 || $chunkResponse->status() === 200) {
                    $driveItem = $chunkResponse->json();
                    $driveItemId = $driveItem['id'];
                }
            }
            fclose($fileHandle);

            if (empty($driveItemId)) {
                throw new \Exception("File upload did not return a valid drive item ID.");
            }

            // 7. Create Anonymous Sharing Link
            $this->info("Generating public sharing link...");
            $linkUrl = "https://graph.microsoft.com/v1.0/users/{$userGuid}/drive/items/{$driveItemId}/createLink";
            $linkResponse = Http::withToken($accessToken)->post($linkUrl, [
                'type' => 'view',
                'scope' => 'anonymous'
            ]);

            if (!$linkResponse->successful()) {
                throw new \Exception("Failed to generate sharing link: " . $linkResponse->body());
            }

            $sharingLink = $linkResponse->json()['link']['webUrl'];

            // 8. Update Call Log record
            $log->update([
                'status' => 'completed',
                'recording_file' => $sharingLink,
                'duration' => $log->ended_at && $log->started_at ? ($log->ended_at - $log->started_at) : null,
                'updated_at' => time()
            ]);

            $this->info("Sync completed! OneDrive Link: {$sharingLink}");

            // 9. Clean up remote Cloudflare R2 files
            $this->info("Cleaning up temporary files from Cloudflare R2...");
            foreach ($downloadedFiles as $fileName) {
                Storage::disk('minio')->delete($fileName);
            }

            // Clean up local temp file
            if (file_exists($localMergedPath)) {
                unlink($localMergedPath);
            }

            $this->info("Call log #{$logId} processing completed successfully!");
            return 0;

        } catch (\Exception $e) {
            Log::error("Call upload command failed for log #{$logId}: " . $e->getMessage());
            $this->error("Error: " . $e->getMessage());
            return 1;
        }
    }
}
