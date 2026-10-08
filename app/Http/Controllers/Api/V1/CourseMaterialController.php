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
     * Boleh dilihat: admin, dosen pengampu, dan mahasiswa yang terdaftar
     * di mata kuliah ini (CoursePolicy::view).
     */
public function index(Request $request, Course $course): MaterialCollection
{
    $user = $request->user();

    Gate::authorize('view', $course);

    $query = $course->materials();

    if ($user->role === 'dosen') {
        $query->whereHas('course', function ($q) use ($user) {
            $q->where('lecturer_id', $user->id);
        });
    } elseif ($user->role === 'mahasiswa') {
        $query->whereHas('course.students', function ($q) use ($user) {
            $q->whereKey($user->id);
        });
    }

    $materials = $query
        ->latest('id')
        ->paginate(
            min(max($request->integer('per_page', 15), 1), 100)
        );

    return new MaterialCollection($materials);
    }
}