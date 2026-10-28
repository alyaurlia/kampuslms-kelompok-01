<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // endpoint publik: siapa pun boleh mencoba login
    }

    public function rules(): array
    {
        // Klien boleh mengirim "login" (NIM/NIP atau email) atau "email" (kompatibel dengan klien lama).
        return [
            'login'       => ['required_without:email', 'nullable', 'string'],
            'email'       => ['required_without:login', 'nullable', 'string'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required_without' => 'NIM/NIP atau email wajib diisi.',
            'email.required_without' => 'NIM/NIP atau email wajib diisi.',
            'password.required'      => 'Kata sandi wajib diisi.',
        ];
    }
}