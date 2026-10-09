<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AssignmentController extends Controller
{
    // GET /{peran}/mata-kuliah/{course}/tugas
    public function index(Course $course)
    {
        Gate::authorize('view', $course);

        $assignments = $course->assignments()
            ->when(auth()->user()->role === 'mahasiswa',
                fn ($q) => $q->where('status', 'published'))
            ->orderBy('due_at')
            ->paginate(10);

        // TODO: return view('assignments.index', compact('course', 'assignments'));
    }

    // GET /dosen/mata-kuliah/{course}/tugas/create
    public function create(Course $course)
    {
        Gate::authorize('create', [Assignment::class, $course]);

        // TODO: return view('assignments.create', compact('course'));
    }

    // POST /dosen/mata-kuliah/{course}/tugas
    public function store(Request $request, Course $course)
    {
        Gate::authorize('create', [Assignment::class, $course]);

        // TODO: validasi, lalu
        // $course->assignments()->create([...$validated, 'created_by' => auth()->id()]);
    }

    // GET /{peran}/tugas/{assignment}  (shallow: tanpa {course})
    public function show(Assignment $assignment)
    {
        Gate::authorize('view', $assignment);

        // TODO: return view('assignments.show', compact('assignment'));
    }

    // GET /dosen/tugas/{assignment}/edit
    public function edit(Assignment $assignment)
    {
        Gate::authorize('update', $assignment);
    }

    // PUT/PATCH /dosen/tugas/{assignment}
    public function update(Request $request, Assignment $assignment)
    {
        Gate::authorize('update', $assignment);
    }

    // DELETE /dosen/tugas/{assignment}
    public function destroy(Assignment $assignment)
    {
        Gate::authorize('delete', $assignment);
    }
}