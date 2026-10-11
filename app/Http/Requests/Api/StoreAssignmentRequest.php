<?php

namespace App\Http\Requests\Api;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Request untuk POST /api/v1/assignments (JSON).
 *
 * Terpisah dari App\Http\Requests\StoreAssignmentRequest (milik form web):
 *  - API menerima course_id di body; form web mengambilnya dari URL.
 *  - Field opsional memakai 'sometimes' supaya default model tetap berlaku
 *    (status=draft, max_score=100, allow_late=true) ketika tidak dikirim.
 *  - Lampiran soal hanya lewat web, jadi tidak ada 'attachment' di sini.
 */
class StoreAssignmentRequest extends FormRequest
{
    // Otorisasi (403) dilakukan di controller lewat Gate::authorize('create', ...).
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'course_id'    => ['required', 'integer', 'exists:courses,id'],
            'title'        => ['required', 'string', 'max:255'],
            'instructions' => ['required', 'string'],
            'due_at'       => ['required', 'date', 'after:now'],
            'max_score'    => ['sometimes', 'integer', 'min:1', 'max:100'],
            'allow_late'   => ['sometimes', 'boolean'],
            'status'       => ['sometimes', Rule::in(Assignment::STATUSES)],
        ];
    }
}