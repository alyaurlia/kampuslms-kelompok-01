<?php

namespace App\Actions;

use App\Events\NilaiDiberikan;
use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;

/**
 * Logika penilaian tunggal, dipakai oleh controller web DAN controller API
 * (PUT /api/v1/submissions/{id}/grade) supaya perilakunya selalu sama.
 *
 * Upsert: 1 submission = 1 nilai (grades.submission_id unique).
 * Cek $grade->wasRecentlyCreated untuk membedakan 201 (baru) vs 200 (diperbarui).
 *
 * $publish: true/false = ubah status tampil ke mahasiswa (grades.is_published);
 *           null       = jangan sentuh (nilai lama dipertahankan / default DB untuk nilai baru).
 */
class GradeSubmission
{
    public function handle(
        Submission $submission,
        User $grader,
        float|int|string $score,
        ?string $feedback = null,
        ?bool $publish = null,
    ): Grade {
        $grade = $submission->grade ?? $submission->grade()->make();

        $attributes = [
            'graded_by' => $grader->id,
            'score'     => $score,
            'feedback'  => $feedback,
            'graded_at' => now(),
        ];

        if ($publish !== null) {
            $attributes['is_published'] = $publish;
        }

        // submission_id TIDAK ada di Grade::$fillable, jadi Grade::updateOrCreate(['submission_id' => ...])
        // akan membuang kolom itu saat membuat nilai baru. Di sini submission_id diisi lewat
        // relasi ($submission->grade()->make()), bukan mass assignment.
        $grade->forceFill($attributes)->save();

        // Notifikasi "nilai keluar" ke mahasiswa (lewat queue) HANYA saat nilai menjadi
        // published: nilai baru yang langsung terbit, atau is_published berubah menjadi true.
        // Mengubah skor atau feedback pada nilai yang sudah terbit tidak memicunya lagi.
        if ($this->terbit($grade)
            && ($grade->wasRecentlyCreated || $grade->wasChanged('is_published'))) {
            NilaiDiberikan::dispatch($grade);
        }

        return $grade;
    }

    /**
     * Kolom is_published bisa terisi default database, dan nilainya tidak ikut ada di
     * atribut model setelah insert; bila null, baca ulang dari database.
     */
    private function terbit(Grade $grade): bool
    {
        return (bool) ($grade->is_published
            ?? $grade->newQuery()->whereKey($grade->getKey())->value('is_published'));
    }
}