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
    /**
     * Disk PRIVAT: storage/app/private (lihat config/filesystems.php).
     * Lampiran soal hanya dapat diambil lewat download() yang memeriksa hak akses.
     */
    private const DISK = 'local';

    /** Prefix peran untuk nama route: 'admin', 'dosen', atau 'mahasiswa'. */
    private function role(): string
    {
        return auth()->user()->role;
    }

    /**
     * Simpan lampiran soal ke disk privat (storage/app/private/assignments/{course_id})
     * dan kembalikan kolom metadata-nya.
     */
    private function storeAttachment(UploadedFile $file, Course $course): array
    {
        // getMimeType() = hasil deteksi isi berkas di server (tepercaya);
        // getClientMimeType() = klaim browser, hanya cadangan.
        $meta = [
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'mime_type'     => $file->getMimeType() ?? $file->getClientMimeType(),
        ];

        return $meta + [
            'file_path' => $file->store("assignments/{$course->id}", self::DISK),
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

        // with('course'): AssignmentPolicy memanggil $assignment->course untuk
        // tiap baris (tombol edit/hapus). Tanpa ini terjadi N+1 query.
        $assignments = $course->assignments()
            ->with('course')
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

    // GET /{admin|dosen}/mata-kuliah/{course}/tugas/create
    public function create(Course $course)
    {
        Gate::authorize('create', [Assignment::class, $course]);

        return view('assignments.create', [
            'course' => $course,
            'role'   => $this->role(),
        ]);
    }

    // POST /{admin|dosen}/mata-kuliah/{course}/tugas
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

        try {
            $course->assignments()->save($assignment);
        } catch (\Throwable $e) {
            // Gagal simpan ke database: jangan tinggalkan berkas yatim di disk.
            if (! empty($data['file_path'])) {
                Storage::disk(self::DISK)->delete($data['file_path']);
            }

            throw $e;
        }

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
                ->with(['grade', 'revisions'])
                ->first()
            : null;

        // Daftar pengumpulan untuk admin/dosen. Mahasiswa TIDAK pernah memuatnya,
        // jadi pengumpulan mahasiswa lain tidak mungkin bocor ke halaman.
        // student & grade di-eager load; relasi balik (assignment, grade->submission)
        // diisi manual supaya @can('grade', $submission) di dalam loop tidak memicu N+1.
        $submissions = collect();

        if ($user->role !== 'mahasiswa') {
            $submissions = $assignment->submissions()
                ->with(['student', 'grade', 'revisions'])
                ->orderBy('submitted_at')
                ->get()
                ->each(function ($submission) use ($assignment) {
                    $submission->setRelation('assignment', $assignment);
                    // GradePolicy::update membaca $grade->submission->assignment->course.
                    $submission->grade?->setRelation('submission', $submission);
                });
        }

        return view('assignments.show', [
            'assignment'   => $assignment,
            'course'       => $assignment->course,
            'mySubmission' => $mySubmission,
            'submissions'  => $submissions,
            'role'         => $this->role(),
        ]);
    }

    // GET /{admin|dosen}/tugas/{assignment}/edit
    public function edit(Assignment $assignment)
    {
        Gate::authorize('update', $assignment);

        return view('assignments.edit', [
            'assignment' => $assignment,
            'course'     => $assignment->course,
            'role'       => $this->role(),
        ]);
    }

    // PUT/PATCH /{admin|dosen}/tugas/{assignment}
    public function update(UpdateAssignmentRequest $request, Assignment $assignment)
    {
        Gate::authorize('update', $assignment);

        $data = $request->validated();
        unset($data['attachment'], $data['remove_attachment']);

        $oldPath = $assignment->file_path;

        // Berkas baru menang atas "hapus lampiran" bila keduanya dikirim.
        if ($file = $request->file('attachment')) {
            $data += $this->storeAttachment($file, $assignment->course);
        } elseif ($request->boolean('remove_attachment')) {
            $data += $this->clearAttachment();
        }

        try {
            $assignment->update($data);
        } catch (\Throwable $e) {
            // Gagal simpan: berkas BARU yang sudah tersimpan di disk menjadi yatim.
            if (! empty($data['file_path']) && $data['file_path'] !== $oldPath) {
                Storage::disk(self::DISK)->delete($data['file_path']);
            }

            throw $e;
        }

        // Hapus berkas lama SETELAH update berhasil, dan hanya bila memang
        // sudah tidak dipakai (diganti berkas baru, atau lampiran dihapus).
        if ($oldPath && $oldPath !== $assignment->file_path) {
            Storage::disk(self::DISK)->delete($oldPath);
        }

        return redirect()
            ->route($this->role() . '.tugas.show', $assignment)
            ->with('success', 'Tugas berhasil diperbarui.');
    }

    // DELETE /{admin|dosen}/tugas/{assignment}
    public function destroy(Assignment $assignment)
    {
        Gate::authorize('delete', $assignment);

        $course = $assignment->course;

        // Berkas fisik (lampiran soal + folder submissions/{id}) dibersihkan oleh
        // Assignment::booted() SETELAH baris berhasil dihapus, supaya web dan API
        // memakai logika yang sama.
        $assignment->delete();

        return redirect()
            ->route($this->role() . '.mata-kuliah.tugas.index', $course)
            ->with('success', 'Tugas berhasil dihapus.');
    }

    /**
     * GET /tugas/{assignment}/lampiran   (nama route: tugas.lampiran)
     *
     * Satu-satunya pintu untuk mengambil lampiran soal. Aturan akses ada di
     * AssignmentPolicy::view: admin, dosen pengampu, atau mahasiswa terdaftar
     * dengan tugas published dan MK active. Lampiran tugas draft TIDAK dapat
     * diunduh mahasiswa, bahkan bila ID-nya ditebak.
     */
    public function download(Assignment $assignment)
    {
        Gate::authorize('view', $assignment);

        abort_unless($assignment->file_path, 404);

        $disk = Storage::disk(self::DISK);

        abort_unless($disk->exists($assignment->file_path), 404, 'Berkas tidak ditemukan di server.');

        return $disk->download(
            $assignment->file_path,
            $assignment->original_name ?? basename($assignment->file_path),
            ['X-Content-Type-Options' => 'nosniff']
        );
    }
}