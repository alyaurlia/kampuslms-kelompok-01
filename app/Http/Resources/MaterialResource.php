<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    /**
     * Daftar putih. Sengaja TIDAK mengeluarkan file_path (lokasi internal
     * di storage). Untuk type=file, unduhan nanti lewat endpoint tersendiri
     * yang memeriksa hak akses.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'course_id'     => $this->course_id,
            'uploaded_by'   => $this->uploaded_by,
            'title'         => $this->title,
            'description'   => $this->description,
            'type'          => $this->type,              // 'file' | 'link'
            'original_name' => $this->original_name,
            'file_size'     => $this->file_size !== null ? (int) $this->file_size : null,
            'mime_type'     => $this->mime_type,
            'external_url'  => $this->external_url,
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}