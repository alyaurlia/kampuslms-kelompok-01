<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // TODO: minggu 7 — ganti dengan Gate::authorize('create', User::class)
        return true;
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:admin,dosen,mahasiswa'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama wajib diisi.',
            'name.max'           => 'Nama maksimal 150 karakter.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah terdaftar, gunakan email lain.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required'      => 'Peran wajib dipilih.',
            'role.in'            => 'Peran harus salah satu dari: admin, dosen, atau mahasiswa.',
        ];
    }
}