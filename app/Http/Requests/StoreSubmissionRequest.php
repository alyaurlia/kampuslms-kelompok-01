<?php

namespace App\Http\Requests;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreSubmissionRequest extends FormRequest
{
    // Aturan lengkap (terdaftar, published, MK active, deadline/allow_late)
    // ada di AssignmentPolicy::submit, dipakai bersama oleh web dan API.
    public function authorize(): bool
    {
        $assignment = $this->route('assignment');

        return $assignment instanceof Assignment
            && Gate::allows('submit', $assignment);
    }

    public function rules(): array
    {
        return [
            // max dalam KILOBYTE: 10240 = 10 MB.
            'file' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:10240'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Berkas tugas wajib diunggah.',
            'file.file'     => 'Berkas yang diunggah tidak valid.',
            'file.mimes'    => 'Format berkas harus PDF, DOC, atau DOCX.',
            'file.max'      => 'Ukuran berkas maksimal 10 MB.',
        ];
    }
}