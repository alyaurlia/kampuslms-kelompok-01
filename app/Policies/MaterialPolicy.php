<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\Material;
use App\Models\User;

class MaterialPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // daftar disaring di query
    }

    // Termasuk unduh berkas
    public function view(User $user, Material $material): bool
    {
        $course = $material->course;

        return $course->isManagedBy($user) || $course->hasStudent($user);
    }

    // Course wajib: admin atau dosen pengampu
    public function create(User $user, Course $course): bool
    {
        return $course->isManagedBy($user);
    }

    public function update(User $user, Material $material): bool
    {
        return $material->course->isManagedBy($user);
    }

    public function delete(User $user, Material $material): bool
    {
        return $material->course->isManagedBy($user);
    }
}