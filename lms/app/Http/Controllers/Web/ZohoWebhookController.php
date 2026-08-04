<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ZohoWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        Log::info('Zoho Forms Webhook received.', $request->all());

        // Validate the request payload
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'subject' => 'nullable|string',
            'message' => 'nullable|string',
            'zoho_lead_id' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            Log::warning('Zoho Forms Webhook validation failed.', ['errors' => $validator->errors()]);
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 400);
        }

        $email = $request->input('email');
        $name = $request->input('name');
        $phone = $request->input('phone');
        $subject = $request->input('subject');
        $messageText = $request->input('message');
        $zohoLeadId = $request->input('zoho_lead_id');

        // Check if there is an existing pending lead for this email to update, or create a new one
        $lead = Lead::where('email', $email)
            ->where('verification_status', 'pending')
            ->first();

        if (!$lead) {
            $lead = new Lead();
        }

        $lead->name = $name;
        $lead->email = $email;
        $lead->phone = $phone;
        $lead->subject = $subject;
        $lead->message = $messageText;
        $lead->zoho_lead_id = $zohoLeadId;
        $lead->verification_status = 'pending';
        
        // Generate a 6-digit OTP
        $otp = (string) rand(100000, 999999);
        $lead->otp_code = $otp;
        $lead->otp_expires_at = time() + 600; // 10 minutes expiry
        $lead->created_at = time();
        $lead->save();

        // Send OTP via Email using the site's default layout and translatable styling
        try {
            $generalSettings = getGeneralSettings();
            $siteName = !empty($generalSettings['site_name']) ? $generalSettings['site_name'] : env('MAIL_FROM_NAME', 'Scholarb');
            $fromEmail = !empty($generalSettings['site_email']) ? $generalSettings['site_email'] : env('MAIL_FROM_ADDRESS');

            Mail::send('design_1.web.emails.confirmCode', [
                'confirm' => [
                    'title' => trans('auth.email_confirmation') . ' | ' . $siteName,
                    'message' => 'Thank you for submitting your inquiry. Please enter the verification code below on the confirmation page to verify your request.',
                    'code' => $otp
                ],
                'generalSettings' => $generalSettings
            ], function ($message) use ($email, $siteName, $fromEmail) {
                $message->to($email)
                        ->subject('Verify Your Inquiry - ' . $siteName)
                        ->from($fromEmail, $siteName);
            });

            Log::info('Lead verification OTP sent successfully.', ['email' => $email]);
        } catch (\Exception $e) {
            Log::error('Failed to send lead verification OTP email.', ['error' => $e->getMessage()]);
            // Still return 200/success to the webhook, since the lead was saved locally
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Lead registered and OTP sent successfully.'
        ]);
    }
}
