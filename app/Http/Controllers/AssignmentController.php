<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssignmentRequest;
use App\Http\Requests\UpdateAssignmentRequest;
use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /** Prefix peran untuk nama route: 'dosen' atau 'mahasiswa'. */
    private function role(): string
    {
        return auth()->user()->role;
    }

    /**
     * Simpan lampiran soal ke disk 'public' (storage/app/public/assignments/{course_id})
     * dan kembalikan kolom metadata-nya.
     */
    private function storeAttachment(UploadedFile $file, Course $course): array
    {
        $meta = [
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getClientMimeType(),
        ];

        return $meta + [
            'file_path' => $file->store("assignments/{$course->id}", 'public'),
        ];
    }

    private function clearAttachment(): array
    {
        return [
            'file_path'     => null,
            'original_name' => null,
            'file_size'     => null,
            'mime_type'     => null,
        ];
    }

    // GET /{peran}/mata-kuliah/{course}/tugas
    public function index(Course $course)
    {
        Gate::authorize('view', $course);

        $assignments = $course->assignments()
            ->when($this->role() === 'mahasiswa',
                fn ($q) => $q->where('status', 'published'))
            ->orderBy('due_at')
            ->paginate(10)
            ->withQueryString();

        return view('assignments.index', [
            'course'      => $course,
            'assignments' => $assignments,
            'role'        => $this->role(),
        ]);
    }

    // GET /dosen/mata-kuliah/{course}/tugas/create
    public function create(Course $course)
    {
        Gate::authorize('create', [Assignment::class, $course]);

        return view('assignments.create', [
            'course' => $course,
            'role'   => $this->role(),
        ]);
    }

    // POST /dosen/mata-kuliah/{course}/tugas
    public function store(StoreAssignmentRequest $request, Course $course)
    {
        Gate::authorize('create', [Assignment::class, $course]);

        $data = $request->validated();
        unset($data['attachment']);

        if ($file = $request->file('attachment')) {
            $data += $this->storeAttachment($file, $course);
        }

        // created_by diisi dari server; course_id ikut terisi lewat relasi.
        $assignment = new Assignment($data);
        $assignment->created_by = auth()->id();
        $course->assignments()->save($assignment);

        return redirect()
            ->route($this->role() . '.mata-kuliah.tugas.index', $course)
            ->with('success', 'Tugas berhasil ditambahkan.');
    }

    // GET /{peran}/tugas/{assignment}  (shallow: tanpa {course})
    public function show(Assignment $assignment)
    {
        Gate::authorize('view', $assignment);

        $user = auth()->user();

        // Pengumpulan milik mahasiswa yang login (null untuk dosen/admin)
        $mySubmission = $user->role === 'mahasiswa'
            ? $assignment->submissions()
                ->where('user_id', $user->id)
                ->with('grade')
                ->first()
            : null;

        return view('assignments.show', [
            'assignment'   => $assignment,
            'course'       => $assignment->course,
            'mySubmission' => $mySubmission,
            'role'         => $this->role(),
        ]);
    }

    // GET /dosen/tugas/{assignment}/edit
    public function edit(Assignment $assignment)
    {
        Gate::authorize('update', $assignment);

        return view('assignments.edit', [
            'assignment' => $assignment,
            'course'     => $assignment->course,
            'role'       => $this->role(),
        ]);
    }

    // PUT/PATCH /dosen/tugas/{assignment}
    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        Gate::authorize('update', $assignment);

        $data = $request->validated();
        unset($data['attachment'], $data['remove_attachment']);

        $file   = $request->file('attachment');
        $remove = $request->boolean('remove_attachment');

        if ($file || $remove) {
            if ($assignment->file_path) {
                Storage::disk('public')->delete($assignment->file_path);
            }

            $data += $file
                ? $this->storeAttachment($file, $assignment->course)
                : $this->clearAttachment();
        }

        $assignment->update($data);

        return redirect()
            ->route($this->role() . '.tugas.show', $assignment)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    // DELETE /dosen/tugas/{assignment}
    public function destroy(Assignment $assignment)
    {
        Gate::authorize('delete', $assignment);

        $course = $assignment->course;

        if ($assignment->file_path) {
            Storage::disk('public')->delete($assignment->file_path);
        }

        $assignment->delete();

        return redirect()
            ->route($this->role() . '.mata-kuliah.tugas.index', $course)
            ->with('success', 'Tugas berhasil dihapus.');
    }
}