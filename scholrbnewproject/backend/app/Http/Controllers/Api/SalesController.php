<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

// Define temporary models locally inside the file or reference generic DB queries
// Since we want to query ERPGo crm tables: 'leads' and 'lead_activity_logs'
class SalesController extends Controller
{
    /**
     * List all leads with calculated lead scores.
     */
    public function leads(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'sales_executive' && $user->type !== 'sales_manager' && $user->type !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Query the CRM leads table
        $leads = DB::table('leads')
            ->select('id', 'name', 'email', 'phone', 'subject', 'notes', 'is_converted')
            ->orderBy('id', 'desc')
            ->paginate(10);

        // Map and append dynamic lead score
        $leads->getCollection()->transform(function ($lead) {
            // Find registered user by email
            $registeredUser = DB::table('users')->where('email', $lead->email)->first();
            $score = 5; // Base score

            if ($registeredUser) {
                // Check bookings count
                $bookingsCount = DB::table('tmp_bookings')->where('student_id', $registeredUser->id)->count();
                $score += ($bookingsCount * 20);

                // Check payments count
                $paymentsCount = DB::table('tmp_payments')->where('user_id', $registeredUser->id)->where('status', 'paid')->count();
                $score += ($paymentsCount * 50);

                // Check profiles onboarding
                $profileExists = DB::table('tmp_student_profiles')->where('user_id', $registeredUser->id)->exists();
                if ($profileExists) {
                    $score += 15;
                }
            }

            $lead->lead_score = $score;
            
            // Set category based on score
            if ($score >= 70) {
                $lead->category = 'Hot';
            } elseif ($score >= 20) {
                $lead->category = 'Warm';
            } else {
                $lead->category = 'Cold';
            }

            return $lead;
        });

        return response()->json($leads);
    }

    /**
     * Add a follow-up log to a CRM lead.
     */
    public function addFollowup(Request $request, $id)
    {
        $user = $request->user();
        if ($user->type !== 'sales_executive' && $user->type !== 'sales_manager' && $user->type !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'remark' => 'required|string',
            'log_type' => 'required|string|in:Call,Email,WhatsApp,Meeting'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $lead = DB::table('leads')->where('id', $id)->first();
        if (!$lead) {
            return response()->json(['message' => 'Lead not found.'], 404);
        }

        // Insert into lead_activity_logs table
        $logId = DB::table('lead_activity_logs')->insertGetId([
            'user_id' => $user->id,
            'lead_id' => $id,
            'log_type' => $request->log_type,
            'remark' => $request->remark,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'message' => 'CRM follow-up activity logged successfully',
            'log_id' => $logId
        ], 201);
    }

    /**
     * Get details of a lead and its dynamic scoring timeline.
     */
    public function getLeadDetails(Request $request, $id)
    {
        $user = $request->user();
        if ($user->type !== 'sales_executive' && $user->type !== 'sales_manager' && $user->type !== 'admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $lead = DB::table('leads')->where('id', $id)->first();
        if (!$lead) {
            return response()->json(['message' => 'Lead not found.'], 404);
        }

        // Fetch logs
        $logs = DB::table('lead_activity_logs')
            ->where('lead_id', $id)
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'lead' => $lead,
            'activity_logs' => $logs
        ]);
    }
}
