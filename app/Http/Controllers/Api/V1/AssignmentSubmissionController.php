<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubmissionRequest;
use App\Http\Resources\SubmissionCollection;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentSubmissionController extends Controller
{
    /**
     * GET /api/v1/assignments/{assignment}/submissions?page=&per_page=
     *
     * - admin     : semua submission tugas ini
     * - dosen     : semua submission, HANYA kalau dia pengampu mata kuliahnya
     * - mahasiswa : hanya submission miliknya, dan hanya kalau dia terdaftar
     *
     * Penyaringan di QUERY; peran tak dikenal => 403.
     * TODO minggu 7: pindahkan ke SubmissionPolicy::viewAny.
     */
    public function index(Request $request, Assignment $assignment): SubmissionCollection
    {
        $user   = $request->user();
        $course = $assignment->course;

        $query = $assignment->submissions()->with(['student', 'grade']);

        $query = match ($user->role) {
            'admin'     => $query,
            'dosen'     => $course->lecturer_id === $user->id
                                ? $query
                                : abort(403),
            'mahasiswa' => $course->students()->whereKey($user->id)->exists()
                                ? $query->where('user_id', $user->id)
                                : abort(403),
            default     => abort(403),
        };

        $submissions = $query
            ->latest('id')
            ->paginate(min(max($request->integer('per_page', 15), 1), 100));

        return new SubmissionCollection($submissions);
    }

    /**
     * POST /api/v1/assignments/{assignment}/submissions   (multipart, field "file")
     *
     * - hanya mahasiswa yang terdaftar di mata kuliah tugas ini
     *   (dicek di StoreSubmissionRequest::authorize() lewat AssignmentPolicy::submit)
     * - satu mahasiswa satu pengumpulan per tugas => 409 kalau sudah ada
     * - berkas disimpan di disk privat (bukan public)
     *
     * Respons: 201 + SubmissionResource.
     */
    public function store(StoreSubmissionRequest $request, Assignment $assignment): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();

        if ($assignment->submissions()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'Anda sudah mengumpulkan tugas ini.',
            ], 409);
        }

        $file = $request->file('file');

        $submittedAt = now();

        $submission = $assignment->submissions()->create([
            'user_id'       => $user->id,
            'file_path'     => $file->store("submissions/{$assignment->id}"),
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'submitted_at'  => $submittedAt,
            'is_late'       => $submittedAt->gt($assignment->due_at),
        ]);

        $submission->load(['user', 'grade']);

        return (new SubmissionResource($submission))
            ->response()
            ->setStatusCode(201);
    }
}