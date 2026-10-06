<?php

namespace App\Http\Requests\Api;

use App\Models\Assignment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        $assignment = $this->route('assignment');

        return $assignment instanceof Assignment
            && Gate::allows('submit', $assignment);
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Berkas tugas wajib diunggah.',
            'file.file'     => 'Berkas yang diunggah tidak valid.',
            'file.max'      => 'Ukuran berkas maksimal 10 MB.',
        ];
    }
}
