{{--
    Dashboard dosen: ringkasan mata kuliah yang diampu user yang sedang login.
    Tampilan kartu memakai komponen .dash-card
    (didefinisikan di resources/css/components/dashboard.css).
--}}
@php
    $mkDiampu = \App\Models\Course::where('lecturer_id', auth()->id());

    $totalMk      = (clone $mkDiampu)->count();
    $totalAktif   = (clone $mkDiampu)->where('status', 'active')->count();
    $totalPeserta = (clone $mkDiampu)->withCount('students')->get()->sum('students_count');

    // Data kartu. Total Mahasiswa & Tugas Menunggu Nilai masih angka statis
    // seperti sebelumnya (belum ada query-nya), ganti jika datanya sudah tersedia.
    $cards = [
        [
            'no'    => 1,
            'judul' => 'Mata Kuliah Diampu',
            'nilai' => $totalMk,
            'desc'  => 'Total mata kuliah yang kamu ampu pada semester berjalan.',
        ],
        [
            'no'    => 2,
            'judul' => 'Mata Kuliah Aktif',
            'nilai' => $totalAktif,
            'desc'  => 'Mata kuliah yang saat ini sedang berlangsung dan terbuka untuk mahasiswa.',
        ],
        [
            'no'    => 3,
            'judul' => 'Total Peserta',
            'nilai' => $totalPeserta,
            'desc'  => 'Jumlah peserta yang terdaftar di seluruh mata kuliah yang kamu ampu.',
        ],
        [
            'no'    => 4,
            'judul' => 'Total Mahasiswa',
            'nilai' => 120,
            'desc'  => 'Jumlah seluruh mahasiswa yang terdata di dalam sistem LMS Kampus.',
        ],
        [
            'no'    => 5,
            'judul' => 'Tugas Menunggu Nilai',
            'nilai' => 18,
            'desc'  => 'Pengumpulan tugas mahasiswa yang belum kamu beri penilaian.',
        ],
    ];
    // Data blok "Semester overview" & kalender. Masih statis seperti dashboard awal,
    // ganti dengan data asli jika sudah tersedia.
    $mataKuliahSemester = [
        ['nama' => 'Pemrograman Dasar',         'kode' => 'IF101'],
        ['nama' => 'Struktur Data',             'kode' => 'IF205'],
        ['nama' => 'Basis Data',                'kode' => 'IF310'],
        ['nama' => 'Rekayasa Perangkat Lunak',  'kode' => 'IF420'],
    ];
    $eventKalender = [
        '2026-09-07' => 'event',
        '2026-09-23' => 'info',
    ];
@endphp

<x-layout title="Dashboard Dosen" role="dosen">
    <section class="dash">
        <h1 class="page-title">Dashboard Dosen</h1>

        <div class="dash-grid">
            @foreach ($cards as $card)
                <article class="dash-card">
                    <div class="dash-card__icon">
                        <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
                            <circle cx="12" cy="12" r="10" fill="currentColor"/>
                            <path d="M7.5 12.5l3 3 6-6.5" fill="none" stroke="#fff" stroke-width="2"
                                  stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <h3 class="dash-card__title">{{ $card['judul'] }}</h3>
                    <p class="dash-card__value">{{ $card['nilai'] }}</p>
                    <p class="dash-card__desc">{{ $card['desc'] }}</p>

                    <span class="dash-card__number" aria-hidden="true">{{ $card['no'] }}</span>
                </article>
            @endforeach
        </div>
    </section>

    <x-home-overview
        semester="Semester Gasal (A) 2026/2027"
        :mata-kuliah="$mataKuliahSemester"
        :events="$eventKalender"
    />
</x-layout>