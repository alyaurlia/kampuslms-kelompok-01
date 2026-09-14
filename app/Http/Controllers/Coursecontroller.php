<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * GET /mata-kuliah
     */
    public function index()
    {
        $mataKuliah = Course::all();

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'       => 'required|string|max:20|unique:courses,code',
            'name'       => 'required|string|max:255',
            'sks'        => 'required|integer|min:1|max:6',
            'lecturer_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
        ]);

        Course::create($validated);

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
    public function update(Request $request, Course $mata_kuliah)
    {
        $validated = $request->validate([
            'code'       => 'required|string|max:20|unique:courses,code,' . $mata_kuliah->id,
            'name'       => 'required|string|max:255',
            'sks'        => 'required|integer|min:1|max:6',
            'lecturer_id' => 'required|exists:users,id',
            'description' => 'nullable|string',
        ]);

        $mata_kuliah->update($validated);

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