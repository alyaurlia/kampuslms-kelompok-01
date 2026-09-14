{{--
    View index: menampilkan daftar mata kuliah dalam bentuk grid kartu,
    meniru gaya kartu "Kursusku" di Beranda ITK (banner bermotif + badge
    kode + tombol aksi bulat), bukan lagi tabel.

    Style kartu (.mk-grid, .mk-grid-card, dst.) didefinisikan terpusat di
    layout.blade.php supaya konsisten dan tidak diduplikasi dengan
    courses/show.blade.php.

    Dibungkus <x-layout> (bukan @extends) sesuai batasan komponen Blade.

    CATATAN PERUBAHAN:
    Card tidak lagi berupa <a> tunggal yang membungkus seluruh konten,
    karena sekarang ada 3 aksi terpisah (detail, edit, hapus) yang tidak
    boleh saling menimpa klik satu sama lain. Sebagai gantinya:
    - Banner + judul + meta dibungkus <a> (area "lihat detail").
    - Tombol edit & hapus ditaruh di footer terpisah di luar <a> tsb.
--}}
<x-layout title="Daftar Mata Kuliah">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1 style="font-size:1.6rem; margin:0;">Daftar Mata Kuliah</h1>

        <a href="{{ route('mata-kuliah.create') }}"
           style="padding:0.5rem 1rem; border-radius:4px; background:var(--color-card-1-from); color:#fff; text-decoration:none; font-family:Arial, sans-serif; font-size:0.9rem;">
            + Tambah Mata Kuliah
        </a>
    </div>

    {{-- Notifikasi sukses dari redirect create/update/delete --}}
    @if (session('success'))
        <div style="background:#e6f4ea; border:1px solid #b7dfc2; border-radius:6px; padding:1rem; margin-bottom:1.5rem; font-family:Arial, sans-serif; color:#1e7e34;">
            {{ session('success') }}
        </div>
    @endif

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

                <div class="mk-grid-card" style="display:flex; flex-direction:column;">

                    {{-- Area klik untuk lihat detail: banner + judul + meta --}}
                    <a href="{{ route('mata-kuliah.show', $mk['id']) }}" style="text-decoration:none; color:inherit;">
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

                    {{-- Footer aksi: di luar <a> di atas supaya klik edit/hapus
                         tidak ikut men-trigger navigasi ke halaman detail. --}}
                    <div style="display:flex; gap:0.5rem; padding:0 1rem 1rem 1rem; margin-top:auto;">
                        <a href="{{ route('mata-kuliah.edit', $mk['id']) }}"
                           style="flex:1; text-align:center; padding:0.4rem; border-radius:4px; border:1px solid var(--color-border); text-decoration:none; color:inherit; font-family:Arial, sans-serif; font-size:0.85rem;">
                            Edit
                        </a>

                        <form action="{{ route('mata-kuliah.destroy', $mk['id']) }}" method="POST"
                              style="flex:1;"
                              onsubmit="return confirm('Yakin ingin menghapus mata kuliah {{ $mk['nama'] }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="width:100%; padding:0.4rem; border-radius:4px; border:1px solid #f5c2c0; background:#fdecea; color:#b3261e; cursor:pointer; font-family:Arial, sans-serif; font-size:0.85rem;">
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>
    @endif

</x-layout>