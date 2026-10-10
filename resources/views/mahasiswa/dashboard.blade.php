{{--
    Dashboard mahasiswa: ringkasan akademik.
    Tampilan kartu memakai komponen .dash-card yang sama dengan dosen/dashboard
    (didefinisikan di resources/css/components/dashboard.css).
--}}
@php
    // Variabel dari MahasiswaDashboardController@index: $courses
    // (koleksi mata kuliah aktif yang diikuti mahasiswa yang sedang login).
    $courses = $courses ?? collect();

    // Data kartu. IPK dan Total SKS masih angka statis (belum ada query-nya);
    // jumlah mata kuliah diambil dari data asli.
    $cards = [
        [
            'no'    => 1,
            'judul' => 'IPK',
            'nilai' => '3.75',
            'desc'  => 'Indeks Prestasi Kumulatif kamu dari seluruh semester yang sudah ditempuh.',
        ],
        [
            'no'    => 2,
            'judul' => 'Total SKS',
            'nilai' => 84,
            'desc'  => 'Jumlah satuan kredit semester yang sudah kamu kumpulkan sampai saat ini.',
        ],
        [
            'no'    => 3,
            'judul' => 'Mata Kuliah Diambil',
            'nilai' => $courses->count(),
            'desc'  => 'Mata kuliah yang sedang kamu ikuti pada semester berjalan.',
        ],
    ];
    // Blok "Semester overview": daftar mata kuliah yang diikuti mahasiswa ini,
    // dikirim dari MahasiswaDashboardController sebagai $courses.
    $mataKuliahSemester = $courses->map(fn ($c) => [
        'nama' => $c->name,
        'kode' => $c->code,
        'url'  => route('mahasiswa.mata-kuliah.show', $c),
    ])->all();
    // Kalender: masih statis seperti dashboard awal.
    $eventKalender = [
        '2026-09-07' => 'event',
        '2026-09-23' => 'info',
    ];
@endphp

<x-layout title="Dashboard Mahasiswa" role="mahasiswa" full-footer>
    <section class="dash">
        <x-dash-hero portal="Portal Mahasiswa" title="Dashboard Mahasiswa"  search-label="Cari mata kuliah..." />

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

    {{-- semester="" (string kosong) = tanpa judul semester, hanya daftar mata kuliah.
         Jangan pakai null: null membuat @props memakai nilai bawaan komponen. --}}
    <x-home-overview
        semester=""
        :mata-kuliah="$mataKuliahSemester"
        :events="$eventKalender"
    />
</x-layout>