<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class GradeResource extends JsonResource
{
    /**
     * score di database decimal(5,2) dan keluar dari Eloquent sebagai
     * string ("85.50"); di sini dijadikan angka (float).
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'submission_id' => $this->submission_id,
            'score'         => (float) $this->score,
            'feedback'      => $this->feedback,
            'graded_by'     => $this->graded_by,
            'graded_at'     => $this->graded_at
                                ? Carbon::parse($this->graded_at)->toIso8601String()
                                : null,
        ];
    }
}