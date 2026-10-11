<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\GradeSubmission;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GradeSubmissionRequest;
use App\Http\Resources\GradeResource;
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
     * Upsert (grades.submission_id unique) dikerjakan GradeSubmission, action yang
     * sama dengan form web, supaya perilakunya selalu sama: 201 saat nilai dibuat
     * pertama kali, 200 saat diperbarui.
     *
     * is_published (opsional): true/false mengubah status tampil ke mahasiswa;
     * bila tidak dikirim, nilai lama dipertahankan (nilai baru = draft/false).
     */
    public function update(
        GradeSubmissionRequest $request,
        Submission $submission,
        GradeSubmission $action
    ): JsonResponse {
        // Otorisasi sudah dilakukan di GradeSubmissionRequest::authorize()

        $grade = $action->handle(
            $submission,
            $request->user(),
            $request->validated('score'),
            $request->validated('feedback'),
            $request->has('is_published') ? $request->boolean('is_published') : null,
        );

        return GradeResource::make($grade)
            ->response()
            ->setStatusCode($grade->wasRecentlyCreated ? 201 : 200);
    }
}