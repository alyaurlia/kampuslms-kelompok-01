<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Facades\Storage;

class Assignment extends Model
{
    use HasFactory;

    public const STATUSES = ['draft', 'published'];

    protected $fillable = [
        'course_id',
        'created_by',
        'title',
        'instructions',
        'file_path',
        'original_name',
        'file_size',
        'mime_type',
        'due_at',
        'max_score',
        'allow_late',
        'status',
    ];

    protected $attributes = [
        'status'     => 'draft',
        'max_score'  => 100,
        'allow_late' => true,
    ];

    protected function casts(): array
    {
        return [
            'due_at'     => 'datetime',
            'allow_late' => 'boolean',
            'max_score'  => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function grades(): HasManyThrough
    {
        return $this->hasManyThrough(Grade::class, Submission::class);
    }

    /** URL lampiran soal (null kalau dosen tidak melampirkan berkas). */
    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->file_path
            ? Storage::disk('public')->url($this->file_path)
            : null;
    }

    /** Mahasiswa tidak melihat tugas draft; dosen melihat semuanya. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->role === 'dosen'
            ? $query
            : $query->where('status', '!=', 'draft');
    }
}