<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\QuizzesResult;
use App\Models\SessionAttendance;
use App\Models\WebinarAssignmentHistory;
use App\User;
use Illuminate\Http\Request;

class ParentDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if (!$user->isParent()) {
            return redirect('/panel/dashboard');
        }

        $children = $user->children()->wherePivot('status', 'active')->get();

        $selectedChildId = $request->get('child_id', session('parent_active_child_id'));
        $selectedChild = null;

        if (!empty($selectedChildId)) {
            $selectedChild = $children->where('id', $selectedChildId)->first();
        }

        if (empty($selectedChild) && $children->count() > 0) {
            $selectedChild = $children->first();
        }

        if ($selectedChild) {
            session(['parent_active_child_id' => $selectedChild->id]);
        }

        // Child analytics
        $stats = [
            'totalCourses' => 0,
            'completedCourses' => 0,
            'quizzesCount' => 0,
            'quizAvgGrade' => 0,
            'assignmentsCount' => 0,
            'attendanceCount' => 0,
        ];

        $recentQuizzes = collect();
        $recentAssignments = collect();
        $purchasedCourses = collect();

        if ($selectedChild) {
            $purchasedCoursesIds = $selectedChild->getPurchasedCoursesIds();
            $stats['totalCourses'] = count($purchasedCoursesIds);

            // Quizzes
            $quizResults = QuizzesResult::where('user_id', $selectedChild->id)->get();
            $stats['quizzesCount'] = $quizResults->count();
            $stats['quizAvgGrade'] = $quizResults->count() > 0 ? round($quizResults->avg('user_grade'), 1) : 0;
            $recentQuizzes = $quizResults->sortByDesc('created_at')->take(5);

            // Assignments
            $assignments = WebinarAssignmentHistory::where('student_id', $selectedChild->id)->get();
            $stats['assignmentsCount'] = $assignments->count();
            $recentAssignments = $assignments->sortByDesc('created_at')->take(5);

            // Attendance
            $stats['attendanceCount'] = SessionAttendance::where('user_id', $selectedChild->id)->count();

            // Purchased courses objects
            if (!empty($purchasedCoursesIds)) {
                $purchasedCourses = \App\Models\Webinar::whereIn('id', $purchasedCoursesIds)->get();
            }
        }

        $data = [
            'pageTitle' => trans('panel.parent_dashboard') ?? 'Parent Dashboard',
            'children' => $children,
            'selectedChild' => $selectedChild,
            'stats' => $stats,
            'recentQuizzes' => $recentQuizzes,
            'recentAssignments' => $recentAssignments,
            'purchasedCourses' => $purchasedCourses,
        ];

        return view('design_1.panel.parent.dashboard.index', $data);
    }

    public function toggleStudentView($studentId)
    {
        $user = auth()->user();

        if (!$user->isParent()) {
            return redirect('/panel/dashboard');
        }

        $child = $user->children()->where('users.id', $studentId)->first();

        if (!$child) {
            return redirect()->back()->with('toast', [
                'status' => 'error',
                'title' => 'Error',
                'msg' => 'Student not found or not linked to your account.'
            ]);
        }

        session(['parent_proxy_student_id' => $child->id]);

        return redirect('/panel/dashboard')->with('toast', [
            'status' => 'success',
            'title' => 'Student View Active',
            'msg' => "Now viewing panel as {$child->full_name} (Read-Only Mode)."
        ]);
    }

    public function exitStudentView()
    {
        session()->forget('parent_proxy_student_id');

        return redirect('/panel/parent/dashboard')->with('toast', [
            'status' => 'success',
            'title' => 'Returned to Parent View',
            'msg' => 'You are back on your Parent Dashboard.'
        ]);
    }
}
