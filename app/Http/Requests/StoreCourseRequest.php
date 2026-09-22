<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: minggu 7 — ganti dengan Gate::authorize('create', Course::class)
        return true;
    }

    public function rules(): array
    {
        return [
            'code'        => ['required', 'string', 'max:20', 'unique:courses,code'],
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sks'         => ['required', 'integer', 'between:1,6'],
            'lecturer_id' => ['required', 'integer', 'exists:users,id'],
            'status'      => ['required', 'in:draft,active,archived'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'        => 'Kode mata kuliah wajib diisi.',
            'code.max'              => 'Kode mata kuliah maksimal 20 karakter.',
            'code.unique'           => 'Kode mata kuliah ini sudah dipakai, silakan gunakan kode lain.',
            'name.required'         => 'Nama mata kuliah wajib diisi.',
            'name.max'              => 'Nama mata kuliah maksimal 150 karakter.',
            'sks.required'          => 'SKS wajib diisi.',
            'sks.integer'           => 'SKS harus berupa angka bulat.',
            'sks.between'           => 'SKS harus antara 1 sampai 6.',
            'lecturer_id.required'  => 'Dosen pengampu wajib dipilih.',
            'lecturer_id.exists'    => 'Dosen yang dipilih tidak ditemukan di sistem.',
            'status.required'       => 'Status mata kuliah wajib dipilih.',
            'status.in'             => 'Status harus salah satu dari: draft, active, atau archived.',
        ];
    }
}