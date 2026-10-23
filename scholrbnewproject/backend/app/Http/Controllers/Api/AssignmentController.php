<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AssignmentVault;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    /**
     * List assignments for student or teacher.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->type === 'teacher') {
            $teacher = TeacherProfile::where('user_id', $user->id)->first();
            $assignments = AssignmentVault::with(['student:id,name,email', 'classSession'])
                ->where('teacher_id', $teacher ? $teacher->id : 0)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Student or Parent
            $assignments = AssignmentVault::with(['teacher.user:id,name,email', 'classSession'])
                ->where('student_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json([
            'assignments' => $assignments
        ]);
    }

    /**
     * Teacher creates an assignment or study material.
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $teacher = TeacherProfile::where('user_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Only teachers can create assignments.'], 403);
        }

        $request->validate([
            'student_id' => 'required|integer|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_url' => 'nullable|string',
            'due_date' => 'nullable|date',
            'class_id' => 'nullable|integer',
        ]);

        $assignment = AssignmentVault::create([
            'class_id' => $request->class_id,
            'teacher_id' => $teacher->id,
            'student_id' => $request->student_id,
            'title' => $request->title,
            'description' => $request->description,
            'file_url' => $request->file_url,
            'due_date' => $request->due_date,
            'submission_status' => 'assigned',
        ]);

        return response()->json([
            'message' => 'Assignment / Study Material successfully assigned to student',
            'assignment' => $assignment
        ], 201);
    }

    /**
     * Student submits homework file/notes.
     */
    public function submitHomework(Request $request, $id)
    {
        $assignment = AssignmentVault::where('id', $id)
            ->where('student_id', $request->user()->id)
            ->firstOrFail();

        $request->validate([
            'submission_url' => 'required|string',
        ]);

        $assignment->update([
            'submission_url' => $request->submission_url,
            'submission_status' => 'submitted',
        ]);

        return response()->json([
            'message' => 'Homework submitted successfully!',
            'assignment' => $assignment
        ]);
    }

    /**
     * Teacher grades assignment & gives qualitative feedback.
     */
    public function gradeHomework(Request $request, $id)
    {
        $user = $request->user();
        $teacher = TeacherProfile::where('user_id', $user->id)->first();

        $assignment = AssignmentVault::where('id', $id)
            ->where('teacher_id', $teacher ? $teacher->id : 0)
            ->firstOrFail();

        $request->validate([
            'grade_score' => 'required|string|max:50',
            'teacher_feedback' => 'required|string',
        ]);

        $assignment->update([
            'grade_score' => $request->grade_score,
            'teacher_feedback' => $request->teacher_feedback,
            'submission_status' => 'graded',
        ]);

        return response()->json([
            'message' => 'Assignment successfully graded with feedback',
            'assignment' => $assignment
        ]);
    }
}
