<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        /*
        |--------------------------------------------------------------------------
        | Statistik Utama
        |--------------------------------------------------------------------------
        */

        $totalDosen = User::where('role', 'dosen')->count();

        $totalMahasiswa = User::where('role', 'mahasiswa')->count();

        $totalMataKuliah = Course::count();

        $totalTugas = Assignment::count();


        /*
        |--------------------------------------------------------------------------
        | Status Tugas
        |--------------------------------------------------------------------------
        */

        $tugasPublished = Assignment::where('status', 'published')->count();

        $tugasDraft = Assignment::where('status', 'draft')->count();

        $persentasePublished = $totalTugas > 0
            ? round(($tugasPublished / $totalTugas) * 100)
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Pengumpulan Tugas
        |--------------------------------------------------------------------------
        */

        $totalPengumpulan = Submission::count();

        $pengumpulanDinilai = Submission::whereHas('grade')->count();

        $pengumpulanMenunggu = Submission::whereDoesntHave('grade')->count();

        $persentaseDinilai = $totalPengumpulan > 0
            ? round(($pengumpulanDinilai / $totalPengumpulan) * 100, 2)
            : 0;


        /*
        |--------------------------------------------------------------------------
        | Aktivitas Terbaru
        |--------------------------------------------------------------------------
        */

        $aktivitasTerbaru = Submission::with([
            'student',
            'assignment',
        ])
            ->latest('submitted_at')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Ringkasan Mata Kuliah
        |--------------------------------------------------------------------------
        |
        | Data diambil langsung dari tabel courses.
        | Relasi lecturer digunakan untuk mendapatkan nama dosen.
        |
        */

        $ringkasanMataKuliah = Course::with('lecturer')
            ->withCount('students')
            ->orderBy('name')
            ->get();


        return view('admin.dashboard', compact(
            'totalDosen',
            'totalMahasiswa',
            'totalMataKuliah',
            'totalTugas',

            'tugasPublished',
            'tugasDraft',
            'persentasePublished',

            'totalPengumpulan',
            'pengumpulanDinilai',
            'pengumpulanMenunggu',
            'persentaseDinilai',

            'aktivitasTerbaru',

            'ringkasanMataKuliah',
        ));
    }
}