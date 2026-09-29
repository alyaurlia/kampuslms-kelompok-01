<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO minggu 7: ganti dengan pengecekan Policy.
        // Sementara, hak akses dijaga abort_unless di MaterialController.
        return true;
    }

    public function rules(): array
    {
        return [
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'type'         => ['required', 'in:file,link'],
            // url:http,https menolak skema berbahaya seperti javascript:
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