<?php

namespace App\Support;

use App\Models\Assignment;
use App\Models\Material;
use App\Models\Submission;
use App\Models\User;

/**
 * Angka pada lonceng notifikasi di navbar.
 *
 * - Dosen & mahasiswa: jumlah notifikasi database yang belum dibaca.
 * - Admin: jumlah aktivitas hari ini (materi diunggah + tugas terbit + pengumpulan),
 *   karena Pusat Notifikasi admin berupa feed aktivitas, bukan tabel notifications.
 *   Rumusnya sama dengan "aktivitasHariIni" di AdminNotifikasiController::ringkasan().
 */
class JumlahNotifikasi
{
    public static function untuk(User $user): int
    {
        if ($user->role === 'admin') {
            return self::aktivitasHariIni();
        }

        return $user->unreadNotifications()->count();
    }

    public static function aktivitasHariIni(): int
    {
        $hariIni = now()->startOfDay();

        return Material::where('created_at', '>=', $hariIni)->count()
            + Assignment::where('status', 'published')->where('created_at', '>=', $hariIni)->count()
            + Submission::where('submitted_at', '>=', $hariIni)->count();
    }
}