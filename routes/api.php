<?php

use App\Http\Controllers\Api\V1\AssignmentController;
use App\Http\Controllers\Api\V1\CourseController;
use Illuminate\Support\Facades\Route;

// install:api memberi prefix "/api"; grup ini menambah "/v1" => /api/v1/...
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Courses
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{course}', [CourseController::class, 'show']);
    Route::get('courses/{course}/assignments', [AssignmentController::class, 'index']);

    // Assignments
    Route::post('assignments', [AssignmentController::class, 'store']);
    Route::match(['put', 'patch'], 'assignments/{assignment}', [AssignmentController::class, 'update']);
    Route::delete('assignments/{assignment}', [AssignmentController::class, 'destroy']);
});