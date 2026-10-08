{{--
    Dashboard admin: ringkasan data sistem.
    Tampilan kartu memakai komponen .dash-card yang sama dengan dosen/dashboard
    dan mahasiswa/dashboard (didefinisikan di resources/css/components/dashboard.css).
--}}
@php
    // Data kartu. Masih angka statis seperti sebelumnya (belum ada query-nya),
    // ganti dengan data asli jika sudah tersedia.
    $cards = [
        [
            'no'    => 1,
            'judul' => 'Total Dosen',
            'nilai' => 25,
            'desc'  => 'Jumlah dosen yang terdaftar dan aktif di dalam sistem LMS Kampus.',
        ],
        [
            'no'    => 2,
            'judul' => 'Total Mahasiswa',
            'nilai' => 480,
            'desc'  => 'Jumlah seluruh mahasiswa yang terdata di dalam sistem LMS Kampus.',
        ],
        [
            'no'    => 3,
            'judul' => 'Mata Kuliah',
            'nilai' => 60,
            'desc'  => 'Total mata kuliah yang tersedia dan dikelola di seluruh program studi.',
        ],
    ];
@endphp

<x-layout title="Dashboard Admin" role="admin">
    <section class="dash">
        <h1 class="page-title">Dashboard Admin</h1>

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
</x-layout>