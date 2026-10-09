<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Hanya admin yang boleh mendaftarkan pengguna baru.
        // Lapisan kedua setelah middleware role:admin pada route.
        return $this->user()?->role === 'admin';
    }

    /**
     * Rapikan input sebelum divalidasi: email dibuat huruf kecil agar
     * "Budi@kampus.ac.id" dan "budi@kampus.ac.id" tidak lolos sebagai
     * dua akun berbeda (unique di SQLite membedakan huruf besar-kecil).
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'  => is_string($this->name) ? trim($this->name) : $this->name,
            'email' => is_string($this->email) ? mb_strtolower(trim($this->email)) : $this->email,
            'nim_nip' => is_string($this->nim_nip) ? trim($this->nim_nip) : $this->nim_nip,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:150'],
            'email'    => ['required', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'in:admin,dosen,mahasiswa'],
            // Wajib untuk dosen & mahasiswa; admin boleh kosong.
            'nim_nip'  => ['required_if:role,dosen,mahasiswa', 'nullable', 'string', 'max:30', 'unique:users,nim_nip'],
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
            'email.unique'       => 'Email ini sudah terdaftar, gunakan email lain.',
            'password.required'  => 'Kata sandi wajib diisi.',
            'password.min'       => 'Kata sandi minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
            'role.required'      => 'Peran wajib dipilih.',
            'role.in'            => 'Peran harus salah satu dari: admin, dosen, atau mahasiswa.',
            'nim_nip.required_if' => 'NIM/NIP wajib diisi untuk dosen dan mahasiswa.',
            'nim_nip.string'     => 'NIM/NIP harus berupa teks.',
            'nim_nip.max'        => 'NIM/NIP maksimal 30 karakter.',
            'nim_nip.unique'     => 'NIM/NIP ini sudah terdaftar.',
        ];
    }
}