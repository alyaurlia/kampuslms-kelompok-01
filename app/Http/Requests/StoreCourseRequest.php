<?php

namespace App\Http\Requests;

use App\Models\Course;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::forUser($this->user())->allows('create', Course::class);
    }

    /**
     * Rapikan input sebelum divalidasi: kode dibuat huruf besar dan di-trim
     * supaya "if101" dan "IF101" tidak lolos sebagai dua kode berbeda
     * (unique di SQLite membedakan huruf besar-kecil).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => is_string($this->code) ? mb_strtoupper(trim($this->code)) : $this->code,
            'name' => is_string($this->name) ? trim($this->name) : $this->name,
        ]);
    }

    public function rules(): array
    {
        return [
            'code'        => ['required', 'string', 'max:20', 'unique:courses,code'],
            'name'        => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:5000'],
            'sks'         => ['required', 'integer', 'between:1,6'],
            // Harus user ber-role dosen yang belum dihapus (soft delete).
            'lecturer_id' => [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'dosen')->whereNull('deleted_at'),
            ],
            'status'      => ['required', 'in:draft,active,archived'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'        => 'Kode mata kuliah wajib diisi.',
            'code.max'             => 'Kode mata kuliah maksimal 20 karakter.',
            'code.unique'          => 'Kode mata kuliah ini sudah dipakai, silakan gunakan kode lain.',
            'name.required'        => 'Nama mata kuliah wajib diisi.',
            'name.max'             => 'Nama mata kuliah maksimal 150 karakter.',
            'description.max'      => 'Deskripsi maksimal 5000 karakter.',
            'sks.required'         => 'SKS wajib diisi.',
            'sks.integer'          => 'SKS harus berupa angka bulat.',
            'sks.between'          => 'SKS harus antara 1 sampai 6.',
            'lecturer_id.required' => 'Dosen pengampu wajib dipilih.',
            'lecturer_id.exists'   => 'Dosen yang dipilih tidak ditemukan di sistem.',
            'status.required'      => 'Status mata kuliah wajib dipilih.',
            'status.in'            => 'Status harus salah satu dari: draft, active, atau archived.',
        ];
    }
}