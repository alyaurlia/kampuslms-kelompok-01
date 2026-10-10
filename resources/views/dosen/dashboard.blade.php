{{--
    Dashboard dosen: ringkasan mata kuliah yang diampu user yang sedang login.
    Tampilan kartu memakai gaya .admin-metric (sama seperti dashboard admin)
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
            'ikon'  => 'buku',
            'judul' => 'Mata Kuliah Diampu',
            'nilai' => $totalMk,
            'desc'  => 'Total mata kuliah yang kamu ampu pada semester berjalan.',
        ],
        [
            'no'    => 2,
            'ikon'  => 'cek',
            'judul' => 'Mata Kuliah Aktif',
            'nilai' => $totalAktif,
            'desc'  => 'Mata kuliah yang saat ini sedang berlangsung dan terbuka untuk mahasiswa.',
        ],
        [
            'no'    => 3,
            'ikon'  => 'orang',
            'judul' => 'Total Peserta',
            'nilai' => $totalPeserta,
            'desc'  => 'Jumlah peserta yang terdaftar di seluruh mata kuliah yang kamu ampu.',
        ],
        [
            'no'    => 4,
            'ikon'  => 'topi',
            'judul' => 'Total Mahasiswa',
            'nilai' => 120,
            'desc'  => 'Jumlah seluruh mahasiswa yang terdata di dalam sistem LMS Kampus.',
        ],
        [
            'no'    => 5,
            'ikon'  => 'berkas',
            'id'    => 'tugas-menunggu-nilai',
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
    // Ikon kartu (gambar garis, warna ikut aksen kartu)
    $ikonSvg = [
        'buku'  => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16z"></path><path d="M4 5.5v16"></path><path d="M8 7h8"></path><path d="M8 11h6"></path>',
        'cek'   => '<circle cx="12" cy="12" r="9"></circle><path d="M8 12.5l3 3 5-6"></path>',
        'orang' => '<circle cx="9" cy="7" r="4"></circle><path d="M3 21v-2a6 6 0 0 1 12 0v2"></path><path d="M16 3.5a4 4 0 0 1 0 7"></path><path d="M18 14a5 5 0 0 1 3 4.6V21"></path>',
        'topi'  => '<path d="M2 10l10-5 10 5-10 5L2 10z"></path><path d="M6 12.5V17c2.5 2 9.5 2 12 0v-4.5"></path><path d="M22 10v6"></path>',
        'berkas'=> '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><path d="M8 13h8"></path><path d="M8 17h5"></path>',
        'bintang'=> '<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"></path>',
        'tumpuk'=> '<path d="M12 3l9 5-9 5-9-5 9-5z"></path><path d="M3 13l9 5 9-5"></path>',
    ];
    $warna = ['pink', 'purple', 'blue', 'orange', 'pink'];
@endphp

<x-layout title="Dashboard Dosen" role="dosen" full-footer>
    <section class="dash">
        <x-dash-hero portal="Portal Dosen" title="Dashboard Dosen" :notif="$cards[4]['nilai']" notif-href="#tugas-menunggu-nilai" notif-label="Tugas Menunggu Nilai" search-label="Cari mata kuliah..." />

        <div class="admin-metrics admin-metrics--flow">
            @foreach ($cards as $card)
                <article @if (isset($card['id'])) id="{{ $card['id'] }}" @endif class="admin-metric admin-metric--{{ $warna[$loop->index % count($warna)] }}">
                    <div class="admin-metric__top">
                        <div class="admin-metric__icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">{!! $ikonSvg[$card['ikon']] !!}</svg>
                        </div>
                        <span class="admin-metric__index">{{ sprintf('%02d', $card['no']) }}</span>
                    </div>

                    <div class="admin-metric__number">{{ $card['nilai'] }}</div>
                    <div class="admin-metric__label">{{ $card['judul'] }}</div>
                    <div class="admin-metric__caption">{{ $card['desc'] }}</div>
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