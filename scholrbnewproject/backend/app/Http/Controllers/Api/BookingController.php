<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\Package;
use App\Models\Payment;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Lock a slot temporarily for checkout.
     */
    public function lockSlot(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:tmp_teachers,id',
            'availability_slot_id' => 'required|exists:tmp_availabilities,id',
            'booking_type' => 'required|in:demo,class',
            'package_id' => 'required_if:booking_type,class|exists:tmp_packages,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $slot = Availability::find($request->availability_slot_id);

        if ($slot->is_booked) {
            return response()->json(['message' => 'Slot is already booked'], 400);
        }

        // Lock slot and create temporary booking
        $booking = DB::transaction(function () use ($request, $user, $slot) {
            // Lock availability slot
            $slot->update(['is_booked' => true]);

            // Calculate dates
            $start = now(); // Mocking datetime for demonstration
            if ($slot->specific_date) {
                $start = \Carbon\Carbon::parse($slot->specific_date->format('Y-m-d') . ' ' . $slot->start_time);
            }

            $booking = Booking::create([
                'booking_uuid' => (string) Str::uuid(),
                'student_id' => $user->id,
                'teacher_id' => $request->teacher_id,
                'package_id' => $request->package_id ?? null,
                'booking_type' => $request->booking_type,
                'scheduled_start' => $start,
                'scheduled_end' => $start->copy()->addHour(), // Default 1 hour slot
                'lock_expires_at' => now()->addMinutes(10), // 10 minutes lock
                'status' => 'slot_held'
            ]);

            return $booking;
        });

        return response()->json([
            'message' => 'Slot locked successfully for 10 minutes',
            'booking' => $booking
        ], 201);
    }

    /**
     * Confirm booking and process payment (Checkout).
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_uuid' => 'required|exists:tmp_bookings,booking_uuid',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::where('booking_uuid', $request->booking_uuid)->first();

        if ($booking->status !== 'slot_held' && $booking->status !== 'pending_payment') {
            return response()->json(['message' => 'Booking is not in a payable state'], 400);
        }

        if ($booking->lock_expires_at && $booking->lock_expires_at->isPast()) {
            // Unlock slot
            Availability::where('teacher_id', $booking->teacher_id)
                ->where('specific_date', $booking->scheduled_start->format('Y-m-d'))
                ->where('start_time', $booking->scheduled_start->format('H:i:s'))
                ->update(['is_booked' => false]);

            $booking->update(['status' => 'cancelled']);
            return response()->json(['message' => 'Slot lock has expired. Booking cancelled.'], 400);
        }

        $teacher = $booking->teacher;
        $price = $booking->booking_type === 'demo' ? $teacher->demo_rate : $teacher->hourly_rate;

        // Perform transactional payment & escrow creation
        DB::transaction(function () use ($booking, $request, $price) {
            // 1. Create Payment record
            $payment = Payment::create([
                'transaction_reference' => 'PAY_' . Str::upper(Str::random(10)),
                'user_id' => $booking->student_id,
                'booking_id' => $booking->id,
                'amount' => $price,
                'gateway_name' => $request->payment_method,
                'status' => 'paid'
            ]);

            // 2. Set booking status to confirmed
            $booking->update([
                'status' => 'confirmed',
                'lock_expires_at' => null
            ]);

            // 3. Create Escrow record
            $commissionRate = 0.15; // 15% platform commission
            $commission = $price * $commissionRate;
            $teacherEarning = $price - $commission;

            \App\Models\Escrow::create([
                'booking_id' => $booking->id,
                'teacher_id' => $booking->teacher_id,
                'amount' => $teacherEarning,
                'platform_commission' => $commission,
                'status' => 'held'
            ]);

            // 4. Create Class Session meeting placeholder
            $booking->classSession()->create([
                'agora_channel_name' => 'channel_' . $booking->booking_uuid,
                'agora_token' => 'agora_token_placeholder_for_' . $booking->booking_uuid
            ]);

            // If class package is used, increment used classes
            if ($booking->package) {
                $package = $booking->package;
                $package->increment('used_classes');
                if ($package->used_classes >= $package->total_classes) {
                    $package->update(['status' => 'expired']);
                }
            }
        });

        return response()->json([
            'message' => 'Payment received and booking confirmed successfully',
            'booking' => $booking->fresh(['classSession'])
        ]);
    }

    /**
     * Purchase a class package.
     */
    public function purchasePackage(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'teacher_id' => 'required|exists:tmp_teachers,id',
            'total_classes' => 'required|integer|in:4,8,12',
            'payment_method' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $teacher = TeacherProfile::find($request->teacher_id);
        $totalAmount = $teacher->hourly_rate * $request->total_classes;

        $package = DB::transaction(function () use ($request, $user, $totalAmount) {
            $package = Package::create([
                'student_id' => $user->id,
                'teacher_id' => $request->teacher_id,
                'total_classes' => $request->total_classes,
                'used_classes' => 0,
                'total_amount' => $totalAmount,
                'expiry_date' => now()->addMonths(3), // Valid for 3 months
                'status' => 'active'
            ]);

            Payment::create([
                'transaction_reference' => 'PKG_' . Str::upper(Str::random(10)),
                'user_id' => $user->id,
                'package_id' => $package->id,
                'amount' => $totalAmount,
                'gateway_name' => $request->payment_method,
                'status' => 'paid'
            ]);

            return $package;
        });

        return response()->json([
            'message' => 'Package purchased successfully',
            'package' => $package
        ], 201);
    }
}
