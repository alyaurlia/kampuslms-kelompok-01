<?php

namespace App\Http\Controllers;

use App\Actions\GradeSubmission;
use App\Http\Requests\StoreGradeRequest;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;

class GradeController extends Controller
{
    // PUT /dosen/pengumpulan/{submission}/nilai   (nama route: dosen.pengumpulan.nilai)
    public function update(StoreGradeRequest $request, Submission $submission, GradeSubmission $action): RedirectResponse
    {
        // Otorisasi (SubmissionPolicy::grade) + validasi sudah ditangani StoreGradeRequest.
        $grade = $action->handle(
            $submission,
            $request->user(),
            $request->validated('score'),
            $request->validated('feedback'),
            $request->has('is_published') ? $request->boolean('is_published') : null,
        );

        return back()->with('grade_success', $grade->wasRecentlyCreated
            ? 'Nilai berhasil disimpan.'
            : 'Nilai berhasil diperbarui.');
    }
}