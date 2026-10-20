<?php

namespace App\Models;

use App\Support\FileSize;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Submission extends Model
{
    use HasFactory;

    /**
     * Path berkas yang dihapus setelah baris terhapus. Diisi saat event `deleting`,
     * karena saat `deleted` baris revisinya sudah hilang oleh cascade di database.
     */
    protected array $filesToDelete = [];

    protected $fillable = [
        'assignment_id',
        'user_id',
        'file_path',
        'original_name',
        'file_size',
        'note',
        'submitted_at',
        'is_late',
    ];

    /**
     * Hapus berkas fisik (versi terbaru + seluruh revisi) saat satu baris submission
     * dihapus lewat Eloquent ($submission->delete()). Berkas dihapus SETELAH database
     * berhasil, supaya kegagalan hapus tidak membuat baris kehilangan berkasnya.
     *
     * TIDAK terpicu pada penghapusan massal dan pada cascadeOnDelete di level database
     * (hapus tugas / hapus mata kuliah). Untuk kasus itu, Assignment::booted (hapus tugas)
     * dan Course::booted (hapus mata kuliah) menghapus folder submissions/{assignment_id},
     * yang juga memuat berkas semua revisi.
     */
    protected static function booted(): void
    {
        static::deleting(function (Submission $submission) {
            $submission->filesToDelete = $submission->revisions()
                ->pluck('file_path')
                ->push($submission->file_path)
                ->filter()
                ->unique()
                ->values()
                ->all();
        });

        static::deleted(function (Submission $submission) {
            if ($submission->filesToDelete !== []) {
                Storage::disk('local')->delete($submission->filesToDelete);
            }
        });
    }

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'is_late' => 'boolean',
        ];
    }

    /** Ukuran berkas terbaru dalam teks yang terbaca manusia ("850 KB"). */
    public function getFileSizeHumanAttribute(): ?string
    {
        return FileSize::format($this->file_size);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Alias untuk kompatibilitas dengan resource/controller API.
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function grade(): HasOne
    {
        return $this->hasOne(Grade::class);
    }

    // Riwayat pengumpulan: satu baris per versi (v1, v2, ...), termasuk versi terbaru.
    public function revisions(): HasMany
    {
        return $this->hasMany(SubmissionRevision::class);
    }
}