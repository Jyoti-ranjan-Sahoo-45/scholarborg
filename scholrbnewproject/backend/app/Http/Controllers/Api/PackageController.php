<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoursePackage;
use App\Models\TeacherProfile;
use App\Models\Booking;
use App\Models\Escrow;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    /**
     * List packages for a teacher.
     */
    public function getTeacherPackages($teacherId)
    {
        $packages = CoursePackage::where('teacher_id', $teacherId)
            ->where('is_active', 1)
            ->get();

        if ($packages->isEmpty()) {
            $teacher = TeacherProfile::find($teacherId);
            $rate = $teacher ? $teacher->hourly_rate : 50.00;

            // Generate tiered bundles (5, 10, 20 lessons)
            $packages = [
                [
                    'id' => 1,
                    'name' => '5-Lesson Starter Pack',
                    'total_classes' => 5,
                    'discount_percent' => 5.0,
                    'total_price' => round($rate * 5 * 0.95, 2),
                    'price_per_class' => round($rate * 0.95, 2),
                ],
                [
                    'id' => 2,
                    'name' => '10-Lesson Mastery Pack',
                    'total_classes' => 10,
                    'discount_percent' => 10.0,
                    'total_price' => round($rate * 10 * 0.90, 2),
                    'price_per_class' => round($rate * 0.90, 2),
                    'popular' => true,
                ],
                [
                    'id' => 3,
                    'name' => '20-Lesson Exam Bootpack',
                    'total_classes' => 20,
                    'discount_percent' => 15.0,
                    'total_price' => round($rate * 20 * 0.85, 2),
                    'price_per_class' => round($rate * 0.85, 2),
                ]
            ];
        }

        return response()->json([
            'packages' => $packages
        ]);
    }

    /**
     * Student purchases multi-class package with milestone escrow holding.
     */
    public function purchasePackage(Request $request)
    {
        $request->validate([
            'teacher_id' => 'required|integer|exists:tmp_teachers,id',
            'package_name' => 'required|string',
            'total_classes' => 'required|integer|min:1',
            'total_price' => 'required|numeric|min:1',
        ]);

        $booking = Booking::create([
            'booking_uuid' => (string) Str::uuid(),
            'student_id' => $request->user()->id,
            'teacher_id' => $request->teacher_id,
            'booking_type' => 'class',
            'status' => 'confirmed',
            'scheduled_start' => now()->addDays(3),
            'scheduled_end' => now()->addDays(3)->addHour(),
        ]);

        // Hold entire package funds in platform escrow vault
        $escrow = Escrow::create([
            'booking_id' => $booking->id,
            'teacher_id' => $request->teacher_id,
            'amount' => $request->total_price,
            'platform_commission' => round($request->total_price * 0.15, 2),
            'status' => 'held',
        ]);

        return response()->json([
            'message' => 'Course package successfully purchased! Milestone escrow secured in platform vault.',
            'booking' => $booking,
            'escrow' => $escrow
        ], 201);
    }
}
