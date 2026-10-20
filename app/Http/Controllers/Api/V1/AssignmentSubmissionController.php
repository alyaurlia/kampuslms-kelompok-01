<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\SubmitAssignment;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSubmissionRequest;
use App\Http\Resources\SubmissionCollection;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AssignmentSubmissionController extends Controller
{
    /**
     * GET /api/v1/assignments/{assignment}/submissions?page=&per_page=
     *
     * - admin     : semua submission tugas ini
     * - dosen     : semua submission, HANYA kalau dia pengampu mata kuliahnya
     * - mahasiswa : hanya submission miliknya, dan hanya kalau dia terdaftar
     *               (nilai hanya tampil jika sudah dipublikasikan)
     *
     * Otorisasi lewat AssignmentPolicy::view; penyaringan di QUERY.
     */
    public function index(Request $request, Assignment $assignment): SubmissionCollection
    {
        Gate::authorize('view', $assignment);

        $user = $request->user();

        $query = $assignment->submissions()
            ->withCount('revisions')
            ->with([
                'student',
                'grade' => function ($q) use ($user) {
                    if ($user->role === 'mahasiswa') {
                        $q->where('is_published', true);
                    }
                },
            ]);

        if ($user->role === 'mahasiswa') {
            $query->where('user_id', $user->id);
        }

        $submissions = $query
            ->latest('id')
            ->paginate(min(max($request->integer('per_page', 15), 1), 100));

        return new SubmissionCollection($submissions);
    }

    /**
     * POST /api/v1/assignments/{assignment}/submissions   (multipart, field "file", opsional "note")
     *
     * - hanya mahasiswa terdaftar di MK tugas ini, tugas published, MK active,
     *   dan tugas masih menerima pengumpulan (AssignmentPolicy::submit lewat
     *   StoreSubmissionRequest::authorize())
     * - kumpul ulang MENIMPA pengumpulan lama (sama seperti web)
     * - berkas disimpan di disk privat
     *
     * Respons: 201 + SubmissionResource bila pertama kali, 200 bila menimpa.
     */
    public function store(StoreSubmissionRequest $request, Assignment $assignment, SubmitAssignment $submit): JsonResponse
    {
        $submission = $submit->handle(
            $assignment,
            $request->user(),
            $request->file('file'),
            $request->input('note'),
        );

        $submission->load(['student', 'grade'])->loadCount('revisions');

        return (new SubmissionResource($submission))
            ->response()
            ->setStatusCode($submission->wasRecentlyCreated ? 201 : 200);
    }
}