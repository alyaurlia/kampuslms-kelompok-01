<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Daftar putih. Kolom mengikuti skema courses di modul:
     * id, code, name, description, sks, lecturer_id, status.
     * SESUAIKAN dengan kontrak Bagian 5 kalau berbeda.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'code'        => $this->code,
            'name'        => $this->name,
            'description' => $this->description,
            'sks'         => $this->sks,
            'status'      => $this->status,
            'lecturer'    => new UserResource($this->whenLoaded('lecturer')),
            'counts'      => [
                'materials'   => $this->whenCounted('materials'),
                'assignments' => $this->whenCounted('assignments'),
            ],
            'created_at'  => $this->created_at?->toIso8601String(),
        ];
    }
}