<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class SubmissionResource extends JsonResource
{
    /**
     * Daftar putih. file_path TIDAK dikeluarkan (lokasi internal storage).
     * submitted_at memakai Carbon::parse supaya aman walau model belum
     * punya cast datetime untuk kolom itu.
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'assignment_id' => $this->assignment_id,
            'student'       => new UserResource($this->whenLoaded('user')),
            'original_name' => $this->original_name,
            'file_size'     => (int) $this->file_size,
            'note'          => $this->note,
            'submitted_at'  => $this->submitted_at
                                ? Carbon::parse($this->submitted_at)->toIso8601String()
                                : null,
            'is_late'       => (bool) $this->is_late,
            'grade'         => $this->whenLoaded(
                'grade',
                fn () => $this->grade ? new GradeResource($this->grade) : null
            ),
        ];
    }
}