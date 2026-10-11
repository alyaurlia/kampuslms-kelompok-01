<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // daftar disaring di query
    }

    // Termasuk unduh berkas dan membuka revisi
    public function view(User $user, Submission $submission): bool
    {
        if ($user->role === 'mahasiswa') {
            return (int) $submission->user_id === (int) $user->id;
        }

        return $submission->assignment->course->isManagedBy($user);
    }

    // Memberi / memperbarui nilai (dipakai form web dan @can di Blade).
    // Aturannya TIDAK ditulis ulang di sini: didelegasikan ke GradePolicy, yang juga
    // dipakai API, supaya web dan API tidak pernah berbeda perilaku.
    // GradePolicy: hanya dosen pengampu MK; admin tidak boleh memberi nilai.
    public function grade(User $user, Submission $submission): bool
    {
        $policy = app(GradePolicy::class);

        return $submission->grade
            ? $policy->update($user, $submission->grade)
            : $policy->create($user, $submission);
    }

    // Mengumpulkan (termasuk mengumpulkan ulang sebagai revisi baru)
    public function create(User $user, Assignment $assignment): bool
    {
        return $assignment->status === 'published'
            && $assignment->course->hasStudent($user);
    }

    // Mahasiswa tidak boleh menarik, admin/dosen tidak boleh mengubah
    public function update(User $user, Submission $submission): bool
    {
        return false;
    }

    public function delete(User $user, Submission $submission): bool
    {
        return false;
    }
}