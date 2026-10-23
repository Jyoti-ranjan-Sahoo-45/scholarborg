<?php

namespace App\Http\Controllers\Api\Web;

use App\Http\Controllers\Api\Controller;
use App\Models\Verification;
use App\Models\Role;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OnboardingController extends Controller
{
    /**
     * Step 1-4: Book a free demo and create a pending user account.
     * POST /api/demo-booking/book
     */
    public function bookDemo(Request $request)
    {
        $data = $request->all();

        // Validate required contact details (Step 4 fields are mandatory)
        $rules = [
            'student_name'  => 'required|string|min:2|max:100',
            'email'         => 'required|email|max:255',
            'password'      => 'required|string|min:6',
        ];

        validateParam($data, $rules);

        $email       = strtolower(trim($data['email']));
        $whatsapp    = $data['whatsapp_number'] ?? null;
        $isNewUser   = false;

        // If user already exists, update password; otherwise create new
        $user = User::where('email', $email)->first();

        if ($user) {
            $user->update(['password' => Hash::make($data['password'])]);
            // Update mobile if provided and not already set
            if ($whatsapp && empty($user->mobile)) {
                $user->update(['mobile' => $whatsapp]);
            }
        } else {
            $isNewUser = true;
            $user = User::create([
                'role_name'  => Role::$user,
                'role_id'    => Role::getUserRoleId(),
                'email'      => $email,
                'full_name'  => $data['student_name'],
                'mobile'     => $whatsapp,
                'status'     => User::$pending,
                'password'   => Hash::make($data['password']),
                'created_at' => time(),
            ]);
        }

        // Store the demo booking record
        \DB::table('trial_demo_bookings')->insert([
            'user_id'            => $user->id,
            // Step 1
            'country'            => $data['country'] ?? null,
            'pin_code'           => $data['pin_code'] ?? null,
            'board_of_education' => $data['board_of_education'] ?? null,
            'grade_class'        => $data['grade_class'] ?? null,
            'subjects'           => $data['subjects'] ?? null,
            'topics'             => isset($data['topics']) ? (is_array($data['topics']) ? implode(',', $data['topics']) : $data['topics']) : null,
            // Step 2
            'class_mode'         => $data['class_mode'] ?? 'online',
            'preferred_language' => $data['preferred_language'] ?? 'English',
            'learning_goals'     => isset($data['learning_goals']) ? (is_array($data['learning_goals']) ? implode(',', $data['learning_goals']) : $data['learning_goals']) : null,
            // Step 3
            'demo_date'          => $data['demo_date'] ?? null,
            'time_range'         => $data['time_range'] ?? null,
            'days_available'     => isset($data['days_available']) ? (is_array($data['days_available']) ? implode(',', $data['days_available']) : $data['days_available']) : null,
            'start_delay'        => $data['start_delay'] ?? null,
            // Step 4
            'student_name'       => $data['student_name'],
            'parent_name'        => $data['parent_name'] ?? null,
            'email'              => $email,
            'whatsapp_number'    => $whatsapp,
            'status'             => 'pending',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $bookingId = \DB::getPdo()->lastInsertId();

        // Fire Registered event for new users → triggers SyncRegistrationToZoho listener
        // which creates a Lead in Zoho CRM with name, email, mobile, role
        if ($isNewUser) {
            event(new Registered($user));
        }

        // Generate 6-digit OTP and send to email
        $time = time();
        $code = rand(100000, 999999);

        Verification::updateOrCreate(
            ['email' => $email],
            [
                'email'       => $email,
                'code'        => $code,
                'user_id'     => $user->id,
                'created_at'  => $time,
                'expired_at'  => $time + (30 * 60), // 30 minutes
                'verified_at' => null,
            ]
        );

        // Attempt to send OTP email
        try {
            $verification = Verification::where('email', $email)->first();
            $verification->sendEmailCode();
        } catch (\Exception $e) {
            // Silently log — OTP still stored in DB for testing
        }

        return apiResponse2(1, 'booking_created', 'Demo booking created. Please verify your email.', [
            'user_id'    => $user->id,
            'booking_id' => (int) $bookingId,
            'email'      => $email,
        ]);
    }

    /**
     * Step 5: Verify OTP code sent to email.
     * POST /api/development/demo-booking/verify
     */
    public function verifyDemo(Request $request)
    {
        $data = $request->all();

        validateParam($data, [
            'user_id'    => 'required|exists:users,id',
            'booking_id' => 'required|exists:trial_demo_bookings,id',
            'code'       => 'required|numeric',
        ]);

        $user  = User::find($data['user_id']);
        $email = $user->email;
        $code  = $data['code'];
        $time  = time();

        // Find valid, unverified OTP
        $verification = Verification::where('email', $email)
            ->where('code', $code)
            ->whereNull('verified_at')
            ->where('expired_at', '>', $time)
            ->first();

        if (!$verification) {
            return apiResponse2(0, 'invalid_code', 'Invalid or expired verification code.');
        }

        // Mark OTP as verified
        $verification->update(['verified_at' => $time]);

        // Activate user
        $user->update(['status' => User::$active]);

        // Confirm the booking
        \DB::table('trial_demo_bookings')
            ->where('id', $data['booking_id'])
            ->update([
                'status'           => 'confirmed',
                'email_verified_at' => now(),
                'updated_at'       => now(),
            ]);

        // Generate JWT token
        $token = auth('api')->tokenById($user->id);

        return apiResponse2(1, 'verified', 'Email verified. Demo booking confirmed!', [
            'token'              => $token,
            'user_id'            => $user->id,
            'booking_confirmed'  => true,
            'user' => [
                'id'        => $user->id,
                'name'      => $user->full_name,
                'email'     => $user->email,
                'role_name' => $user->role_name,
                'avatar'    => $user->avatar ?? null,
            ]
        ]);
    }

    /**
     * Resend OTP to email.
     * POST /api/development/demo-booking/resend
     */
    public function resendCode(Request $request)
    {
        validateParam($request->all(), [
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);
        $email = $user->email;
        $time = time();
        $code = rand(100000, 999999);

        Verification::updateOrCreate(
            ['email' => $email],
            [
                'code'       => $code,
                'user_id'    => $user->id,
                'created_at' => $time,
                'expired_at' => $time + (30 * 60),
                'verified_at' => null,
            ]
        );

        try {
            $verification = Verification::where('email', $email)->first();
            $verification->sendEmailCode();
        } catch (\Exception $e) {}

        return apiResponse2(1, 'code_sent', 'A new verification code has been sent to your email.');
    }
}
