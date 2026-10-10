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

    // Mengumpulkan tugas: mahasiswa terdaftar, tugas published, MK active
    public function submit(User $user, Assignment $assignment): bool
    {
        return $assignment->status === 'published'
            && $assignment->course->status === 'active'
            && $assignment->course->hasStudent($user);
    }
}