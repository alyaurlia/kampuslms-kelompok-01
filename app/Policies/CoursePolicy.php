<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    /** Dosen pengampu, atau mahasiswa yang terdaftar. */
    public function view(User $user, Course $course): bool
    {
        return match ($user->role) {
            'dosen' => (int) $course->lecturer_id === (int) $user->id,
            'mahasiswa' => $course->students()->whereKey($user->id)->exists(),
            default => false,
        };
    }
}