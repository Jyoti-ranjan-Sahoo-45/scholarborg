<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use App\Services\ZohoCrmService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncRegistrationToZoho
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $user = $event->user;

        if (empty($user)) {
            return;
        }

        try {
            Log::info('SyncRegistrationToZoho: Capturing registration silently.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role_name
            ]);

            $zohoService = new ZohoCrmService();
            $zohoLeadId = $zohoService->createLead(
                $user->full_name,
                $user->email,
                $user->mobile,
                $user->role_name
            );

            if (!empty($zohoLeadId)) {
                $user->update([
                    'zoho_lead_id' => $zohoLeadId
                ]);

                // Sync past guest activity under the current session
                $sessionId = session()->getId();
                if (!empty($sessionId)) {
                    // Update activities to belong to the new user
                    DB::table('user_activities')
                        ->where('session_id', $sessionId)
                        ->whereNull('user_id')
                        ->update([
                            'user_id' => $user->id
                        ]);

                    // Retrieve updated activities to push to Zoho CRM
                    $unsyncedActivities = DB::table('user_activities')
                        ->where('user_id', $user->id)
                        ->where('synced_to_zoho', false)
                        ->get();

                    foreach ($unsyncedActivities as $activity) {
                        $noteContent = "Past behavior: Action '{$activity->action_type}' for '{$activity->query}' on device '{$activity->device_name}' ({$activity->device_type}) from location: {$activity->location} (IP: {$activity->ip_address}).";
                        
                        $synced = $zohoService->addLeadNote($zohoLeadId, $noteContent);
                        if ($synced) {
                            DB::table('user_activities')
                                ->where('id', $activity->id)
                                ->update(['synced_to_zoho' => true]);
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('SyncRegistrationToZoho: Error syncing user registration.', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
