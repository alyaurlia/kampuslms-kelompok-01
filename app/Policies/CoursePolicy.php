<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // daftar disaring di query (scopeVisibleTo)
    }

    public function view(User $user, Course $course): bool
    {
        return $course->isManagedBy($user) || $course->hasStudent($user);
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin';
    }

    // Matriks: CRUD mata kuliah hanya admin
    public function update(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->role === 'admin';
    }

    // Admin, atau dosen pengampu MK itu
    public function manageEnrollment(User $user, Course $course): bool
    {
        return $course->isManagedBy($user);
    }
}