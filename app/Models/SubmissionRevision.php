<?php

namespace App\Models;

use App\Support\FileSize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionRevision extends Model
{
    use HasFactory;

    public $timestamps = false; // hanya created_at, tanpa updated_at (immutable)
    const UPDATED_AT = null;

    protected $fillable = [
        'version_number',
        'file_path',
        'original_name',
        'file_size',
        'note',
        'submitted_at',
        'is_late',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'is_late' => 'boolean',
        ];
    }

    /** Ukuran berkas versi ini dalam teks yang terbaca manusia ("850 KB"). */
    public function getFileSizeHumanAttribute(): ?string
    {
        return FileSize::format($this->file_size);
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}