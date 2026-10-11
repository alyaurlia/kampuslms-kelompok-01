<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'external_url',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Tautan untuk pengguna:
     *  - type=link : tautan luar apa adanya
     *  - type=file : route unduh (materi.unduh) yang memeriksa hak akses.
     *                Berkas disimpan di disk privat, jadi TIDAK ada URL
     *                langsung ke berkasnya.
     */
    public function getUrlAttribute(): ?string
    {
        if ($this->type === 'link') {
            return $this->external_url;
        }

        return $this->file_path
            ? route('materi.unduh', $this)
            : null;
    }
}