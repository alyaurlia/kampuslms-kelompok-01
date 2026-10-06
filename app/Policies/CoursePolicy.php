<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

/**
 * JIKA Anda SUDAH punya CoursePolicy, jangan ditimpa: cukup pastikan
 * method view() di bawah setara dengan punya Anda.
 * Laravel 12 menemukan policy ini otomatis (Course -> CoursePolicy).
 */
class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // daftar sudah disaring di query (lihat controller index)
    }

    public function view(User $user, Course $course): bool
    {
        return $user->role === 'admin'
            || $course->lecturer_id === $user->id
            || $course->students()->whereKey($user->id)->exists();
    }

    public function update(User $user, Course $course): bool
    {
        return $user->role === 'admin' || $course->lecturer_id === $user->id;
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }
}