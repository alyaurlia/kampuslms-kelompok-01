<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

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
     * Rapikan data hasil validasi sebelum disimpan:
     *  - tipe 'link' : buang berkas lama (kalau ada), kosongkan kolom berkas
     *  - tipe 'file' : kosongkan external_url, simpan berkas baru bila diunggah
     *
     * Berkas disimpan di disk 'public' (storage/app/public/materials/{course_id}).
     * Jalankan sekali: php artisan storage:link
     */
    private function prepareData(array $data, ?UploadedFile $file, Course $course, ?Material $existing = null): array
    {
        unset($data['file']);

        if ($data['type'] === 'link') {
            if ($existing?->file_path) {
                Storage::disk('public')->delete($existing->file_path);
            }

            return $data + [
                'file_path'     => null,
                'original_name' => null,
                'file_size'     => null,
                'mime_type'     => null,
            ];
        }

        $data['external_url'] = null;

        if ($file) {
            // Ambil metadata SEBELUM store(), lalu hapus berkas lama.
            $meta = [
                'original_name' => $file->getClientOriginalName(),
                'file_size'     => $file->getSize(),
                'mime_type'     => $file->getClientMimeType(),
            ];

            if ($existing?->file_path) {
                Storage::disk('public')->delete($existing->file_path);
            }

            $data['file_path'] = $file->store("materials/{$course->id}", 'public');
            $data = array_merge($data, $meta);
        }

        return $data;
    }

    /**
     * GET /{peran}/mata-kuliah/{course}/materi
     */
    public function index(Course $course)
    {
        Gate::authorize('view', $course);

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
        Gate::authorize('create', [Material::class, $course]);

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
        Gate::authorize('create', [Material::class, $course]);

        $data = $this->prepareData($request->validated(), $request->file('file'), $course);

        // uploaded_by diisi dari server, BUKAN dari input form.
        // Memakai save() lewat relasi, jadi course_id ikut terisi
        // tanpa perlu masuk $fillable.
        $material = new Material($data);
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
        Gate::authorize('view', $material);

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
        Gate::authorize('update', $material);

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
        Gate::authorize('update', $material);

        $material->update(
            $this->prepareData($request->validated(), $request->file('file'), $material->course, $material)
        );

        return redirect()
            ->route($this->role() . '.materi.show', $material)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * DELETE /dosen/materi/{material}
     */
    public function destroy(Material $material)
    {
        Gate::authorize('delete', $material);

        $course = $material->course;

        if ($material->file_path) {
            Storage::disk('public')->delete($material->file_path);
        }

        $material->delete();

        return redirect()
            ->route($this->role() . '.mata-kuliah.materi.index', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }
}