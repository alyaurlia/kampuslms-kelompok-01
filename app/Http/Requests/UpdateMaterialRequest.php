<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Berkas wajib hanya bila tipe "file" dan materi ini belum punya berkas.
        $material = $this->route('material');

        return [
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'type'         => ['required', Rule::in(['file', 'link'])],
            'external_url' => ['required_if:type,link', 'nullable', 'url', 'max:255'],
            'file'         => [
                Rule::requiredIf(fn () => $this->input('type') === 'file' && empty($material?->file_path)),
                'nullable', 'file',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,txt,jpg,jpeg,png',
                'max:10240',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'title'        => 'judul',
            'description'  => 'deskripsi',
            'type'         => 'tipe materi',
            'external_url' => 'tautan',
            'file'         => 'berkas',
        ];
    }
}