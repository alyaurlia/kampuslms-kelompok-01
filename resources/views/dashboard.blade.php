
@php
    $namaPengguna = 'Adelia';

    $semesters = [
        [
            'label' => 'Semester Gasal (A) 2026/2027',
            'aktif' => true,
            'courses' => [
                ['nama' => 'Pemrograman Dasar', 'kode' => 'IF101'],
                ['nama' => 'Struktur Data', 'kode' => 'IF205'],
                ['nama' => 'Basis Data', 'kode' => 'IF310'],
                ['nama' => 'Rekayasa Perangkat Lunak', 'kode' => 'IF420'],
            ],
        ],
        [
            'label' => 'Semester Genap (E) 2026',
            'aktif' => false,
            'courses' => [],
        ],
        [
            'label' => 'Semester Gasal (A) 2025/2026',
            'aktif' => false,
            'courses' => [],
        ],
        [
            'label' => 'Semester Genap (E) 2025',
            'aktif' => false,
            'courses' => [],
        ],
    ];
@endphp

<x-layout title="Dasbor">

    <h1 style="font-size:1.6rem; margin-bottom:1.5rem;">
        Jumpa lagi, {{ $namaPengguna }}! 👋
    </h1>

    <section style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem;">

        <h2 style="font-size:1.1rem; margin-top:0; margin-bottom:1rem;">Semester overview</h2>

        @foreach ($semesters as $semester)
            <details @if ($semester['aktif']) open @endif
                     style="border:1px solid var(--color-border); border-radius:4px; margin-bottom:0.5rem;">

                <summary style="cursor:pointer; padding:0.75rem 1rem; font-weight:600; font-family:Arial, sans-serif; font-size:0.95rem; list-style:revert;">
                    {{ $semester['label'] }}
                </summary>

                @if (count($semester['courses']) === 0)
                    <p style="margin:0; padding:0 1rem 1rem; font-family:Arial, sans-serif; font-size:0.85rem; color:var(--color-ink-soft);">
                        Belum ada data mata kuliah untuk semester ini.
                    </p>
                @else
                    <div style="border-top:1px solid var(--color-border);">
                        @foreach ($semester['courses'] as $course)
                            <div style="display:flex; align-items:center; justify-content:space-between; padding:0.75rem 1rem; {{ !$loop->last ? 'border-bottom:1px solid var(--color-border);' : '' }}">

                                <span style="font-family:Arial, sans-serif; font-size:0.9rem; font-weight:600;">
                                    {{ $course['nama'] }} - {{ $course['kode'] }}
                                </span>

                                <span aria-hidden="true" style="color:var(--color-ink-soft);">&#9734;</span>
                            </div>
                        @endforeach
                    </div>
                @endif

            </details>
        @endforeach

    </section>

</x-layout>