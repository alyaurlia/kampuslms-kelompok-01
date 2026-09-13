{{--
    View index: menampilkan daftar mata kuliah dalam bentuk tabel.
    Dibungkus <x-layout> (bukan @extends) sesuai batasan komponen Blade.
--}}
<x-layout title="Daftar Mata Kuliah">

    <h1 style="font-size:1.6rem; margin-bottom:1.5rem;">Daftar Mata Kuliah</h1>

    <section style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem;">

        {{-- @forelse dipakai (bukan @foreach) supaya ada fallback rapi
             kalau suatu saat $mataKuliah kosong, tanpa perlu if terpisah. --}}
        <table style="width:100%; border-collapse:collapse; font-family:Arial, sans-serif; font-size:0.9rem;">
            <thead>
                <tr style="text-align:left; border-bottom:2px solid var(--color-border);">
                    <th style="padding:0.6rem 0.5rem; color:var(--color-ink-soft); font-weight:600;">Kode</th>
                    <th style="padding:0.6rem 0.5rem; color:var(--color-ink-soft); font-weight:600;">Nama</th>
                    <th style="padding:0.6rem 0.5rem; color:var(--color-ink-soft); font-weight:600;">SKS</th>
                    <th style="padding:0.6rem 0.5rem; color:var(--color-ink-soft); font-weight:600;">Dosen</th>
                    <th style="padding:0.6rem 0.5rem; color:var(--color-ink-soft); font-weight:600;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($mataKuliah as $mk)
                    <tr style="{{ !$loop->last ? 'border-bottom:1px solid var(--color-border);' : '' }}">
                        {{-- Semua output variabel pakai {{ }} agar otomatis
                             di-escape, sesuai batasan yang diminta. --}}
                        <td style="padding:0.6rem 0.5rem;">{{ $mk['kode'] }}</td>
                        <td style="padding:0.6rem 0.5rem; font-weight:600;">{{ $mk['nama'] }}</td>
                        <td style="padding:0.6rem 0.5rem;">{{ $mk['sks'] }}</td>
                        <td style="padding:0.6rem 0.5rem;">{{ $mk['dosen'] }}</td>
                        <td style="padding:0.6rem 0.5rem;">
                            {{-- route() dengan parameter id, bukan URL hardcode
                                 seperti "/mata-kuliah/1", supaya tetap benar
                                 walau struktur URI berubah. --}}
                            <a href="{{ route('mata-kuliah.show', $mk['id']) }}"
                               style="color:var(--color-primary); text-decoration:none; font-weight:600;">
                                Detail &rarr;
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:1rem 0.5rem; color:var(--color-ink-soft); text-align:center;">
                            Belum ada data mata kuliah.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </section>

</x-layout>