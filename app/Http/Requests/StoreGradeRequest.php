<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGradeRequest extends FormRequest
{
    /** Error bag dipisah supaya error hanya tampil di baris mahasiswa yang dinilai. */
    protected $errorBag = 'grade';

    public function authorize(): bool
    {
        // Otorisasi di sini => 403 dijalankan SEBELUM validasi.
        return $this->user()->can('grade', $this->route('submission'));
    }

    public function rules(): array
    {
        $max = $this->route('submission')->assignment->max_score;

        return [
            'score'        => ['required', 'numeric', 'min:0', 'max:' . $max],
            'feedback'     => ['nullable', 'string', 'max:2000'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'score.required'      => 'Nilai wajib diisi.',
            'score.numeric'       => 'Nilai harus berupa angka.',
            'score.min'           => 'Nilai tidak boleh kurang dari 0.',
            'score.max'           => 'Nilai tidak boleh melebihi nilai maksimal tugas (:max).',
            'feedback.max'        => 'Feedback maksimal 2000 karakter.',
            'is_published.boolean' => 'Status tampil nilai tidak valid.',
        ];
    }
}