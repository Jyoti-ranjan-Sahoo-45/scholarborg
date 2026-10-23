<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Escrow;
use App\Models\LedgerEntry;
use App\Models\Payout;
use App\Models\SystemRule;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Approve or reject teacher profiles.
     */
    public function verifyTeacher(Request $request, $teacherId)
    {
        $user = $request->user();
        if ($user->type !== 'admin' && $user->type !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:verified,rejected,suspended',
            'admin_comments' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $teacher = TeacherProfile::findOrFail($teacherId);

        DB::transaction(function () use ($teacher, $request, $user) {
            $teacher->update([
                'verification_status' => $request->status
            ]);

            // Log in verification audit trail
            $teacher->verifications()->create([
                'document_type' => 'Profile Verification Decision',
                'document_path' => 'N/A',
                'status' => $request->status === 'verified' ? 'approved' : 'rejected',
                'admin_comments' => $request->admin_comments,
                'verified_by' => $user->id
            ]);
        });

        return response()->json([
            'message' => 'Teacher profile verification updated to: ' . $request->status,
            'teacher' => $teacher->fresh()
        ]);
    }

    /**
     * Release payment escrow manually to teacher (e.g. on class completion or dispute resolution).
     */
    public function releaseEscrow(Request $request, $bookingId)
    {
        $user = $request->user();
        if ($user->type !== 'admin' && $user->type !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $escrow = Escrow::where('booking_id', $bookingId)->firstOrFail();

        if ($escrow->status !== 'held') {
            return response()->json(['message' => 'Escrow is not in held status.'], 400);
        }

        $teacher = $escrow->teacher;
        $booking = $escrow->booking;

        DB::transaction(function () use ($escrow, $teacher, $booking) {
            // 1. Release escrow status
            $escrow->update(['status' => 'released']);
            $booking->update(['status' => 'completed']);

            // 2. Fetch current ledger balance of the teacher
            $lastEntry = LedgerEntry::where('user_id', $teacher->user_id)->orderBy('id', 'desc')->first();
            $balanceBefore = $lastEntry ? $lastEntry->balance_after : 0.00;
            $balanceAfter = $balanceBefore + $escrow->amount;

            // 3. Credit teacher ledger account
            LedgerEntry::create([
                'ledger_uuid' => (string) Str::uuid(),
                'user_id' => $teacher->user_id,
                'amount' => $escrow->amount,
                'type' => 'credit',
                'description' => 'Escrow Release: Booking completed - ID ' . $booking->id,
                'balance_before' => $balanceBefore,
                'balance_after' => $balanceAfter,
                'source_reference' => 'ESCROW_' . $escrow->id
            ]);
        });

        return response()->json([
            'message' => 'Escrow released successfully and funds credited to teacher ledger.',
            'escrow' => $escrow->fresh()
        ]);
    }

    /**
     * Approve and payout requested withdrawals.
     */
    public function processPayout(Request $request, $payoutId)
    {
        $user = $request->user();
        if ($user->type !== 'admin' && $user->type !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:approved,paid,rejected',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payout = Payout::findOrFail($payoutId);

        if ($payout->status === 'paid' || $payout->status === 'rejected') {
            return response()->json(['message' => 'Payout already processed.'], 400);
        }

        $teacher = $payout->teacher;

        DB::transaction(function () use ($payout, $request, $teacher) {
            $payout->update([
                'status' => $request->status,
                'admin_notes' => $request->notes
            ]);

            if ($request->status === 'approved' || $request->status === 'paid') {
                // If paid, create matching debit ledger entry to clear balance
                $lastEntry = LedgerEntry::where('user_id', $teacher->user_id)->orderBy('id', 'desc')->first();
                $balanceBefore = $lastEntry ? $lastEntry->balance_after : 0.00;
                
                if ($balanceBefore < $payout->amount) {
                    throw new \Exception("Insufficient ledger balance to process payout.");
                }

                $balanceAfter = $balanceBefore - $payout->amount;

                LedgerEntry::create([
                    'ledger_uuid' => (string) Str::uuid(),
                    'user_id' => $teacher->user_id,
                    'amount' => $payout->amount,
                    'type' => 'debit',
                    'description' => 'Withdrawal payout processed - ID: ' . $payout->id,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'source_reference' => 'PAYOUT_' . $payout->id
                ]);
            }
        });

        return response()->json([
            'message' => 'Payout request successfully processed to: ' . $request->status,
            'payout' => $payout->fresh()
        ]);
    }

    /**
     * Configure Dynamic Platform Rules.
     */
    public function updateRule(Request $request)
    {
        $user = $request->user();
        if ($user->type !== 'admin' && $user->type !== 'super_admin') {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'key' => 'required|string',
            'value' => 'required',
            'description' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $rule = SystemRule::where('key', $request->key)->first();

        if ($rule) {
            $rule->update([
                'value' => $request->value,
                'description' => $request->description,
                'version' => $rule->version + 1,
                'updated_by' => $user->id
            ]);
        } else {
            $rule = SystemRule::create([
                'key' => $request->key,
                'value' => $request->value,
                'description' => $request->description,
                'version' => 1,
                'updated_by' => $user->id
            ]);
        }

        return response()->json([
            'message' => 'System Rule updated successfully',
            'rule' => $rule
        ]);
    }
}
