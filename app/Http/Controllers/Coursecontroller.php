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
    private function routePrefix(Request $request): string
    {
        return Str::beforeLast($request->route()->getName(), '.');
    }

    /**
     * GET /{peran}/mata-kuliah
     * TODO minggu 7: saring daftar per peran di level query.
     */
    public function index(Request $request)
    {
        $mataKuliah = Course::query()
            ->with('lecturer')
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
            'routePrefix' => $this->routePrefix($request),
        ]);
    }

    /**
     * GET /admin/mata-kuliah/create
     * Route hanya ada di grup admin (dijaga role:admin).
     */
    public function create(Request $request)
    {
        return view('courses.create', [
            'routePrefix' => $this->routePrefix($request),
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
            ->route($this->routePrefix($request) . '.index')
            ->with('success', 'Mata kuliah berhasil ditambahkan.');
    }

    /**
     * GET /{peran}/mata-kuliah/{mata_kuliah}
     * Nama parameter HARUS "mata_kuliah": Route::resource('mata-kuliah', ...)
     * menghasilkan wildcard {mata_kuliah}. Kalau namanya tidak cocok,
     * route model binding tidak akan resolve modelnya.
     */
    public function show(Request $request, Course $mata_kuliah)
    {
        // SEMENTARA: diganti Gate::authorize('view', ...) di minggu 7.
        abort_unless($this->canViewCourse($mata_kuliah), 403);

        return view('courses.show', [
            'mataKuliah'  => $mata_kuliah,
            'routePrefix' => $this->routePrefix($request),
        ]);
    }

    /**
     * GET /{admin|dosen}/mata-kuliah/{mata_kuliah}/edit
     */
    public function edit(Request $request, Course $mata_kuliah)
    {
        // SEMENTARA: diganti Gate::authorize('update', ...) di minggu 7.
        abort_unless($this->canManageCourse($mata_kuliah), 403);

        return view('courses.edit', [
            'mataKuliah'  => $mata_kuliah,
            'routePrefix' => $this->routePrefix($request),
        ]);
    }

    /**
     * PUT/PATCH /{admin|dosen}/mata-kuliah/{mata_kuliah}
     */
    public function update(UpdateCourseRequest $request, Course $mata_kuliah)
    {
        // SEMENTARA: diganti Gate::authorize('update', ...) di minggu 7.
        abort_unless($this->canManageCourse($mata_kuliah), 403);

        $mata_kuliah->update($request->validated());

        return redirect()
            ->route($this->routePrefix($request) . '.index')
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * DELETE /admin/mata-kuliah/{mata_kuliah}
     * Route hanya ada di grup admin (dijaga role:admin).
     */
    public function destroy(Request $request, Course $mata_kuliah)
    {
        $mata_kuliah->delete();

        return redirect()
            ->route($this->routePrefix($request) . '.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }

    public function tentang()
    {
        return view('tentang');
    }
}