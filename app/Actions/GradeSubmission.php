<?php

namespace App\Actions;

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

        // TODO F9: kirim notifikasi "nilai keluar" ke mahasiswa (via queue)
        // HANYA saat nilai menjadi published: $grade->is_published dan
        // ($grade->wasRecentlyCreated || $grade->wasChanged('is_published')).

        return $grade;
    }
}