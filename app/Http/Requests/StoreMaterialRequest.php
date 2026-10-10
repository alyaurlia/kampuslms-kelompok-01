<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMaterialRequest extends FormRequest
{
    // Otorisasi dilakukan di controller lewat Gate::authorize().
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'type'         => ['required', Rule::in(['file', 'link'])],
            'external_url' => ['required_if:type,link', 'nullable', 'url', 'max:255'],
            'file'         => [
                'required_if:type,file', 'nullable', 'file',
                'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,zip,txt,jpg,jpeg,png',
                'max:10240', // 10 MB
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