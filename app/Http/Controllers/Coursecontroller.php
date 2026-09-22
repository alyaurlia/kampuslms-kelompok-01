<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;

class CourseController extends Controller
{
    /**
     * GET /mata-kuliah
     */
    public function index(Request $request)
    {
        $mataKuliah = Course::query()
            ->with('lecturer')
            ->when($request->filled('q'), fn ($query) =>
                $query->where('name', 'like', '%' . $request->q . '%')
                      ->orWhere('code', 'like', '%' . $request->q . '%'))
            ->when($request->filled('status'), fn ($query) =>
                $query->where('status', $request->status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('courses.index', compact('mataKuliah'));
    }
    
    /**
     * GET /mata-kuliah/create
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * POST /mata-kuliah
     */
    public function store(StoreCourseRequest $request)
{
    Course::create($request->validated());

    return redirect()
        ->route('mata-kuliah.index')
        ->with('success', 'Mata kuliah berhasil ditambahkan.');
}

    /**
     * GET /mata-kuliah/{mata_kuliah}
     * Nama parameter HARUS "mata_kuliah" (bukan "course"), karena
     * Route::resource('mata-kuliah', ...) menghasilkan wildcard
     * {mata_kuliah} — tanda hubung otomatis diubah jadi underscore
     * oleh Laravel. Kalau nama variabel tidak cocok, route model
     * binding tidak akan resolve model-nya secara otomatis.
     */
    public function show(Course $mata_kuliah)
    {
        return view('courses.show', ['mataKuliah' => $mata_kuliah]);
    }

    /**
     * GET /mata-kuliah/{mata_kuliah}/edit
     */
    public function edit(Course $mata_kuliah)
    {
        return view('courses.edit', ['mataKuliah' => $mata_kuliah]);
    }

    /**
     * PUT/PATCH /mata-kuliah/{mata_kuliah}
     */
    public function update(UpdateCourseRequest $request, Course $mata_kuliah)
{
    $mata_kuliah->update($request->validated());

    return redirect()
        ->route('mata-kuliah.index')
        ->with('success', 'Mata kuliah berhasil diperbarui.');
}

    /**
     * DELETE /mata-kuliah/{mata_kuliah}
     */
    public function destroy(Course $mata_kuliah)
    {
        $mata_kuliah->delete();

        return redirect()
            ->route('mata-kuliah.index')
            ->with('success', 'Mata kuliah berhasil dihapus.');
    }

    public function tentang()
    {
        return view('tentang');
    }
}