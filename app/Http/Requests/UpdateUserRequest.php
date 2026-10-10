<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang boleh mengubah data pengguna.
        // Lapisan kedua setelah middleware role:admin pada route.
        return $this->user()?->role === 'admin';
    }

    /**
     * Rapikan input sebelum divalidasi (sama seperti StoreUserRequest).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'    => is_string($this->name) ? trim($this->name) : $this->name,
            'email'   => is_string($this->email) ? mb_strtolower(trim($this->email)) : $this->email,
            'nim_nip' => is_string($this->nim_nip) ? trim($this->nim_nip) : $this->nim_nip,
        ]);
    }

    public function rules(): array
    {
        $target = $this->route('user');

        // Admin tidak boleh menurunkan peran akunnya sendiri,
        // supaya tidak terkunci dari halaman admin.
        $roleRule = $target->is($this->user())
            ? Rule::in(['admin'])
            : Rule::in(['admin', 'dosen', 'mahasiswa']);

        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($target->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'], // nullable — tidak wajib diisi ulang saat edit
            'role'     => ['required', $roleRule],
            'nim_nip'  => ['required', 'string', 'max:30', Rule::unique('users', 'nim_nip')->ignore($target->id)],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama wajib diisi.',
            'name.string'        => 'Nama harus berupa teks.',
            'name.max'           => 'Nama maksimal 150 karakter.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.max'          => 'Email maksimal 150 karakter.',
            'email.unique'       => 'Email ini sudah terdaftar.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required'      => 'Peran wajib dipilih.',
            'role.in'            => 'Peran tidak valid, atau Anda tidak dapat mengubah peran akun Anda sendiri.',
            'nim_nip.required'   => 'NIM/NIP wajib diisi.',
            'nim_nip.string'     => 'NIM/NIP harus berupa teks.',
            'nim_nip.max'        => 'NIM/NIP maksimal 30 karakter.',
            'nim_nip.unique'     => 'NIM/NIP ini sudah terdaftar.',
        ];
    }
}