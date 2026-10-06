<?php

namespace App\Policies;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;

class AssignmentPolicy
{
    /**
     * Dosen boleh membuat tugas. Bila MK sudah diketahui, dosen harus
     * pengampunya. ($course null = MK tidak ada; biarkan validasi `exists` -> 422)
     */
    public function create(User $user, ?Course $course = null): bool
    {
        if ($user->role !== 'dosen') {
            return false;
        }

        return $course === null || (int) $course->lecturer_id === (int) $user->id;
    }

    public function update(User $user, Assignment $assignment): bool
    {
        return $this->owns($user, $assignment);
    }

    public function delete(User $user, Assignment $assignment): bool
    {
        return $this->owns($user, $assignment);
    }

    private function owns(User $user, Assignment $assignment): bool
    {
        return $user->role === 'dosen'
            && (int) $assignment->course->lecturer_id === (int) $user->id;
    }
}