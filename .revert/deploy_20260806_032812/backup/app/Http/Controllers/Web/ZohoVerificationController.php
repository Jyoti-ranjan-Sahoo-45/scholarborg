<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\ZohoCrmService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ZohoVerificationController extends Controller
{
    /**
     * Display the OTP verification page.
     */
    public function showVerifyPage(Request $request)
    {
        $email = $request->get('email');

        if (empty($email)) {
            $toastData = [
                'title' => 'Inquiry Not Found',
                'msg' => 'Please provide the email address used for submission.',
                'status' => 'error'
            ];
            return redirect('/')->with(['toast' => $toastData]);
        }

        $lead = Lead::where('email', $email)
            ->where('verification_status', 'pending')
            ->first();

        if (!$lead) {
            $toastData = [
                'title' => 'Verification Complete / Invalid',
                'msg' => 'There are no pending inquiries for this email address.',
                'status' => 'info'
            ];
            return redirect('/')->with(['toast' => $toastData]);
        }

        $data = [
            'pageTitle' => 'Verify Your Inquiry',
            'email' => $email,
            'lead' => $lead,
        ];

        return view('design_1.web.zoho.verify_lead', $data);
    }

    /**
     * Validate the submitted OTP and update status.
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = $request->input('email');
        $otp = $request->input('otp');

        $lead = Lead::where('email', $email)
            ->where('verification_status', 'pending')
            ->first();

        if (!$lead) {
            return back()->withErrors(['otp' => 'No pending inquiry found for this email.'])->withInput();
        }

        // Check OTP code and expiration
        if ($lead->otp_code !== $otp) {
            return back()->withErrors(['otp' => 'The verification code you entered is incorrect.'])->withInput();
        }

        if (time() > $lead->otp_expires_at) {
            return back()->withErrors(['otp' => 'This verification code has expired. Please request a new one.'])->withInput();
        }

        // Mark as verified locally
        $lead->verification_status = 'verified';
        $lead->otp_code = null;
        $lead->otp_expires_at = null;
        $lead->updated_at = time();
        $lead->save();

        Log::info('Lead verified locally.', ['email' => $email]);

        // Push verification update to Zoho CRM
        $zohoService = new ZohoCrmService();
        $zohoUpdated = $zohoService->updateLeadVerificationStatus($lead->email, $lead->phone);

        if ($zohoUpdated) {
            Log::info('Lead status synced to Zoho CRM.', ['email' => $email]);

            // Sync guest session activities as Zoho Notes
            try {
                $sessionId = session()->getId();
                if (!empty($sessionId)) {
                    $unsyncedActivities = \Illuminate\Support\Facades\DB::table('user_activities')
                        ->where('session_id', $sessionId)
                        ->where('synced_to_zoho', false)
                        ->get();

                    foreach ($unsyncedActivities as $activity) {
                        $noteContent = "Past behavior: Action '{$activity->action_type}' for '{$activity->query}' on device '{$activity->device_name}' ({$activity->device_type}) from location: {$activity->location} (IP: {$activity->ip_address}).";
                        
                        $synced = $zohoService->addLeadNote($zohoUpdated, $noteContent);
                        if ($synced) {
                            \Illuminate\Support\Facades\DB::table('user_activities')
                                ->where('id', $activity->id)
                                ->update(['synced_to_zoho' => true]);
                        }
                    }
                }
            } catch (\Exception $activityEx) {
                Log::error('ZohoVerificationController: Error syncing behavior notes.', ['error' => $activityEx->getMessage()]);
            }
        } else {
            Log::warning('Failed to sync lead status to Zoho CRM. Sales team will need to manually verify or check logs.', ['email' => $email]);
        }

        $toastData = [
            'title' => 'Verification Success',
            'msg' => 'Your inquiry has been successfully verified! Our sales team will connect with you shortly.',
            'status' => 'success'
        ];

        return redirect('/')->with(['toast' => $toastData]);
    }

    /**
     * Resend verification OTP code to student.
     */
    public function resendOtp(Request $request)
    {
        $email = $request->input('email');
        
        if (empty($email)) {
            return response()->json(['message' => 'Email is required.'], 400);
        }

        $lead = Lead::where('email', $email)
            ->where('verification_status', 'pending')
            ->first();

        if (!$lead) {
            return response()->json(['message' => 'No pending inquiry found.'], 404);
        }

        // Throttling: limit resending to once every 60 seconds
        if (time() < ($lead->created_at + 60)) {
            $secondsLeft = ($lead->created_at + 60) - time();
            return response()->json([
                'message' => "Please wait {$secondsLeft} seconds before requesting a new code."
            ], 429);
        }

        // Generate and save new OTP
        $otp = (string) rand(100000, 999999);
        $lead->otp_code = $otp;
        $lead->otp_expires_at = time() + 600; // 10 mins expiry
        $lead->created_at = time(); // Reset throttle timer
        $lead->save();

        try {
            $generalSettings = getGeneralSettings();
            $siteName = !empty($generalSettings['site_name']) ? $generalSettings['site_name'] : env('MAIL_FROM_NAME', 'Scholarb');
            $fromEmail = !empty($generalSettings['site_email']) ? $generalSettings['site_email'] : env('MAIL_FROM_ADDRESS');

            Mail::send('design_1.web.emails.confirmCode', [
                'confirm' => [
                    'title' => trans('auth.email_confirmation') . ' | ' . $siteName,
                    'message' => 'Here is your new verification code. Please enter it on the confirmation page.',
                    'code' => $otp
                ],
                'generalSettings' => $generalSettings
            ], function ($message) use ($email, $siteName, $fromEmail) {
                $message->to($email)
                        ->subject('New Verification Code - ' . $siteName)
                        ->from($fromEmail, $siteName);
            });

            return response()->json(['message' => 'A new code has been sent to your email.']);
        } catch (\Exception $e) {
            Log::error('Resend OTP Mail failed.', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to send email. Please try again later.'], 500);
        }
    }
}
