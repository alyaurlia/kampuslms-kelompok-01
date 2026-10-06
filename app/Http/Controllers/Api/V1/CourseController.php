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
    public function index(Request $request): CourseCollection
    {
        $user = $request->user();

        $courses = Course::query()
            ->visibleTo($user)
            ->with('lecturer:id,name')          // eager load: cegah N+1 pada resource
            ->orderBy('name')
            ->orderBy('id')
            ->paginate(min(max($request->integer('per_page', 15), 1), 100));

        return new CourseCollection($courses);
    }

    public function show(Request $request, Course $course): CourseResource
    {
        Gate::authorize('view', $course);

        $user = $request->user();

        $course->load('lecturer:id,name')->loadCount([
            'materials',
            // hitungan tugas mengikuti visibilitas (mahasiswa tidak menghitung draft)
            'assignments' => fn ($q) => $q->visibleTo($user),
        ]);

        return new CourseResource($course);
    }
}