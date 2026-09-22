{{--
    View show: menampilkan detail satu mata kuliah.
    $mataKuliah di sini adalah array asosiatif tunggal (bukan koleksi),
    sesuai bentuk data yang dikirim controller lewat compact().

    Style kartu (.mk-card, .mk-banner, dst.) didefinisikan terpusat di
    layout.blade.php supaya konsisten dan tidak diduplikasi dengan
    courses/index.blade.php.
--}}
<x-layout :title="$mataKuliah['nama']">

    {{-- Tombol kembali ditaruh di atas judul supaya konsisten dengan pola
         navigasi umum "list -> detail -> kembali ke list". --}}
    <a href="{{ route('mata-kuliah.index') }}" class="mk-back-link">
        &larr; Kembali ke Daftar Mata Kuliah
    </a>

    @php
        // Palet warna & motif dirotasi berdasarkan kode mata kuliah (crc32),
        // memakai variabel --color-card-N-* yang sama dengan index.blade.php,
        // supaya identitas visual satu mata kuliah konsisten di kedua halaman.
        $mkPatterns = ['diamond', 'triangle', 'circle', 'diamond', 'triangle'];
        $mkIndex = (crc32($mataKuliah['kode']) % 5) + 1;
        $mkPattern = $mkPatterns[$mkIndex - 1];
    @endphp

    <div class="mk-card">

        <div class="mk-banner mk-pattern-{{ $mkPattern }}"
             style="background: linear-gradient(135deg, var(--color-card-{{ $mkIndex }}-from), var(--color-card-{{ $mkIndex }}-to));">
            <span class="mk-badge">{{ $mataKuliah['kode'] }}</span>
        </div>

        <div class="mk-body">
            <h1 class="mk-title">{{ $mataKuliah['nama'] }}</h1>

            <div class="mk-meta-row">
                <div class="mk-meta-item">
                    <span class="mk-meta-label">SKS</span>
                    <span class="mk-meta-value">{{ $mataKuliah['sks'] }}</span>
                </div>
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Dosen Pengampu</span>
                    <span class="mk-meta-value">{{ $mataKuliah['dosen'] }}</span>
                </div>
            </div>

            <div class="mk-desc">
                <h2 class="mk-desc-label">Deskripsi</h2>
                <p class="mk-desc-text">{{ $mataKuliah['deskripsi'] }}</p>
            </div>
        </div>
    </div>

</x-layout> 

{{--
    View show: menampilkan detail satu pengguna.
    $user di sini adalah OBJEK model Eloquent (akses pakai ->), sesuai
    bentuk data yang dikirim controller lewat compact('user').

    Style kartu (.mk-card, .mk-banner, dst.) dipakai ulang dari
    layout.blade.php supaya identitas visual konsisten dengan
    courses/show.blade.php, meski datanya beda (nama, email, peran).
--}}