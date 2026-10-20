<?php

namespace App\Models;

use App\Support\FileSize;
use Carbon\CarbonInterface;
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

    /**
     * Bersihkan berkas fisik saat tugas dihapus: lampiran soal + seluruh folder
     * submissions/{id}, yang juga memuat berkas semua revisi.
     *
     * Baris submissions dan submission_revisions terhapus oleh cascade di DATABASE,
     * yang tidak memicu event model, jadi pembersihannya dilakukan di sini.
     * Ditulis di model (bukan di controller) supaya web, API, dan tinker berperilaku
     * sama. `deleted` hanya terpicu bila baris BERHASIL dihapus; kalau penghapusan
     * di database gagal, berkasnya tetap utuh.
     */
    protected static function booted(): void
    {
        static::deleted(function (Assignment $assignment) {
            $disk = Storage::disk('local');

            if ($assignment->file_path) {
                $disk->delete($assignment->file_path);
            }

            $disk->deleteDirectory("submissions/{$assignment->id}");
        });
    }

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

    /**
     * Apakah tugas ini menerima pengumpulan pada waktu $at?
     * Ya bila tanpa deadline, belum lewat deadline, atau keterlambatan diizinkan.
     * Satu-satunya tempat aturan ini ditulis; dipakai AssignmentPolicy::submit
     * sehingga web dan API otomatis sama.
     */
    public function acceptsSubmissionsAt(CarbonInterface $at): bool
    {
        return $this->due_at === null
            || $at->lte($this->due_at)
            || $this->allow_late;
    }

    /** Apakah pengumpulan pada waktu $at ditandai terlambat (is_late)? */
    public function isLateAt(CarbonInterface $at): bool
    {
        return $this->due_at !== null && $at->gt($this->due_at);
    }

    /**
     * Tautan unduh lampiran soal (null kalau dosen tidak melampirkan berkas).
     * Berkas disimpan di disk privat, jadi TIDAK ada URL langsung ke berkasnya:
     * tautan ini mengarah ke route tugas.lampiran yang memeriksa hak akses.
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->file_path
            ? route('tugas.lampiran', $this)
            : null;
    }

    /**
     * Ukuran lampiran soal dalam teks yang terbaca manusia ("1,5 MB").
     * null bila tugas tidak punya lampiran.
     */
    public function getFileSizeHumanAttribute(): ?string
    {
        return FileSize::format($this->file_size);
    }

    /** Mahasiswa tidak melihat tugas draft; admin dan dosen melihat semuanya. */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return in_array($user->role, ['admin', 'dosen'], true)
            ? $query
            : $query->where('status', '!=', 'draft');
    }
}