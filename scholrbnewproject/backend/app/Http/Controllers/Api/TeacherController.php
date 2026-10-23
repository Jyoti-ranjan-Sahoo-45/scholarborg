<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TeacherController extends Controller
{
    /**
     * Complete teacher onboarding.
     */
    public function onboard(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'teacher') {
            return response()->json(['message' => 'Unauthorized. Must be a teacher.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'qualification' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'bio' => 'required|string',
            'teaching_methodology' => 'required|string',
            'hourly_rate' => 'required|numeric|min:0',
            'demo_rate' => 'required|numeric|min:0',
            'languages_spoken' => 'required|array',
            'teaching_modes' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $profile = $user->teacherProfile;
        if (!$profile) {
            $profile = new TeacherProfile();
            $profile->user_id = $user->id;
        }

        $profile->fill([
            'qualification' => $request->qualification,
            'experience_years' => $request->experience_years,
            'bio' => $request->bio,
            'teaching_methodology' => $request->teaching_methodology,
            'hourly_rate' => $request->hourly_rate,
            'demo_rate' => $request->demo_rate,
            'languages_spoken' => $request->languages_spoken,
            'teaching_modes' => $request->teaching_modes,
            'verification_status' => 'pending' // Reset to pending for verification
        ]);
        $profile->save();

        return response()->json([
            'message' => 'Onboarding information submitted successfully. Waiting for admin approval.',
            'profile' => $profile
        ]);
    }

    /**
     * Set teacher availability slots.
     */
    public function updateAvailability(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'teacher' || !$user->teacherProfile) {
            return response()->json(['message' => 'Unauthorized. Teacher profile not found.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'slots' => 'required|array',
            'slots.*.day_of_week' => 'required_without:slots.*.specific_date|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'slots.*.specific_date' => 'required_without:slots.*.day_of_week|date_format:Y-m-d',
            'slots.*.start_time' => 'required|date_format:H:i:s',
            'slots.*.end_time' => 'required|date_format:H:i:s|after:slots.*.start_time',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $teacher = $user->teacherProfile;

        // Clear existing unbooked slots to overwrite
        Availability::where('teacher_id', $teacher->id)->where('is_booked', false)->delete();

        $createdSlots = [];
        foreach ($request->slots as $slotData) {
            $slot = Availability::create([
                'teacher_id' => $teacher->id,
                'day_of_week' => $slotData['day_of_week'] ?? null,
                'specific_date' => $slotData['specific_date'] ?? null,
                'start_time' => $slotData['start_time'],
                'end_time' => $slotData['end_time'],
                'is_booked' => false
            ]);
            $createdSlots[] = $slot;
        }

        return response()->json([
            'message' => 'Availability slots updated successfully',
            'slots' => $createdSlots
        ]);
    }

    /**
     * Get teacher calendar events (bookings).
     */
    public function calendar(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'teacher' || !$user->teacherProfile) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $bookings = $user->teacherProfile->bookings()
            ->with(['student'])
            ->orderBy('scheduled_start', 'asc')
            ->get();

        return response()->json([
            'bookings' => $bookings
        ]);
    }

    /**
     * View teacher earnings ledger.
     */
    public function earnings(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'teacher') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Get ledger entries
        $ledger = \App\Models\LedgerEntry::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate totals
        $totalEarnings = \App\Models\LedgerEntry::where('user_id', $user->id)
            ->where('type', 'credit')
            ->sum('amount');

        $totalWithdrawn = \App\Models\LedgerEntry::where('user_id', $user->id)
            ->where('type', 'debit')
            ->sum('amount');

        $balance = $totalEarnings - $totalWithdrawn;

        return response()->json([
            'ledger' => $ledger,
            'summary' => [
                'total_earnings' => $totalEarnings,
                'total_withdrawn' => $totalWithdrawn,
                'available_balance' => $balance
            ]
        ]);
    }
}
