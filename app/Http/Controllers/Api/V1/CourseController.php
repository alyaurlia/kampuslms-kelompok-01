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
     * Daftar disaring di QUERY sesuai peran (scope Course::visibleTo):
     * - admin     : semua mata kuliah
     * - dosen     : hanya yang dia ampu
     * - mahasiswa : hanya yang dia ikuti
     * Peran tak dikenal => daftar kosong.
     */
    public function index(Request $request): CourseCollection
    {
        Gate::authorize('viewAny', Course::class);

        $courses = Course::query()
            ->visibleTo($request->user())
            ->with('lecturer')                          // cegah N+1
            ->withCount(['materials', 'assignments'])
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