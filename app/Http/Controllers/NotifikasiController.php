<?php

namespace App\Http\Controllers;

use App\Support\JumlahNotifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Notifikasi untuk user yang sedang login (jalur web / session).
 *
 * Versi API-nya ada di Api\V1\NotificationController (pakai token Sanctum).
 */
class NotifikasiController extends Controller
{
    /**
     * GET /notifikasi/belum-dibaca
     *
     * Dipanggil berkala oleh lonceng di navbar (polling). Angkanya bergantung
     * pada peran (lihat JumlahNotifikasi) dan selalu dihitung dari
     * $request->user(); tidak ada ID dari klien, jadi tidak mungkin melihat
     * jumlah milik orang lain.
     */
    public function belumDibaca(Request $request): JsonResponse
    {
        return response()
            ->json(['count' => JumlahNotifikasi::untuk($request->user())])
            ->header('Cache-Control', 'no-store');
    }
}