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

        return $course->isManagedBy($user) || $course->hasStudent($user);
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

    // Mengumpulkan tugas: mahasiswa terdaftar, tugas harus published
    public function submit(User $user, Assignment $assignment): bool
    {
        return $assignment->status === 'published'
            && $assignment->course->hasStudent($user);
    }
}