<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CourseEnrollmentController extends Controller
{
    /**
     * POST /{admin|dosen}/mata-kuliah/{mata_kuliah}/mahasiswa
     * Memasukkan satu atau banyak mahasiswa ke mata kuliah.
     *
     * Otorisasi: CoursePolicy::manageEnrollment -> admin, atau dosen
     * pengampu MK ini. Dosen lain mendapat 403.
     */
    public function store(Request $request, Course $mata_kuliah)
    {
        Gate::authorize('manageEnrollment', $mata_kuliah);

        $data = $request->validate([
            'student_ids'   => ['required', 'array', 'min:1'],
            'student_ids.*' => [
                'integer',
                'distinct',
                // Harus user ber-role mahasiswa yang belum dihapus (soft delete).
                Rule::exists('users', 'id')->where('role', 'mahasiswa')->whereNull('deleted_at'),
            ],
        ], [
            'student_ids.required'   => 'Pilih minimal satu mahasiswa.',
            'student_ids.min'        => 'Pilih minimal satu mahasiswa.',
            'student_ids.*.exists'   => 'Ada mahasiswa yang dipilih tidak ditemukan di sistem.',
            'student_ids.*.integer'  => 'Data mahasiswa tidak valid.',
            'student_ids.*.distinct' => 'Ada mahasiswa yang dipilih lebih dari sekali.',
        ]);

        $ids = collect($data['student_ids'])->map(fn ($id) => (int) $id)->unique()->values();

        // Pisahkan yang sudah terdaftar. Hanya yang BARU yang di-attach, supaya
        // enrolled_at mahasiswa lama tidak tertimpa. (syncWithoutDetaching yang
        // diberi atribut pivot akan meng-update baris yang sudah ada.)
        $alreadyEnrolled = $mata_kuliah->students()
            ->whereIn('users.id', $ids)
            ->pluck('users.id');

        $newIds = $ids->diff($alreadyEnrolled)->values();

        if ($newIds->isNotEmpty()) {
            $mata_kuliah->students()->attach(
                $newIds->mapWithKeys(fn ($id) => [$id => ['enrolled_at' => now()]])->all()
            );
        }

        $added = $newIds->count();

        return redirect()
            ->route($this->routePrefix($request) . '.show', $mata_kuliah)
            ->with('success', $added > 0
                ? "{$added} mahasiswa berhasil ditambahkan ke mata kuliah."
                : 'Mahasiswa yang dipilih sudah terdaftar pada mata kuliah ini.');
    }

    /**
     * DELETE /{admin|dosen}/mata-kuliah/{mata_kuliah}/mahasiswa/{student}
     * Mengeluarkan satu mahasiswa dari mata kuliah.
     */
    public function destroy(Request $request, Course $mata_kuliah, int $student)
    {
        Gate::authorize('manageEnrollment', $mata_kuliah);

        // detach() mengembalikan jumlah baris pivot yang terhapus.
        $removed = $mata_kuliah->students()->detach($student);

        return redirect()
            ->route($this->routePrefix($request) . '.show', $mata_kuliah)
            ->with(
                $removed > 0 ? 'success' : 'error',
                $removed > 0
                    ? 'Mahasiswa dikeluarkan dari mata kuliah.'
                    : 'Mahasiswa tersebut tidak terdaftar pada mata kuliah ini.'
            );
    }

    /**
     * Prefix nama route sesuai peran: 'admin.mata-kuliah' atau 'dosen.mata-kuliah'.
     * Aman karena route hanya dapat dicapai oleh admin/dosen (middleware role
     * + Gate di atas), jadi role di sini tidak pernah 'mahasiswa'.
     */
    private function routePrefix(Request $request): string
    {
        return $request->user()->role . '.mata-kuliah';
    }
}