<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\TeacherProfile;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    /**
     * List all verified teachers with dynamic filters.
     */
    public function index(Request $request)
    {
        $query = TeacherProfile::with(['user', 'subjects'])
            ->where('verification_status', 'verified');

        // Filter by Subject ID
        if ($request->has('subject_id')) {
            $query->whereHas('subjects', function ($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }

        // Filter by Hourly Price Rate Range
        if ($request->has('min_price')) {
            $query->where('hourly_rate', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('hourly_rate', '<=', $request->max_price);
        }

        // Filter by Experience Years
        if ($request->has('min_experience')) {
            $query->where('experience_years', '>=', $request->min_experience);
        }

        // Filter by Rating
        if ($request->has('min_rating')) {
            $query->where('rating_avg', '>=', $request->min_rating);
        }

        // Search by Teacher Name or Bio
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bio', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Default Marketplace Sorting (verified status and high rating)
        $teachers = $query->orderBy('rating_avg', 'desc')
            ->orderBy('experience_years', 'desc')
            ->paginate(10);

        return response()->json($teachers);
    }

    /**
     * Show detailed teacher profile.
     */
    public function show($id)
    {
        $teacher = TeacherProfile::with(['user', 'subjects', 'availabilities' => function ($q) {
            $q->where('is_booked', false)->where(function ($sq) {
                $sq->where('specific_date', '>=', now()->toDateString())
                   ->orWhereNotNull('day_of_week');
            });
        }])->findOrFail($id);

        return response()->json($teacher);
    }

    /**
     * List all subjects.
     */
    public function subjects()
    {
        $subjects = Subject::all()->groupBy('category');

        return response()->json([
            'subjects' => $subjects
        ]);
    }
}
