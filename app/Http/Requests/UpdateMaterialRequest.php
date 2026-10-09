<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;


class UpdateMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::forUser($this->user())
            ->allows('update', $this->route('material'));
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'type'         => ['required', 'in:file,link'],
            'external_url' => ['required_if:type,link', 'nullable', 'url:http,https', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'        => 'Judul materi wajib diisi.',
            'type.in'               => 'Tipe materi harus berkas atau tautan.',
            'external_url.required_if' => 'Alamat tautan wajib diisi untuk materi bertipe tautan.',
            'external_url.url'      => 'Alamat tautan harus diawali http:// atau https://.',
        ];
    }
}