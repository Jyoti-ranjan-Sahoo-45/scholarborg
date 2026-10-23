<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParentController extends Controller
{
    /**
     * List all children associated with parent.
     */
    public function listChildren(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'parent') {
            return response()->json(['message' => 'Unauthorized. Must be a parent.'], 403);
        }

        $children = $user->parentChildren()->with('user')->get();

        return response()->json([
            'children' => $children
        ]);
    }

    /**
     * Link child student profile to parent account.
     */
    public function addChild(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'parent') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'child_email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $childUser = User::where('email', $request->child_email)->where('type', 'student')->first();

        if (!$childUser) {
            return response()->json(['message' => 'User found is not a student.'], 400);
        }

        $studentProfile = $childUser->studentProfile;
        if (!$studentProfile) {
            $studentProfile = StudentProfile::create([
                'user_id' => $childUser->id,
                'timezone' => 'UTC'
            ]);
        }

        // Link parent
        $studentProfile->update([
            'parent_id' => $user->id
        ]);

        return response()->json([
            'message' => 'Child account linked successfully',
            'child' => $studentProfile->fresh('user')
        ]);
    }

    /**
     * Get learning timeline and activity for child.
     */
    public function childActivity(Request $request, $childStudentId)
    {
        $user = $request->user();
        if ($user->type !== 'parent') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $studentProfile = StudentProfile::where('user_id', $childStudentId)
            ->where('parent_id', $user->id)
            ->firstOrFail();

        // Fetch bookings & classes
        $bookings = \App\Models\Booking::where('student_id', $childStudentId)
            ->with(['teacher.user', 'classSession.attendances'])
            ->orderBy('scheduled_start', 'desc')
            ->get();

        return response()->json([
            'child_user_id' => $childStudentId,
            'bookings' => $bookings
        ]);
    }
}
