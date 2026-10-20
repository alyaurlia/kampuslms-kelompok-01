<?php

namespace App\Actions;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class SubmitAssignment
{
    private const DISK = 'local'; // storage/app/private

    // Kolom submission_revisions.version_number = unsignedTinyInteger (maks 255).
    private const MAX_VERSIONS = 255;

    /**
     * Simpan pengumpulan sebagai REVISI baru.
     *
     * - Baris `submissions` (unique assignment_id + user_id) selalu menunjuk versi TERBARU.
     * - Setiap pengumpulan, termasuk yang pertama, tercatat di `submission_revisions`
     *   (v1, v2, ...). Berkas versi lama TIDAK dihapus: ia tetap dirujuk revisinya.
     * - Otorisasi dan validasi dilakukan pemanggil (StoreSubmissionRequest).
     */
    public function handle(
        Assignment $assignment,
        User $user,
        UploadedFile $file,
        ?string $note = null,
    ): Submission {
        // is_late dihitung di server, bukan dari browser.
        $submittedAt = now();

        // store() memberi nama acak; nama asli hanya disimpan di original_name.
        $newPath = $file->store("submissions/{$assignment->id}", self::DISK);

        $attributes = [
            'file_path'     => $newPath,
            'original_name' => $file->getClientOriginalName(),
            'file_size'     => $file->getSize(),
            'note'          => $note,
            'submitted_at'  => $submittedAt,
            'is_late'       => $assignment->isLateAt($submittedAt),
        ];

        try {
            return DB::transaction(function () use ($assignment, $user, $attributes) {
                // lockForUpdate: dua kumpul ulang bersamaan dari akun yang sama
                // diproses bergantian, sehingga nomor versi tidak bentrok.
                $submission = $assignment->submissions()
                    ->where('user_id', $user->id)
                    ->lockForUpdate()
                    ->first();

                if ($submission) {
                    $this->backfillFirstRevision($submission);
                    $submission->update($attributes);
                } else {
                    $submission = $assignment->submissions()
                        ->create($attributes + ['user_id' => $user->id]);
                }

                $version = (int) $submission->revisions()->max('version_number') + 1;

                if ($version > self::MAX_VERSIONS) {
                    throw ValidationException::withMessages([
                        'file' => 'Jumlah pengumpulan ulang untuk tugas ini sudah mencapai batas.',
                    ]);
                }

                $submission->revisions()->create($attributes + ['version_number' => $version]);

                return $submission;
            });
        } catch (\Throwable $e) {
            // Gagal: hanya berkas BARU yang dibersihkan. Berkas versi lama tidak disentuh.
            Storage::disk(self::DISK)->delete($newPath);

            throw $e;
        }
    }

    /**
     * Pengumpulan yang dibuat SEBELUM fitur riwayat ada belum punya revisi.
     * Jadikan isinya v1 dulu, supaya berkas lamanya tidak jadi yatim.
     */
    private function backfillFirstRevision(Submission $submission): void
    {
        if ($submission->revisions()->exists()) {
            return;
        }

        $submission->revisions()->create([
            'version_number' => 1,
            'file_path'      => $submission->file_path,
            'original_name'  => $submission->original_name,
            'file_size'      => $submission->file_size,
            'note'           => $submission->note,
            'submitted_at'   => $submission->submitted_at,
            'is_late'        => $submission->is_late,
        ]);
    }
}