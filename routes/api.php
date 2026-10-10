<?php

use App\Models\User;
use App\Http\Controllers\Api\V1\AssignmentController;
use App\Http\Controllers\Api\V1\AssignmentSubmissionController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CourseController;
use App\Http\Controllers\Api\V1\CourseMaterialController;
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\SubmissionGradeController;
use Illuminate\Support\Facades\Route;

// install:api memberi prefix "/api"; grup ini menambah "/v1" => /api/v1/...
Route::prefix('v1')->group(function () {

    // PUBLIK: harus di LUAR grup auth:sanctum, kalau tidak, orang tidak bisa login.
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:api-login');

    // TERLINDUNGI
    Route::middleware(['auth:sanctum', 'throttle:api-umum'])->group(function () {

        // Auth
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);

        // Courses
        Route::get('courses', [CourseController::class, 'index']);
        Route::get('courses/{course}', [CourseController::class, 'show']);
        Route::get('courses/{course}/assignments', [AssignmentController::class, 'index']);
        Route::get('courses/{course}/materials', [CourseMaterialController::class, 'index']);

        // Assignments
        Route::post('assignments', [AssignmentController::class, 'store']);
        Route::match(['put', 'patch'], 'assignments/{assignment}', [AssignmentController::class, 'update']);
        Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy']);
        Route::get('assignments/{assignment}/submissions', [AssignmentSubmissionController::class, 'index']);
        Route::post('assignments/{assignment}/submissions', [AssignmentSubmissionController::class, 'store']); // 12

        // Submissions
        Route::put('submissions/{submission}/grade', [SubmissionGradeController::class, 'update']); // 13

        // Notifications
        Route::get('notifications', [NotificationController::class, 'index']); // 14
        Route::post('notifications/{id}/read', [NotificationController::class, 'markAsRead']); // 15
    });
});
