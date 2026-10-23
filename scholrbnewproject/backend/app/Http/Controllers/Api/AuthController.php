<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\ZohoCRMService;
class AuthController extends Controller
{
    /**
     * Register a new user (Student or Parent).
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'type' => 'required|string|in:student,parent,teacher,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'type' => $request->type,
            'lang' => 'en',
            'active_plan' => 0,
        ]);

        // Create specific profiles if necessary
        if ($request->type === 'student') {
            $user->studentProfile()->create([
                'timezone' => 'UTC'
            ]);
            $user->load('studentProfile');
        } elseif ($request->type === 'teacher') {
            $user->teacherProfile()->create([
                'qualification' => 'Pending Onboarding',
                'experience_years' => 0,
                'bio' => 'Draft profile',
                'teaching_methodology' => 'Draft',
                'hourly_rate' => 0.00,
                'languages_spoken' => [],
                'teaching_modes' => ['one-to-one'],
                'verification_status' => 'pending'
            ]);
            $user->load('teacherProfile');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        // Sync contact to Zoho CRM (fails gracefully if credentials or request fails)
        $zohoSyncResult = ZohoCRMService::syncContact($user);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'zoho_sync' => $zohoSyncResult
        ], 201);
    }

    /**
     * Authenticate a user and return access token.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid login credentials'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer'
        ]);
    }

    /**
     * Get the authenticated user details.
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
            'student_profile' => $request->user()->studentProfile,
            'teacher_profile' => $request->user()->teacherProfile
        ]);
    }

    /**
     * Revoke access token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Token revoked successfully'
        ]);
    }
}
