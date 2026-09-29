<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    // GET /{peran}/mata-kuliah/{course}/tugas
    public function index(Course $course)
    {
        abort_unless($this->canViewCourse($course), 403);

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
        abort_unless($this->canManageCourse($course), 403);

        // TODO: return view('assignments.create', compact('course'));
    }

    // POST /dosen/mata-kuliah/{course}/tugas
    public function store(Request $request, Course $course)
    {
        abort_unless($this->canManageCourse($course), 403);

        // TODO: validasi, lalu
        // $course->assignments()->create([...$validated, 'created_by' => auth()->id()]);
    }

    // GET /{peran}/tugas/{assignment}  (shallow: tanpa {course})
    public function show(Assignment $assignment)
    {
        abort_unless($this->canViewCourse($assignment->course), 403);
        abort_if(
            auth()->user()->role === 'mahasiswa' && $assignment->status !== 'published',
            404
        );

        // TODO: return view('assignments.show', compact('assignment'));
    }

    // GET /dosen/tugas/{assignment}/edit
    public function edit(Assignment $assignment)
    {
        abort_unless($this->canManageCourse($assignment->course), 403);
    }

    // PUT/PATCH /dosen/tugas/{assignment}
    public function update(Request $request, Assignment $assignment)
    {
        abort_unless($this->canManageCourse($assignment->course), 403);
    }

    // DELETE /dosen/tugas/{assignment}
    public function destroy(Assignment $assignment)
    {
        abort_unless($this->canManageCourse($assignment->course), 403);
    }
}