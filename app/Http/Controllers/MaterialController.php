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
     * Disk PRIVAT: storage/app/private (lihat config/filesystems.php).
     * Berkas di sini TIDAK dapat dijangkau lewat URL, hanya lewat
     * method download() di bawah yang memeriksa hak akses.
     */
    private const DISK = 'local';

    /**
     * Prefix peran untuk nama route: 'admin', 'dosen', atau 'mahasiswa'.
     * Cocok dengan ->name('admin.') / ->name('dosen.') / ->name('mahasiswa.')
     * di routes/web.php.
     */
    private function role(): string
    {
        return auth()->user()->role;
    }

    /**
     * Rapikan data hasil validasi sebelum disimpan:
     *  - tipe 'link' : kosongkan semua kolom berkas
     *  - tipe 'file' : kosongkan external_url, simpan berkas baru bila diunggah
     *
     * Method ini TIDAK menghapus berkas lama. Penghapusan dilakukan pemanggil
     * setelah simpan ke database berhasil, supaya kegagalan di tengah jalan
     * tidak membuat materi kehilangan berkasnya.
     */
    private function prepareData(array $data, ?UploadedFile $file, Course $course): array
    {
        unset($data['file']);

        if ($data['type'] === 'link') {
            return $data + [
                'file_path'     => null,
                'original_name' => null,
                'file_size'     => null,
                'mime_type'     => null,
            ];
        }

        $data['external_url'] = null;

        if ($file) {
            // Ambil metadata SEBELUM store().
            // getMimeType() = hasil deteksi isi berkas di server (tepercaya),
            // getClientMimeType() = klaim browser (bisa dipalsukan), hanya cadangan.
            $meta = [
                'original_name' => $file->getClientOriginalName(),
                'file_size'     => $file->getSize(),
                'mime_type'     => $file->getMimeType() ?? $file->getClientMimeType(),
            ];

            // store() memberi nama acak, jadi nama asli tidak dipakai di disk.
            $data['file_path'] = $file->store("materials/{$course->id}", self::DISK);
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

        // with('course'): MaterialPolicy memanggil $material->course untuk tiap
        // baris (tombol edit/hapus). Tanpa ini terjadi N+1 query.
        $materials = $course->materials()
            ->with('course')
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
     * GET /{admin|dosen}/mata-kuliah/{course}/materi/create
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
     * POST /{admin|dosen}/mata-kuliah/{course}/materi
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

        try {
            $course->materials()->save($material);
        } catch (\Throwable $e) {
            // Gagal simpan ke database: jangan tinggalkan berkas yatim di disk.
            if (! empty($data['file_path'])) {
                Storage::disk(self::DISK)->delete($data['file_path']);
            }

            throw $e;
        }

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
     * GET /{admin|dosen}/materi/{material}/edit
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
     * PUT/PATCH /{admin|dosen}/materi/{material}
     */
    public function update(UpdateMaterialRequest $request, Material $material)
    {
        Gate::authorize('update', $material);

        $oldPath = $material->file_path;

        $material->update(
            $this->prepareData($request->validated(), $request->file('file'), $material->course)
        );

        // Hapus berkas lama SETELAH update berhasil, dan hanya bila memang
        // sudah tidak dipakai (diganti berkas baru, atau materi jadi tipe link).
        if ($oldPath && $oldPath !== $material->file_path) {
            Storage::disk(self::DISK)->delete($oldPath);
        }

        return redirect()
            ->route($this->role() . '.materi.show', $material)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * DELETE /{admin|dosen}/materi/{material}
     */
    public function destroy(Material $material)
    {
        Gate::authorize('delete', $material);

        $course = $material->course;

        if ($material->file_path) {
            Storage::disk(self::DISK)->delete($material->file_path);
        }

        $material->delete();

        return redirect()
            ->route($this->role() . '.mata-kuliah.materi.index', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }

    /**
     * GET /materi/{material}/unduh   (nama route: materi.unduh)
     *
     * Satu-satunya pintu untuk mengambil berkas materi. Aturan akses ada di
     * MaterialPolicy::view: admin, dosen pengampu, atau mahasiswa yang
     * terdaftar di MK itu dan MK-nya berstatus active.
     * Pengguna lain mendapat 403 walau menebak ID materi.
     */
    public function download(Material $material)
    {
        Gate::authorize('view', $material);

        abort_unless($material->type === 'file' && $material->file_path, 404);

        $disk = Storage::disk(self::DISK);

        abort_unless($disk->exists($material->file_path), 404, 'Berkas tidak ditemukan di server.');

        // download() = selalu sebagai lampiran (attachment), tidak dirender
        // di browser; nosniff mencegah browser menebak ulang tipe berkas.
        return $disk->download(
            $material->file_path,
            $material->original_name ?? basename($material->file_path),
            ['X-Content-Type-Options' => 'nosniff']
        );
    }
}