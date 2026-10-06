<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GradeSubmissionRequest;
use App\Http\Resources\GradeResource;
use App\Models\Grade;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;

class SubmissionGradeController extends Controller
{
    /**
     * PUT /api/v1/submissions/{submission}/grade
     *
     * Hanya dosen yang mengampu mata kuliah dari assignment submission
     * yang boleh memberi atau mengubah nilai.
     *
     * updateOrCreate() dipakai karena grades.submission_id bersifat unique.
     */
    public function update(
        GradeSubmissionRequest $request,
        Submission $submission
    ): JsonResponse {
        $user = $request->user();

        $isOwner = $user->role === 'dosen'
            && (int) $submission->assignment->course->lecturer_id === (int) $user->id;

        if (! $isOwner) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses ke sumber daya ini.',
            ], 403);
        }

        $grade = Grade::updateOrCreate(
            ['submission_id' => $submission->id],
            [
                'graded_by' => $user->id,
                'score'     => $request->validated('score'),
                'feedback'  => $request->validated('feedback'),
                'graded_at' => now(),
            ]
        );

        $status = $grade->wasRecentlyCreated ? 201 : 200;

        return GradeResource::make($grade)
            ->response()
            ->setStatusCode($status);
    }
}
