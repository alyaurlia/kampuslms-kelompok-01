<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Course;
use App\Models\Material;

class MaterialController extends Controller
{
    /**
     * Prefix peran untuk nama route: 'dosen' atau 'mahasiswa'.
     * Cocok dengan ->name('dosen.') / ->name('mahasiswa.') di routes/web.php.
     */
    private function role(): string
    {
        return auth()->user()->role;
    }

    /**
     * Metadata saja dulu; unggah berkas baru dikerjakan di minggu 9.
     * Materi bertipe 'file' tidak boleh membawa external_url.
     */
    private function cleanData(array $data): array
    {
        if ($data['type'] === 'file') {
            $data['external_url'] = null;
        }

        return $data;
    }

    /**
     * GET /{peran}/mata-kuliah/{course}/materi
     */
    public function index(Course $course)
    {
        // SEMENTARA: diganti Gate::authorize('view', ...) di minggu 7.
        abort_unless($this->canViewCourse($course), 403);

        $materials = $course->materials()
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('materials.index', [
            'course'    => $course,
            'materials' => $materials,
            'role'      => $this->role(),
        ]);
    }

    /**
     * GET /dosen/mata-kuliah/{course}/materi/create
     */
    public function create(Course $course)
    {
        abort_unless($this->canManageCourse($course), 403);

        return view('materials.create', [
            'course' => $course,
            'role'   => $this->role(),
        ]);
    }

    /**
     * POST /dosen/mata-kuliah/{course}/materi
     */
    public function store(StoreMaterialRequest $request, Course $course)
    {
        abort_unless($this->canManageCourse($course), 403);

        // uploaded_by diisi dari server, BUKAN dari input form.
        // Memakai save() lewat relasi, jadi course_id ikut terisi
        // tanpa perlu masuk $fillable.
        $material = new Material($this->cleanData($request->validated()));
        $material->uploaded_by = auth()->id();
        $course->materials()->save($material);

        return redirect()
            ->route($this->role() . '.mata-kuliah.materi.index', $course)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * GET /{peran}/materi/{material}   (shallow: tanpa {course})
     */
    public function show(Material $material)
    {
        abort_unless($this->canViewCourse($material->course), 403);

        return view('materials.show', [
            'material' => $material,
            'course'   => $material->course,
            'role'     => $this->role(),
        ]);
    }

    /**
     * GET /dosen/materi/{material}/edit
     */
    public function edit(Material $material)
    {
        abort_unless($this->canManageCourse($material->course), 403);

        return view('materials.edit', [
            'material' => $material,
            'course'   => $material->course,
            'role'     => $this->role(),
        ]);
    }

    /**
     * PUT/PATCH /dosen/materi/{material}
     */
    public function update(UpdateMaterialRequest $request, Material $material)
    {
        abort_unless($this->canManageCourse($material->course), 403);

        $material->update($this->cleanData($request->validated()));

        return redirect()
            ->route($this->role() . '.materi.show', $material)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * DELETE /dosen/materi/{material}
     */
    public function destroy(Material $material)
    {
        abort_unless($this->canManageCourse($material->course), 403);

        $course = $material->course;

        // TODO minggu 9: hapus juga berkas fisiknya lewat Storage::delete().
        $material->delete();

        return redirect()
            ->route($this->role() . '.mata-kuliah.materi.index', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }
}