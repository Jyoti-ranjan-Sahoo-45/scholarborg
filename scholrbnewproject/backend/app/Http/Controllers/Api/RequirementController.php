<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LearningRequirement;
use App\Models\TutorProposal;
use App\Models\TeacherProfile;
use App\Models\Booking;
use App\Models\ClassSession;
use App\Models\Escrow;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequirementController extends Controller
{
    /**
     * Post a new learning requirement (Student or Parent).
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_name' => 'required|string|max:191',
            'grade_level' => 'required|string|max:100',
            'board_syllabus' => 'nullable|string|max:100',
            'mode' => 'nullable|in:one-on-one,group,both',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0',
            'preferred_timings' => 'nullable|string|max:191',
            'description' => 'nullable|string',
        ]);

        $requirement = LearningRequirement::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'subject_id' => $request->subject_id,
            'subject_name' => $request->subject_name,
            'grade_level' => $request->grade_level,
            'board_syllabus' => $request->board_syllabus ?? 'General',
            'mode' => $request->mode ?? 'one-on-one',
            'budget_min' => $request->budget_min ?? 20.00,
            'budget_max' => $request->budget_max ?? 100.00,
            'preferred_timings' => $request->preferred_timings ?? 'Flexible',
            'description' => $request->description,
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Learning requirement successfully published to marketplace feed',
            'requirement' => $requirement
        ], 201);
    }

    /**
     * List open requirements (Feed for Teachers).
     */
    public function index(Request $request)
    {
        $query = LearningRequirement::with(['user:id,name,email', 'proposals.teacher.user:id,name'])
            ->where('status', 'open')
            ->orderBy('created_at', 'desc');

        if ($request->has('subject')) {
            $query->where('subject_name', 'like', "%{$request->subject}%");
        }

        $requirements = $query->paginate(15);
        return response()->json($requirements);
    }

    /**
     * List requirements posted by the logged-in student or parent.
     */
    public function myRequirements(Request $request)
    {
        $requirements = LearningRequirement::with(['proposals.teacher.user:id,name'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'requirements' => $requirements
        ]);
    }

    /**
     * Teacher submits a custom quote / proposal.
     */
    public function submitProposal(Request $request, $requirementId)
    {
        $user = $request->user();
        $teacher = TeacherProfile::where('user_id', $user->id)->first();

        if (!$teacher) {
            return response()->json(['message' => 'Only registered teachers can submit quotes.'], 403);
        }

        $request->validate([
            'proposed_hourly_rate' => 'required|numeric|min:5',
            'pitch_message' => 'required|string|min:10',
            'available_slot_id' => 'nullable|integer',
        ]);

        $proposal = TutorProposal::create([
            'requirement_id' => $requirementId,
            'teacher_id' => $teacher->id,
            'proposed_hourly_rate' => $request->proposed_hourly_rate,
            'pitch_message' => $request->pitch_message,
            'available_slot_id' => $request->available_slot_id,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Proposal successfully submitted to the student',
            'proposal' => $proposal
        ], 201);
    }

    /**
     * Student accepts proposal and books session.
     */
    public function acceptProposal(Request $request, $proposalId)
    {
        $proposal = TutorProposal::with(['requirement', 'teacher'])->findOrFail($proposalId);
        
        // Ensure student owns the requirement
        if ((int)$proposal->requirement->user_id !== (int)$request->user()->id) {
            return response()->json(['message' => 'Unauthorized to accept this proposal.'], 403);
        }

        $proposal->update(['status' => 'accepted']);
        $proposal->requirement->update(['status' => 'matched']);

        // Generate instant Booking & Escrow Hold matching tmp_bookings schema
        $booking = Booking::create([
            'booking_uuid' => (string) Str::uuid(),
            'student_id' => $request->user()->id,
            'teacher_id' => $proposal->teacher_id,
            'booking_type' => 'demo',
            'status' => 'confirmed',
            'scheduled_start' => now()->addDays(2),
            'scheduled_end' => now()->addDays(2)->addHour(),
        ]);

        // Create Class Session matching tmp_classes schema
        $session = ClassSession::create([
            'booking_id' => $booking->id,
        ]);

        // Create Escrow Hold matching tmp_escrows schema
        $escrow = Escrow::create([
            'booking_id' => $booking->id,
            'teacher_id' => $proposal->teacher_id,
            'amount' => $proposal->proposed_hourly_rate,
            'platform_commission' => round($proposal->proposed_hourly_rate * 0.15, 2),
            'status' => 'held',
        ]);

        return response()->json([
            'message' => 'Proposal accepted! Lesson booked with escrow protection.',
            'booking' => $booking,
            'session' => $session,
            'escrow' => $escrow
        ]);
    }
}
