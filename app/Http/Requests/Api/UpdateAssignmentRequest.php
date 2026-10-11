<?php

namespace App\Http\Requests\Api;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request untuk PUT|PATCH /api/v1/assignments/{assignment} (JSON).
 *
 * Semua field 'sometimes': hanya yang dikirim yang divalidasi dan diubah,
 * sehingga PATCH parsial tidak menimpa field lain. Sengaja TIDAK ada
 * prepareForValidation() yang memaksa allow_late jadi false seperti di
 * form web (checkbox HTML tidak terkirim saat tidak dicentang; klien JSON
 * tidak punya masalah itu).
 *
 * course_id dan created_by tidak ada di sini: tugas tidak dapat dipindah
 * mata kuliah, dan pembuatnya tidak dapat diganti.
 */
class UpdateAssignmentRequest extends FormRequest
{
    // Otorisasi (403) dilakukan di controller lewat Gate::authorize('update', ...).
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['sometimes', 'required', 'string', 'max:255'],
            'instructions' => ['sometimes', 'required', 'string'],
            // Saat update, batas waktu boleh yang sudah lewat.
            'due_at'       => ['sometimes', 'required', 'date'],
            'max_score'    => ['sometimes', 'required', 'integer', 'min:1', 'max:100'],
            'allow_late'   => ['sometimes', 'boolean'],
            'status'       => ['sometimes', Rule::in(Assignment::STATUSES)],
        ];
    }
}