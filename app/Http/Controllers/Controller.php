<?php

namespace App\Http\Controllers;

use App\Models\Course;

abstract class Controller
{
    /**
     * SEMENTARA (Build 5): boleh melihat mata kuliah ini?
     * Akan dipindah ke CoursePolicy@view di minggu 7.
     */
    protected function canViewCourse(Course $course): bool
{
    $user = auth()->user();

    if (! $user) {
        return false;
    }

    return match ($user->role) {
        // ... tetap sama
    };
}

protected function canManageCourse(Course $course): bool
{
    $user = auth()->user();

    if (! $user) {
        return false;
    }

    return $user->role === 'admin' || $course->lecturer_id === $user->id;
}
}