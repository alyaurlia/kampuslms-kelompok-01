<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class CourseEnrollmentController extends Controller
{
    /**
     * POST /admin/mata-kuliah/{mata_kuliah}/mahasiswa
     * Memasukkan satu atau banyak mahasiswa ke mata kuliah.
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

        // syncWithoutDetaching: yang sudah terdaftar tidak diduplikasi
        // dan yang lama tidak dihapus.
        $result = $mata_kuliah->students()->syncWithoutDetaching(
            collect($data['student_ids'])
                ->mapWithKeys(fn ($id) => [(int) $id => ['enrolled_at' => now()]])
                ->all()
        );

        $added = count($result['attached']);

        return redirect()
            ->route('admin.mata-kuliah.show', $mata_kuliah)
            ->with('success', $added > 0
                ? "{$added} mahasiswa berhasil ditambahkan ke mata kuliah."
                : 'Mahasiswa yang dipilih sudah terdaftar pada mata kuliah ini.');
    }

    /**
     * DELETE /admin/mata-kuliah/{mata_kuliah}/mahasiswa/{student}
     * Mengeluarkan satu mahasiswa dari mata kuliah.
     */
    public function destroy(Course $mata_kuliah, int $student)
    {
        Gate::authorize('manageEnrollment', $mata_kuliah);

        // detach() mengembalikan jumlah baris pivot yang terhapus.
        $removed = $mata_kuliah->students()->detach($student);

        return redirect()
            ->route('admin.mata-kuliah.show', $mata_kuliah)
            ->with(
                $removed > 0 ? 'success' : 'error',
                $removed > 0
                    ? 'Mahasiswa dikeluarkan dari mata kuliah.'
                    : 'Mahasiswa tersebut tidak terdaftar pada mata kuliah ini.'
            );
    }
}