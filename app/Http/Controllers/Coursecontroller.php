<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
     * Daftar dosen untuk dropdown "Dosen Pengampu" di form create/edit.
     */
    private function lecturers()
    {
        return User::query()
            ->where('role', 'dosen')
            ->orderBy('name')
            ->get(['id', 'name', 'nim_nip']);
    }

    /**
     * GET /{peran}/mata-kuliah
     * Daftar disaring di level query sesuai peran (scope visibleTo).
     */
    public function index(Request $request)
    {
        Gate::authorize('viewAny', Course::class);

        $mataKuliah = Course::query()
            ->visibleTo($request->user())
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
     */
    public function create(Request $request)
    {
        Gate::authorize('create', Course::class);

        return view('courses.create', [
            'routePrefix' => $this->routePrefix($request),
            'lecturers'   => $this->lecturers(),
        ]);
    }

    /**
     * POST /admin/mata-kuliah
     */
    public function store(StoreCourseRequest $request)
    {
        Gate::authorize('create', Course::class);

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
        Gate::authorize('view', $mata_kuliah);

        return view('courses.show', [
            'mataKuliah'  => $mata_kuliah,
            'routePrefix' => $this->routePrefix($request),
        ]);
    }

    /**
     * GET /admin/mata-kuliah/{mata_kuliah}/edit
     */
    public function edit(Request $request, Course $mata_kuliah)
    {
        Gate::authorize('update', $mata_kuliah);

        return view('courses.edit', [
            'mataKuliah'  => $mata_kuliah,
            'routePrefix' => $this->routePrefix($request),
            'lecturers'   => $this->lecturers(),
        ]);
    }

    /**
     * PUT/PATCH /admin/mata-kuliah/{mata_kuliah}
     */
    public function update(UpdateCourseRequest $request, Course $mata_kuliah)
    {
        Gate::authorize('update', $mata_kuliah);

        $mata_kuliah->update($request->validated());

        return redirect()
            ->route($this->routePrefix($request) . '.index')
            ->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * DELETE /admin/mata-kuliah/{mata_kuliah}
     */
    public function destroy(Request $request, Course $mata_kuliah)
    {
        Gate::authorize('delete', $mata_kuliah);

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