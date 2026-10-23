<?php

namespace App\Http\Controllers\Api\Panel;

use App\Http\Controllers\Api\Controller;
use App\Models\ParentStudent;
use App\Services\ZohoCrmService;
use App\Models\Webinar;
use App\Models\QuizzesResult;
use App\Models\Support;
use App\Models\ReserveMeeting;
use App\User;
use Illuminate\Http\Request;

class ParentChildrenController extends Controller
{
    public function index()
    {
        $user = apiAuth();

        if (!$user->isParent()) {
            return apiResponse2(0, 'unauthorized', 'Only parent accounts are authorized.');
        }

        $children = $user->children()->get();

        $childrenData = $children->map(function ($child) {
            return [
                'id' => $child->id,
                'full_name' => $child->full_name,
                'email' => $child->email,
                'username' => $child->username,
                'student_code' => $child->student_code,
                'avatar' => $child->getAvatar(),
                'status' => $child->pivot->status,
                'created_at' => $child->pivot->created_at,
            ];
        });

        return apiResponse2(1, 'retrieved', 'Children list retrieved successfully.', [
            'children' => $childrenData
        ]);
    }

    public function store(Request $request)
    {
        $user = apiAuth();

        if (!$user->isParent()) {
            return apiResponse2(0, 'unauthorized', 'Only parent accounts are authorized.');
        }

        $rules = [
            'student_code_or_email' => 'required|string',
        ];
        validateParam($request->all(), $rules);

        $query = trim($request->input('student_code_or_email'));

        $student = User::where('student_code', $query)
            ->orWhere('email', $query)
            ->orWhere('username', $query)
            ->first();

        if (!$student) {
            return apiResponse2(0, 'not_found', 'No student account found with the provided Code or Email.');
        }

        if ($student->id == $user->id) {
            return apiResponse2(0, 'invalid', 'You cannot link your own account as a child.');
        }

        $existing = ParentStudent::where('parent_id', $user->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return apiResponse2(0, 'exists', 'This student is already linked to your parent account.');
        }

        ParentStudent::create([
            'parent_id' => $user->id,
            'student_id' => $student->id,
            'access_code' => $student->student_code,
            'status' => 'active',
            'created_at' => time(),
        ]);

        // Sync lead note to Zoho CRM if Parent has a Zoho Lead ID
        if (!empty($user->zoho_lead_id)) {
            try {
                $zohoService = new ZohoCrmService();
                $noteContent = "Linked Child via API: {$student->full_name} (Email: {$student->email}, Code: {$student->student_code}).";
                $zohoService->addLeadNote($user->zoho_lead_id, $noteContent);
            } catch (\Exception $e) {
                // Log silently
            }
        }

        return apiResponse2(1, 'linked', 'Child linked successfully!', [
            'child' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'email' => $student->email,
                'student_code' => $student->student_code,
            ]
        ]);
    }

    public function destroy($id)
    {
        $user = apiAuth();

        if (!$user->isParent()) {
            return apiResponse2(0, 'unauthorized', 'Only parent accounts are authorized.');
        }

        $deleted = ParentStudent::where('parent_id', $user->id)
            ->where('student_id', $id)
            ->delete();

        if (!$deleted) {
            return apiResponse2(0, 'failed', 'Child relation not found or already unlinked.');
        }

        return apiResponse2(1, 'unlinked', 'Child account unlinked successfully.');
    }

    public function progress($id)
    {
        $user = apiAuth();

        if (!$user->isParent()) {
            return apiResponse2(0, 'unauthorized', 'Only parent accounts are authorized.');
        }

        // Verify relationship
        $isLinked = ParentStudent::where('parent_id', $user->id)
            ->where('student_id', $id)
            ->exists();

        if (!$isLinked) {
            return apiResponse2(0, 'unauthorized', 'You are not linked to this student account.');
        }

        $student = User::find($id);

        if (!$student) {
            return apiResponse2(0, 'not_found', 'Student account not found.');
        }

        // 1. Purchased courses list & progress
        $webinarsIds = $student->getPurchasedCoursesIds();
        $webinars = Webinar::whereIn('id', $webinarsIds)
            ->where('status', 'active')
            ->get();

        $coursesData = $webinars->map(function ($webinar) use ($student) {
            return [
                'id' => $webinar->id,
                'title' => $webinar->title,
                'category' => $webinar->category ? $webinar->category->title : null,
                'teacher' => $webinar->teacher ? $webinar->teacher->full_name : null,
                'progress' => $webinar->getProgress(false, $student) ?? 0,
            ];
        });

        // 2. Recent quiz results
        $quizResults = QuizzesResult::where('user_id', $student->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $quizzesData = $quizResults->map(function ($result) {
            return [
                'id' => $result->id,
                'quiz_title' => $result->quiz ? $result->quiz->title : 'Quiz',
                'user_grade' => $result->user_grade,
                'status' => $result->status, // passed or failed
                'created_at' => $result->created_at,
            ];
        });

        // 3. Support tickets status
        $openTicketsCount = Support::where('user_id', $student->id)->where('status', 'open')->count();

        // 4. Meeting appointments
        $reserveMeetings = ReserveMeeting::where('user_id', $student->id)
            ->whereHas('sale')
            ->where('status', ReserveMeeting::$open)
            ->get();

        $meetingsData = $reserveMeetings->map(function ($meeting) {
            return [
                'id' => $meeting->id,
                'meeting_title' => 'Meeting with ' . ($meeting->meeting && $meeting->meeting->creator ? $meeting->meeting->creator->full_name : 'Teacher'),
                'date' => $meeting->day,
                'time' => $meeting->time,
            ];
        });

        return apiResponse2(1, 'retrieved', 'Student progress retrieved successfully.', [
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'email' => $student->email,
                'student_code' => $student->student_code,
            ],
            'courses' => $coursesData,
            'quizzes' => $quizzesData,
            'meetings' => $meetingsData,
            'open_tickets_count' => $openTicketsCount,
        ]);
    }
}
