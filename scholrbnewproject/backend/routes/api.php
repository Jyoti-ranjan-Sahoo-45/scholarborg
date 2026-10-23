<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\MarketplaceController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\SalesController;
use App\Http\Controllers\Api\RequirementController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\PackageController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/marketplace')->group(function () {
    
    // GUEST ROUTES
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/teachers', [MarketplaceController::class, 'index']);
    Route::get('/teachers/{id}', [MarketplaceController::class, 'show']);
    Route::get('/subjects', [MarketplaceController::class, 'subjects']);
    Route::get('/teachers/{id}/packages', [PackageController::class, 'getTeacherPackages']);

    // AUTHENTICATED ROUTES
    Route::middleware('auth:sanctum')->group(function () {
        
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        // UrbanPro Requirement & Custom Quotes
        Route::post('/requirements', [RequirementController::class, 'store']);
        Route::get('/requirements', [RequirementController::class, 'index']);
        Route::get('/my-requirements', [RequirementController::class, 'myRequirements']);
        Route::post('/requirements/{id}/proposals', [RequirementController::class, 'submitProposal']);
        Route::post('/proposals/{id}/accept', [RequirementController::class, 'acceptProposal']);

        // UrbanPro In-App Direct Messaging
        Route::get('/messages/conversations', [MessageController::class, 'getConversations']);
        Route::get('/messages/{userId}', [MessageController::class, 'getThread']);
        Route::post('/messages', [MessageController::class, 'sendMessage']);

        // UrbanPro Homework & Study Material Vault
        Route::get('/assignments', [AssignmentController::class, 'index']);
        Route::post('/assignments', [AssignmentController::class, 'store']);
        Route::post('/assignments/{id}/submit', [AssignmentController::class, 'submitHomework']);
        Route::post('/assignments/{id}/grade', [AssignmentController::class, 'gradeHomework']);

        // UrbanPro Packages & Milestone Escrow
        Route::post('/packages/purchase', [PackageController::class, 'purchasePackage']);

        // Teacher endpoints
        Route::post('/teacher/onboarding', [TeacherController::class, 'onboard']);
        Route::post('/teacher/availability', [TeacherController::class, 'updateAvailability']);
        Route::get('/teacher/earnings', [TeacherController::class, 'earnings']);
        Route::get('/teacher/calendar', [TeacherController::class, 'calendar']);

        // Parent endpoints
        Route::get('/parent/children', [ParentController::class, 'listChildren']);
        Route::post('/parent/children/add', [ParentController::class, 'addChild']);
        Route::get('/parent/children/{id}/activity', [ParentController::class, 'childActivity']);

        // Sales CRM endpoints
        Route::get('/sales/leads', [SalesController::class, 'leads']);
        Route::post('/sales/leads/{id}/followup', [SalesController::class, 'addFollowup']);
        Route::get('/sales/leads/{id}', [SalesController::class, 'getLeadDetails']);

        // Booking endpoints
        Route::post('/bookings/lock', [BookingController::class, 'lockSlot']);
        Route::post('/bookings/checkout', [BookingController::class, 'checkout']);
        Route::post('/bookings/package', [BookingController::class, 'purchasePackage']);

        // Live Class meeting endpoints
        Route::post('/classes/{classId}/teams', [ClassController::class, 'generateTeamsMeeting']);
        Route::post('/classes/{classId}/agora', [ClassController::class, 'generateAgoraToken']);

        // Admin endpoints
        Route::post('/admin/teachers/{id}/verify', [AdminController::class, 'verifyTeacher']);
        Route::post('/admin/bookings/{id}/release', [AdminController::class, 'releaseEscrow']);
        Route::post('/admin/payouts/{id}/process', [AdminController::class, 'processPayout']);
        Route::post('/admin/rules', [AdminController::class, 'updateRule']);
    });
});
