<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class MahasiswaDashboardController extends Controller
{
    /**
     * GET /mahasiswa/dashboard
     * Mengirim daftar mata kuliah yang DIIKUTI mahasiswa yang sedang login
     * ke view, untuk bagian "Semester overview".
     * Draft/archived sudah disaring oleh scope Course::visibleTo() untuk mahasiswa.
     */
    public function index(Request $request)
    {
        $courses = Course::query()
            ->visibleTo($request->user())      // mahasiswa: hanya MK aktif yang diikuti
            ->orderBy('name')
            ->get(['id', 'code', 'name', 'sks']);

        return view('mahasiswa.dashboard', [
            'courses' => $courses,
        ]);
    }
}