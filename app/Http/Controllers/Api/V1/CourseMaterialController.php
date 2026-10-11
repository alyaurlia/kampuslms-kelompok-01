<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialCollection;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CourseMaterialController extends Controller
{
    /**
     * GET /api/v1/courses/{course}/materials?page=&per_page=
     *
     * Boleh dilihat: admin, dosen pengampu, dan mahasiswa yang terdaftar di
     * mata kuliah ini DAN berstatus active (CoursePolicy::view). Selain itu 403.
     *
     * Gate di bawah sudah mencakup seluruh aturan akses per peran, jadi query
     * tidak perlu disaring lagi per peran: yang lolos Gate pasti berhak
     * melihat semua materi di mata kuliah ini.
     */
    public function index(Request $request, Course $course): MaterialCollection
    {
        Gate::authorize('view', $course);

        $materials = $course->materials()
            ->latest('id')
            ->paginate(
                min(max($request->integer('per_page', 15), 1), 100)
            );

        return new MaterialCollection($materials);
    }
}