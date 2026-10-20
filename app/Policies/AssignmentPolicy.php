<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;

class AssignmentPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // daftar disaring di query
    }

    // Termasuk unduh lampiran soal
    public function view(User $user, Assignment $assignment): bool
    {
        $course = $assignment->course;

        if ($user->role === 'mahasiswa') {
            // Tugas harus published, MK-nya diikuti, DAN MK berstatus active.
            return $assignment->status === 'published'
                && $course->status === 'active'
                && $course->hasStudent($user);
        }

        return $course->isManagedBy($user);
    }

    // Course wajib. Admin atau dosen pengampu MK tersebut.
    public function create(User $user, Course $course): bool
    {
        return $course->isManagedBy($user);
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $assignment->course->isManagedBy($user);
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $assignment->course->isManagedBy($user);
    }

    // Melihat daftar pengumpulan sebuah tugas (siapa sudah / belum / terlambat).
    // Admin atau dosen pengampu MK tersebut. Mahasiswa tidak pernah boleh:
    // isManagedBy() hanya meloloskan admin dan dosen pengampu.
    // Dipakai SubmissionListController::index lewat Gate::authorize('viewSubmissions', ...).
    public function viewSubmissions(User $user, Assignment $assignment): bool
    {
        return $assignment->course->isManagedBy($user);
    }

    // Mengumpulkan tugas: mahasiswa terdaftar, tugas published, MK active,
    // dan tugas masih menerima pengumpulan (belum lewat deadline, atau
    // keterlambatan diizinkan). Aturan deadline ada di
    // Assignment::acceptsSubmissionsAt, dipakai bersama oleh web dan API.
    public function submit(User $user, Assignment $assignment): bool
    {
        return $assignment->status === 'published'
            && $assignment->course->status === 'active'
            && $assignment->acceptsSubmissionsAt(now())
            && $assignment->course->hasStudent($user);
    }
}