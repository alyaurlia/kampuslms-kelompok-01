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
        $totalAdmin = User::where('role', 'admin')->count();
        $totalDosen = User::where('role', 'dosen')->count();
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalPengguna = $totalAdmin + $totalDosen + $totalMahasiswa;

        $totalMataKuliah = Course::count();
        $totalTugas = Assignment::count();

        /*
        |--------------------------------------------------------------------------
        | Komposisi Pengguna
        |--------------------------------------------------------------------------
        */
        $komposisiPengguna = collect([
            [
                'key' => 'admin',
                'label' => 'Admin',
                'jumlah' => $totalAdmin,
            ],
            [
                'key' => 'dosen',
                'label' => 'Dosen',
                'jumlah' => $totalDosen,
            ],
            [
                'key' => 'mahasiswa',
                'label' => 'Mahasiswa',
                'jumlah' => $totalMahasiswa,
            ],
        ])->map(function (array $peran) use ($totalPengguna) {
            $peran['persentase'] = $totalPengguna > 0
                ? round(($peran['jumlah'] / $totalPengguna) * 100)
                : 0;

            return $peran;
        });

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
        | Grafik Pengumpulan Enam Bulan Terakhir
        |--------------------------------------------------------------------------
        */
        $awalPeriode = now()->startOfMonth()->subMonths(5);

        $dataPengumpulan = Submission::whereNotNull('submitted_at')
            ->where('submitted_at', '>=', $awalPeriode)
            ->get(['submitted_at']);

        $jumlahPerBulan = $dataPengumpulan->groupBy(function ($submission) {
            return \Illuminate\Support\Carbon::parse(
                $submission->submitted_at
            )->format('Y-m');
        });

        $grafikPengumpulanMentah = collect();

        for ($i = 0; $i < 6; $i++) {
            $tanggalBulan = $awalPeriode->copy()->addMonths($i);
            $kunciBulan = $tanggalBulan->format('Y-m');

            $grafikPengumpulanMentah->push([
                'key' => $kunciBulan,
                'label' => $tanggalBulan->translatedFormat('M'),
                'jumlah' => $jumlahPerBulan
                    ->get($kunciBulan, collect())
                    ->count(),
            ]);
        }

        $nilaiGrafikMaksimum = max(
            1,
            (int) $grafikPengumpulanMentah->max('jumlah')
        );

        $grafikPengumpulan = $grafikPengumpulanMentah->map(
            function (array $bulan) use ($nilaiGrafikMaksimum) {
                $bulan['tinggi'] = $bulan['jumlah'] > 0
                    ? max(
                        5,
                        round(
                            ($bulan['jumlah'] / $nilaiGrafikMaksimum) * 100
                        )
                    )
                    : 0;

                return $bulan;
            }
        );

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
        */
        $ringkasanMataKuliah = Course::with('lecturer')
            ->withCount('students')
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Kirim Data ke Dashboard Admin
        |--------------------------------------------------------------------------
        */
        return view('admin.dashboard', compact(
            'totalAdmin',
            'totalDosen',
            'totalMahasiswa',
            'totalPengguna',
            'komposisiPengguna',
            'totalMataKuliah',
            'totalTugas',
            'tugasPublished',
            'tugasDraft',
            'persentasePublished',
            'totalPengumpulan',
            'pengumpulanDinilai',
            'pengumpulanMenunggu',
            'persentaseDinilai',
            'grafikPengumpulan',
            'aktivitasTerbaru',
            'ringkasanMataKuliah',
        ));
    }
}