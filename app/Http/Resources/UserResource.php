<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Daftar putih. Sengaja TIDAK memuat password, remember_token,
     * email_verified_at, dsb. Kolom baru di tabel users tidak otomatis bocor.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email,
            'role'  => $this->role,
            'nim_nip' => $this->nim_nip,
        ];
    }
}