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

        return match ($user->role) {
            'admin'     => true,
            'dosen'     => $course->lecturer_id === $user->id,
            'mahasiswa' => $course->students()->whereKey($user->id)->exists(),
            default     => false,
        };
    }

    /**
     * SEMENTARA (Build 5): boleh mengelola (ubah/hapus) mata kuliah ini?
     * Akan dipindah ke CoursePolicy@update di minggu 7.
     */
    protected function canManageCourse(Course $course): bool
    {
        $user = auth()->user();

        return $user->role === 'admin' || $course->lecturer_id === $user->id;
    }
}