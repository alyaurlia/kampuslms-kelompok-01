<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Prefix nama route dari route yang sedang berjalan.
     * 'admin.mata-kuliah.index' -> 'admin.mata-kuliah'
     * Dipakai agar controller yang sama melayani admin, dosen, dan mahasiswa.
     */
    private function routePrefix(): string
    {
        return Str::beforeLast(request()->route()->getName(), '.');
    }

    /**
     * Boleh melihat detail mata kuliah?
     * SEMENTARA: diganti Gate/Policy di minggu 7.
     */
    private function canViewCourse(Course $course): bool
    {
        $user = auth()->user();

        return match ($user->role) {
            'admin'     => true,
            'dosen'     => $course->lecturer_id === $user->id,
            'mahasiswa' => $course->status === 'active',
            default     => false,
        };
    }

    /**
     * Boleh mengubah mata kuliah?
     * Admin: semua. Dosen: hanya mata kuliah yang diampunya.
     */
    private function canManageCourse(Course $course): bool
    {
        $user = auth()->user();

        return match ($user->role) {
            'admin' => true,
            'dosen' => $course->lecturer_id === $user->id,
            default => false,
        };
    }

    /**
     * GET /{peran}/mata-kuliah
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        $mataKuliah = Course::query()
            ->with('lecturer')
            // Dosen hanya melihat mata kuliah miliknya
            ->when($user->role === 'dosen', fn ($query) =>
                $query->where('lecturer_id', $user->id))
            // Mahasiswa hanya melihat mata kuliah yang aktif
            ->when($user->role === 'mahasiswa', fn ($query) =>
                $query->where('status', 'active'))
            ->when($request->filled('q'), fn ($query) =>
                $query->where(fn ($q) =>
                    $q->where('name', 'like', '%' . $request->q . '%')
                      ->orWhere('code', 'like', '%' . $request->q . '%')))
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('courses.index', [
            'mataKuliah'  => $mataKuliah,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * GET /admin/mata-kuliah/create
     * Route hanya ada di grup admin (dijaga role:admin).
     */
    public function create()
    {
        return view('courses.create', [
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * POST /admin/mata-kuliah
     * Route hanya ada di grup admin (dijaga role:admin).
     */
    public function store(StoreCourseRequest $request)
    {
        Course::create($request->validated());

        return redirect()
            ->route($this->routePrefix() . '.index')
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    /**
     * GET /{peran}/mata-kuliah/{mata_kuliah}
     * Nama parameter HARUS "mata_kuliah" (sesuai wildcard Route::resource).
     */
    public function show(Course $mata_kuliah)
    {
        abort_unless($this->canViewCourse($mata_kuliah), 403);

        $mata_kuliah->load('lecturer');

        return view('courses.show', [
            'mataKuliah'  => $mata_kuliah,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * GET /{admin|dosen}/mata-kuliah/{mata_kuliah}/edit
     */
    public function edit(Course $mata_kuliah)
    {
        abort_unless($this->canManageCourse($mata_kuliah), 403);

        return view('courses.edit', [
            'mataKuliah'  => $mata_kuliah,
            'routePrefix' => $this->routePrefix(),
        ]);
    }

    /**
     * PUT/PATCH /{admin|dosen}/mata-kuliah/{mata_kuliah}
     */
    public function update(UpdateCourseRequest $request, Course $mata_kuliah)
    {
        abort_unless($this->canManageCourse($mata_kuliah), 403);

        $mata_kuliah->update($request->validated());

        return redirect()
            ->route($this->routePrefix() . '.index')
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * DELETE /admin/mata-kuliah/{mata_kuliah}
     * Route hanya ada di grup admin (dijaga role:admin).
     */
    public function destroy(Course $mata_kuliah)
    {
        $mata_kuliah->delete();

        return redirect()
            ->route($this->routePrefix() . '.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }
}