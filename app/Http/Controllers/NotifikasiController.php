<?php

namespace App\Http\Controllers;

use App\Support\JumlahNotifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Notifikasi untuk user yang sedang login (jalur web / session).
 *
 * Versi API-nya ada di Api\V1\NotificationController (pakai token Sanctum).
 * Method untuk halaman daftar (index) dan "tandai dibaca" (Build nomor 5)
 * nanti ditambahkan di controller ini.
 */
class NotifikasiController extends Controller
{
    /**
     * GET /notifikasi/belum-dibaca
     *
     * Dipanggil berkala oleh lonceng di navbar (polling).
     * Dosen & mahasiswa: notifikasi belum dibaca. Admin: aktivitas hari ini
     * (lihat JumlahNotifikasi). Selalu dihitung dari $request->user(), tidak ada
     * ID dari klien, jadi tidak mungkin melihat jumlah milik orang lain.
     */
    public function belumDibaca(Request $request): JsonResponse
    {
        return response()
            ->json(['count' => JumlahNotifikasi::untuk($request->user())])
            ->header('Cache-Control', 'no-store');
    }
}