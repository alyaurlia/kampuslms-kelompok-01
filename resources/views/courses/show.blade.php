{{--
    View show: menampilkan detail satu mata kuliah.
    $mataKuliah di sini adalah array asosiatif tunggal (bukan koleksi),
    sesuai bentuk data yang dikirim controller lewat compact().
--}}
<x-layout :title="$mataKuliah['nama']">

    {{-- Tombol kembali ditaruh di atas judul supaya konsisten dengan pola
         navigasi umum "list -> detail -> kembali ke list". --}}
    <a href="{{ route('mata-kuliah.index') }}">&larr; Kembali ke Daftar Mata Kuliah</a>

    <h1>{{ $mataKuliah['nama'] }}</h1>

    <dl>
        <dt>Kode</dt>
        <dd>{{ $mataKuliah['kode'] }}</dd>

        <dt>SKS</dt>
        <dd>{{ $mataKuliah['sks'] }}</dd>

        <dt>Dosen Pengampu</dt>
        <dd>{{ $mataKuliah['dosen'] }}</dd>

        <dt>Deskripsi</dt>
        <dd>{{ $mataKuliah['deskripsi'] }}</dd>
    </dl>

</x-layout>