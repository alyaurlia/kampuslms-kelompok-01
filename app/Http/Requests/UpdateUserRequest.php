<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: minggu 7 — ganti dengan Gate::authorize('update', $this->route('user'))
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($userId)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // nullable — password tidak wajib diisi ulang saat edit
            'role'     => ['required', 'in:admin,dosen,mahasiswa'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Email ini sudah terdaftar.',
            'role.in'      => 'Peran tidak valid.',
        ];
    }
}