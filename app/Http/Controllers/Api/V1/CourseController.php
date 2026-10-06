<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseCollection;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    /**
     * GET /api/v1/courses?q=&status=&page=&per_page=
     *
     * Daftar disaring di QUERY sesuai peran:
     * - admin     : semua mata kuliah
     * - dosen     : hanya yang dia ampu
     * - mahasiswa : hanya yang dia ikuti
     * Peran tak dikenal => 403 (bukan error 500).
     */
    public function index(Request $request): CourseCollection
    {
        $user = $request->user();

        $query = Course::query()
            ->with('lecturer')                          // cegah N+1
            ->withCount(['materials', 'assignments']);

        $query = match ($user->role) {
            'admin'     => $query,
            'dosen'     => $query->where('lecturer_id', $user->id),
            'mahasiswa' => $query->whereHas('students', fn ($q) => $q->whereKey($user->id)),
            default     => abort(403),
        };

        $courses = $query
            ->when($request->filled('q'), fn ($query) =>
                // orWhere WAJIB dibungkus closure agar tidak merusak filter lain
                $query->where(fn ($q) =>
                    $q->where('name', 'like', '%' . $request->q . '%')
                      ->orWhere('code', 'like', '%' . $request->q . '%')))
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->status))
            ->latest('id')
            ->paginate(min(max($request->integer('per_page', 15), 1), 100));

        return new CourseCollection($courses);
    }

    /**
     * GET /api/v1/courses/{course}
     * Aturan akses ada di CoursePolicy::view.
     */
    public function show(Course $course): CourseResource
    {
        Gate::authorize('view', $course);   // 403 kalau tidak berhak

        return new CourseResource(
            $course->load('lecturer')->loadCount(['materials', 'assignments'])
        );
    }
}