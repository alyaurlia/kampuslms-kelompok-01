<?php

namespace App\Support;

use App\Models\Assignment;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;

/**
 * Angka pada lonceng notifikasi di navbar. Satu sumber untuk dua jalur:
 * angka awal yang dirender server di komponen notification-bell, dan jawaban
 * endpoint polling (NotifikasiController@belumDibaca), jadi keduanya selalu sama.
 *
 * - admin     : aktivitas hari ini (materi diunggah + tugas terbit + pengumpulan masuk)
 * - dosen     : pengumpulan di mata kuliah yang diampu yang belum dinilai
 * - mahasiswa : notifikasi database yang belum dibaca (tabel notifications)
 */
class JumlahNotifikasi
{
    public static function untuk(User $user): int
    {
        return match ($user->role) {
            'admin' => self::aktivitasHariIni(),
            'dosen' => self::menungguNilai($user),
            default => $user->unreadNotifications()->count(),
        };
    }

    private static function aktivitasHariIni(): int
    {
        $hariIni = now()->startOfDay();

        return Material::where('created_at', '>=', $hariIni)->count()
            + Assignment::where('status', 'published')->where('created_at', '>=', $hariIni)->count()
            + Submission::where('submitted_at', '>=', $hariIni)->count();
    }

    /** Sama dengan kartu "Menunggu Penilaian" di halaman Notifikasi dosen. */
    private static function menungguNilai(User $dosen): int
    {
        return Submission::query()
            ->join('assignments', 'assignments.id', '=', 'submissions.assignment_id')
            ->join('courses', 'courses.id', '=', 'assignments.course_id')
            ->where('courses.lecturer_id', $dosen->id)
            ->whereDoesntHave('grade')
            ->count();
    }
}