{{--
    View show: menampilkan detail satu mata kuliah.
    $mataKuliah di sini adalah array asosiatif tunggal (bukan koleksi),
    sesuai bentuk data yang dikirim controller lewat compact().
--}}
<x-layout :title="$mataKuliah['nama']">

    {{-- Tombol kembali ditaruh di atas judul supaya konsisten dengan pola
         navigasi umum "list -> detail -> kembali ke list". --}}
    <a href="{{ route('mata-kuliah.index') }}"
       style="font-family:Arial, sans-serif; font-size:0.85rem; color:var(--color-ink-soft); text-decoration:none;">
        &larr; Kembali ke Daftar Mata Kuliah
    </a>

    <h1 style="font-size:1.6rem; margin:0.75rem 0 1.5rem;">{{ $mataKuliah['nama'] }}</h1>

    <section style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem;">

        <dl style="font-family:Arial, sans-serif; font-size:0.9rem; margin:0;">
            <dt style="color:var(--color-ink-soft); font-weight:600; margin-bottom:0.25rem;">Kode</dt>
            <dd style="margin:0 0 1rem;">{{ $mataKuliah['kode'] }}</dd>

            <dt style="color:var(--color-ink-soft); font-weight:600; margin-bottom:0.25rem;">SKS</dt>
            <dd style="margin:0 0 1rem;">{{ $mataKuliah['sks'] }}</dd>

            <dt style="color:var(--color-ink-soft); font-weight:600; margin-bottom:0.25rem;">Dosen Pengampu</dt>
            <dd style="margin:0 0 1rem;">{{ $mataKuliah['dosen'] }}</dd>

            <dt style="color:var(--color-ink-soft); font-weight:600; margin-bottom:0.25rem;">Deskripsi</dt>
            <dd style="margin:0; line-height:1.6;">{{ $mataKuliah['deskripsi'] }}</dd>
        </dl>

    </section>

</x-layout>