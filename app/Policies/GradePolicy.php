<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\Submission;
use App\Models\User;

class GradePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // disaring di query (lihat catatan di bawah)
    }

    public function view(User $user, Grade $grade): bool
    {
        $submission = $grade->submission;

        if ($user->role === 'mahasiswa') {
            return $grade->is_published
                && (int) $submission->user_id === (int) $user->id;
        }

        return $submission->assignment->course->isManagedBy($user);
    }

    // Memberi nilai: hanya dosen pengampu (admin tidak boleh)
    public function create(User $user, Submission $submission): bool
    {
        return $submission->assignment->course->isLecturedBy($user);
    }

    public function update(User $user, Grade $grade): bool
    {
        return $grade->submission->assignment->course->isLecturedBy($user);
    }

    public function delete(User $user, Grade $grade): bool
    {
        return $this->update($user, $grade);
    }

    // Mempublikasikan / menyembunyikan nilai
    public function publish(User $user, Grade $grade): bool
    {
        return $this->update($user, $grade);
    }
}