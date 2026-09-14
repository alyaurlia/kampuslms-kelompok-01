{{--
    View index: menampilkan daftar mata kuliah dalam bentuk grid kartu,
    meniru gaya kartu "Kursusku" di Beranda ITK (banner bermotif + badge
    kode + tombol aksi bulat), bukan lagi tabel.

    Style kartu (.mk-grid, .mk-grid-card, dst.) didefinisikan terpusat di
    layout.blade.php supaya konsisten dan tidak diduplikasi dengan
    courses/show.blade.php.

    Dibungkus <x-layout> (bukan @extends) sesuai batasan komponen Blade.
--}}
<x-layout title="Daftar Mata Kuliah">

    <h1 style="font-size:1.6rem; margin-bottom:1.5rem;">Daftar Mata Kuliah</h1>

    {{-- @forelse dipakai (bukan @foreach) supaya ada fallback rapi
         kalau suatu saat $mataKuliah kosong, tanpa perlu if terpisah. --}}
    @forelse ($mataKuliah as $mk)
    @empty
        <section style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem;">
            <p style="margin:0; color:var(--color-ink-soft); text-align:center; font-family:Arial, sans-serif;">
                Belum ada data mata kuliah.
            </p>
        </section>
    @endforelse

    @if (count($mataKuliah))
        <div class="mk-grid">
            @foreach ($mataKuliah as $mk)
                @php
                    // Palet & motif sama persis dengan show.blade.php, memakai
                    // variabel --color-card-N-* dari layout.blade.php, supaya
                    // satu mata kuliah punya identitas visual yang konsisten
                    // di daftar maupun di halaman detailnya.
                    $mkPatterns = ['diamond', 'triangle', 'circle', 'diamond', 'triangle'];
                    $mkIndex = (crc32($mk['kode']) % 5) + 1;
                    $mkPattern = $mkPatterns[$mkIndex - 1];
                @endphp

                <a href="{{ route('mata-kuliah.show', $mk['id']) }}" class="mk-grid-card">

                    <div class="mk-grid-banner mk-pattern-{{ $mkPattern }}"
                         style="background: linear-gradient(135deg, var(--color-card-{{ $mkIndex }}-from), var(--color-card-{{ $mkIndex }}-to));">
                        <span class="mk-badge">{{ $mk['kode'] }}</span>
                        <span class="mk-grid-arrow" aria-hidden="true">&#10132;</span>
                    </div>

                    <div class="mk-grid-body">
                        {{-- Semua output variabel pakai {{ }} agar otomatis
                             di-escape, sesuai batasan yang diminta. --}}
                        <h2 class="mk-grid-title">{{ $mk['nama'] }}</h2>
                        <p class="mk-grid-meta">{{ $mk['sks'] }} SKS &middot; {{ $mk['dosen'] }}</p>
                    </div>

                </a>
            @endforeach
        </div>
    @endif

</x-layout>