{{--
    View index: menampilkan daftar mata kuliah dalam bentuk tabel.
    Dibungkus <x-layout> (bukan @extends) sesuai batasan komponen Blade.
--}}
<x-layout title="Daftar Mata Kuliah">

    <h1>Daftar Mata Kuliah</h1>

    {{-- @forelse dipakai (bukan @foreach) supaya ada fallback rapi
         kalau suatu saat $mataKuliah kosong, tanpa perlu if terpisah. --}}
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Nama</th>
                <th>SKS</th>
                <th>Dosen</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($mataKuliah as $mk)
                <tr>
                    {{-- Semua output variabel pakai {{ }} agar otomatis
                         di-escape, sesuai batasan yang diminta. --}}
                    <td>{{ $mk['kode'] }}</td>
                    <td>{{ $mk['nama'] }}</td>
                    <td>{{ $mk['sks'] }}</td>
                    <td>{{ $mk['dosen'] }}</td>
                    <td>
                        {{-- route() dengan parameter id, bukan URL hardcode
                             seperti "/mata-kuliah/1", supaya tetap benar
                             walau struktur URI berubah. --}}
                        <a href="{{ route('mata-kuliah.show', $mk['id']) }}">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Belum ada data mata kuliah.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</x-layout>