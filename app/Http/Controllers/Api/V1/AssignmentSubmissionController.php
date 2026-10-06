<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\SubmissionCollection;
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

        $query = $assignment->submissions()->with(['user', 'grade']);

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
}