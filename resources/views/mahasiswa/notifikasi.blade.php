{{--
    Pusat Notifikasi mahasiswa: notifikasi database (tugas baru dan nilai) + status baca.
    Data dari MahasiswaNotifikasiController. Gaya: resources/css/dashboard/notifikasi.css
    (dipakai bersama halaman notifikasi admin dan dosen); kartu metrik memakai .admin-metric
    (components/dashboard.css) dan banner memakai .dash-hero (dashboard/dosen-mahasiswa.css).
--}}
@php
    // Parameter URL yang dipertahankan saat pindah tab (nilai bawaan tidak ditulis ke URL).
    $urlTab = fn (string $kunci) => route('mahasiswa.notifikasi', array_filter([
        'tab' => $kunci === 'semua' ? null : $kunci,
        'periode' => $periode === 'semua' ? null : $periode,
    ]));

    $filterAktif = $periode !== 'semua';
@endphp

<x-layout title="Notifikasi Mahasiswa" role="mahasiswa" full-footer>
    {{-- Gaya khusus halaman ini (penanda belum dibaca). Bisa dipindah ke notifikasi.css. --}}
    <style>
        .notif-item.is-unread {
            background: color-mix(in srgb, var(--tone) 5%, var(--color-surface));
            border-color: color-mix(in srgb, var(--tone) 38%, var(--color-border));
            box-shadow: inset 4px 0 0 var(--tone);
        }
        .notif-item.is-read .notif-item__icon { opacity: 0.6; }
        .notif-inline-form { display: inline; margin: 0; }
        .notif-markall { display: flex; justify-content: flex-end; margin: 0 0 0.85rem; }
    </style>

    <section class="notif">

        {{-- BANNER --}}
        <header class="dash-hero">
            <div class="dash-hero__top">
                <div>
                    <p class="dash-hero__portal">Portal Mahasiswa</p>
                    <h1 class="dash-hero__title">Pusat Notifikasi</h1>
                </div>
            </div>
            <p class="dash-hero__sub">
                Pantau tugas baru dari dosen dan nilai yang sudah diberikan di mata kuliah yang kamu ikuti.
            </p>
            <span class="dash-hero__status">
                <i></i>{{ $ringkasan['belum'] }} notifikasi belum dibaca
            </span>
        </header>

        {{-- KARTU RINGKASAN --}}
        <section class="admin-metrics" aria-label="Ringkasan notifikasi">
            <article class="admin-metric admin-metric--orange">
                <div class="admin-metric__top">
                    <div class="admin-metric__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.7 21a2 2 0 0 1-3.4 0"></path>
                        </svg>
                    </div>
                    <span class="admin-metric__index">01</span>
                </div>
                <div class="admin-metric__number">{{ $ringkasan['belum'] }}</div>
                <div class="admin-metric__label">Belum Dibaca</div>
                <div class="admin-metric__caption">Notifikasi yang belum kamu tandai sudah dibaca.</div>
            </article>

            <article class="admin-metric admin-metric--pink">
                <div class="admin-metric__top">
                    <div class="admin-metric__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 7v5l3 2"></path>
                        </svg>
                    </div>
                    <span class="admin-metric__index">02</span>
                </div>
                <div class="admin-metric__number">{{ $ringkasan['perluKumpul'] }}</div>
                <div class="admin-metric__label">Belum Dikumpulkan</div>
                <div class="admin-metric__caption">
                    Tugas aktif yang belum kamu kumpulkan dan belum lewat batas waktu.
                </div>
            </article>

            <article class="admin-metric admin-metric--blue">
                <div class="admin-metric__top">
                    <div class="admin-metric__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M8 12.5l3 3 5-6"></path>
                        </svg>
                    </div>
                    <span class="admin-metric__index">03</span>
                </div>
                <div class="admin-metric__number">{{ $ringkasan['nilai'] }}</div>
                <div class="admin-metric__label">Nilai Tersedia</div>
                <div class="admin-metric__caption">Pengumpulan kamu yang sudah dinilai dosen.</div>
            </article>

            <article class="admin-metric admin-metric--purple">
                <div class="admin-metric__top">
                    <div class="admin-metric__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <path d="M14 2v6h6"></path>
                            <path d="M8 13h8"></path>
                            <path d="M8 17h5"></path>
                        </svg>
                    </div>
                    <span class="admin-metric__index">04</span>
                </div>
                <div class="admin-metric__number">{{ $ringkasan['total'] }}</div>
                <div class="admin-metric__label">Total Notifikasi</div>
                <div class="admin-metric__caption">{{ $ringkasan['hariIni'] }} masuk hari ini.</div>
            </article>
        </section>

        <div class="notif-layout">

            {{-- KOLOM UTAMA: filter + daftar --}}
            <div class="notif-main">

                <div class="notif-toolbar">
                    <nav class="notif-tabs" aria-label="Jenis notifikasi">
                        @foreach ($tabs as $kunci => $info)
                            <a href="{{ $urlTab($kunci) }}"
                               class="notif-tab {{ $tab === $kunci ? 'is-active' : '' }}"
                               @if ($tab === $kunci) aria-current="page" @endif>
                                {{ $info['label'] }}
                                <span class="notif-tab__count">{{ $info['jumlah'] }}</span>
                            </a>
                        @endforeach
                    </nav>

                    <form method="GET" action="{{ route('mahasiswa.notifikasi') }}" class="notif-filter">
                        <input type="hidden" name="tab" value="{{ $tab }}">

                        <select name="periode" class="notif-select" aria-label="Periode">
                            @foreach ($opsiPeriode as $kunci => $label)
                                <option value="{{ $kunci }}" @selected($periode === $kunci)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <button type="submit" class="notif-btn notif-btn--primary">Terapkan</button>

                        @if ($filterAktif)
                            <a href="{{ route('mahasiswa.notifikasi', array_filter(['tab' => $tab === 'semua' ? null : $tab])) }}"
                               class="notif-btn">Reset</a>
                        @endif
                    </form>
                </div>

                @if ($ringkasan['belum'] > 0)
                    <form method="POST" action="{{ route('mahasiswa.notifikasi.dibaca-semua') }}" class="notif-markall">
                        @csrf
                        <button type="submit" class="notif-btn">Tandai semua dibaca ({{ $ringkasan['belum'] }})</button>
                    </form>
                @endif

                @forelse ($grupHari as $grup)
                    <section class="notif-day">
                        <div class="notif-day__head">
                            <h2>{{ $grup['label'] }}</h2>
                            <span>{{ $grup['jumlah'] }} notifikasi</span>
                        </div>

                        <div class="notif-list">
                            @foreach ($grup['items'] as $item)
                                <article class="notif-item notif-item--{{ $item['nada'] }} {{ $item['dibaca'] ? 'is-read' : 'is-unread' }}">
                                    <div class="notif-item__icon" aria-hidden="true">
                                        <svg viewBox="0 0 24 24">
                                            @if ($item['jenis'] === 'nilai')
                                                <circle cx="12" cy="12" r="9"></circle>
                                                <path d="M8 12.5l3 3 5-6"></path>
                                            @elseif ($item['jenis'] === 'tugas_baru')
                                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                <path d="M14 2v6h6"></path>
                                                <path d="M8 13h8"></path>
                                                <path d="M8 17h5"></path>
                                            @else
                                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                                <path d="M13.7 21a2 2 0 0 1-3.4 0"></path>
                                            @endif
                                        </svg>
                                    </div>

                                    <div class="notif-item__body">
                                        <div class="notif-item__top">
                                            <div class="notif-item__who">
                                                <strong>
                                                    @if ($item['jenis'] === 'nilai')
                                                        Nilai tersedia
                                                    @elseif ($item['jenis'] === 'tugas_baru')
                                                        Tugas baru
                                                    @else
                                                        Notifikasi
                                                    @endif
                                                </strong>
                                                @unless ($item['dibaca'])
                                                    <span class="notif-chip notif-chip--warn">Baru</span>
                                                @endunless
                                                <span class="notif-chip notif-chip--tone">
                                                    {{ $item['jenis'] === 'nilai' ? 'Penilaian' : ($item['jenis'] === 'tugas_baru' ? 'Tugas' : 'Info') }}
                                                </span>
                                            </div>
                                            <time class="notif-item__time" datetime="{{ $item['at']->toIso8601String() }}" title="{{ $item['lengkap'] }}">
                                                {{ $item['relatif'] }}
                                            </time>
                                        </div>

                                        <p class="notif-item__text">
                                            @if ($item['jenis'] === 'nilai')
                                                Dosen sudah memberi nilai untuk tugas
                                            @elseif ($item['jenis'] === 'tugas_baru')
                                                Tugas baru dipublikasikan:
                                            @endif
                                            <strong>"{{ $item['judul'] }}"</strong>
                                            @if ($item['course_id'])
                                                pada mata kuliah
                                                <a href="{{ route('mahasiswa.mata-kuliah.show', $item['course_id']) }}">{{ $item['course_name'] }}</a>.
                                            @endif
                                            @if ($item['jenis'] === 'nilai')
                                                Buka tugasnya untuk melihat nilai dan masukan dosen.
                                            @endif
                                        </p>

                                        @if ($item['course_code'] || $item['batas'])
                                            <div class="notif-item__meta">
                                                @if ($item['course_code'])
                                                    <span class="notif-meta notif-meta--kode">{{ $item['course_code'] }}</span>
                                                @endif
                                                @if ($item['batas'] && $item['jenis'] === 'tugas_baru')
                                                    <span class="notif-meta">{{ $item['batas'] }}</span>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="notif-item__actions">
                                            @if ($item['assignment_id'])
                                                <a class="notif-btn notif-btn--primary" href="{{ route('mahasiswa.tugas.show', $item['assignment_id']) }}">
                                                    {{ $item['jenis'] === 'nilai' ? 'Lihat Nilai' : 'Lihat Tugas' }}
                                                </a>
                                            @else
                                                <span class="notif-meta">Tugas ini sudah tidak tersedia.</span>
                                            @endif

                                            @unless ($item['dibaca'])
                                                <form method="POST" action="{{ route('mahasiswa.notifikasi.dibaca', $item['id']) }}" class="notif-inline-form">
                                                    @csrf
                                                    <button type="submit" class="notif-btn">Tandai Dibaca</button>
                                                </form>
                                            @endunless
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @empty
                    <div class="notif-empty">
                        <p>
                            @if ($ringkasan['total'] === 0)
                                Belum ada notifikasi. Notifikasi muncul saat dosen menerbitkan tugas baru atau memberi nilai.
                            @elseif ($filterAktif)
                                Tidak ada notifikasi pada periode ini.
                            @else
                                Tidak ada notifikasi untuk tab ini.
                            @endif
                        </p>
                        @if ($filterAktif)
                            <a class="notif-btn" href="{{ route('mahasiswa.notifikasi', array_filter(['tab' => $tab === 'semua' ? null : $tab])) }}">Reset filter</a>
                        @endif
                    </div>
                @endforelse

                @if ($total > 0)
                    <nav class="notif-pager" aria-label="Halaman notifikasi">
                        <p class="notif-pager__info">
                            Menampilkan {{ $halaman->firstItem() }}&ndash;{{ $halaman->lastItem() }} dari {{ $total }} notifikasi
                        </p>

                        @if ($halaman->lastPage() > 1)
                            <div class="notif-pager__links">
                                @if ($halaman->onFirstPage())
                                    <span class="notif-page notif-page--wide is-disabled">Sebelumnya</span>
                                @else
                                    <a class="notif-page notif-page--wide" href="{{ $halaman->previousPageUrl() }}" rel="prev">Sebelumnya</a>
                                @endif

                                @for ($p = max(1, $halaman->currentPage() - 2); $p <= min($halaman->lastPage(), $halaman->currentPage() + 2); $p++)
                                    @if ($p === $halaman->currentPage())
                                        <span class="notif-page is-active" aria-current="page">{{ $p }}</span>
                                    @else
                                        <a class="notif-page" href="{{ $halaman->url($p) }}">{{ $p }}</a>
                                    @endif
                                @endfor

                                @if ($halaman->hasMorePages())
                                    <a class="notif-page notif-page--wide" href="{{ $halaman->nextPageUrl() }}" rel="next">Berikutnya</a>
                                @else
                                    <span class="notif-page notif-page--wide is-disabled">Berikutnya</span>
                                @endif
                            </div>
                        @endif
                    </nav>
                @endif
            </div>

            {{-- KOLOM SAMPING --}}
            <aside class="notif-side">

                <section class="notif-card">
                    <p class="notif-card__eyebrow">PROGRES</p>
                    <h2 class="notif-card__title">Progres Pengumpulan</h2>
                    <p class="notif-card__desc">
                        Mata kuliahmu dengan tugas terbit yang paling banyak belum kamu kumpulkan.
                    </p>

                    @forelse ($progres as $mk)
                        <div class="notif-course notif-course--{{ $mk['level'] }}">
                            <div class="notif-course__top">
                                <a href="{{ route('mahasiswa.mata-kuliah.show', $mk['id']) }}">{{ $mk['nama'] }}</a>
                                <strong>{{ $mk['belum'] }} belum</strong>
                            </div>
                            <div class="notif-bar" role="img" aria-label="{{ $mk['persen'] }} persen sudah dikumpulkan">
                                <div class="notif-bar__fill" style="width: {{ $mk['persen'] }}%;"></div>
                            </div>
                            <p class="notif-course__sub">
                                {{ $mk['kode'] }} &middot; {{ $mk['kumpul'] }} dari {{ $mk['total'] }} tugas sudah dikumpulkan
                            </p>
                        </div>
                    @empty
                        <p class="notif-card__empty">Belum ada tugas terbit di mata kuliahmu.</p>
                    @endforelse
                </section>

                <section class="notif-card">
                    <p class="notif-card__eyebrow">TENGGAT</p>
                    <h2 class="notif-card__title">Jadwal Batas Tugas</h2>
                    <p class="notif-card__desc">
                        Tugas yang belum kamu kumpulkan dengan batas waktu paling dekat.
                    </p>

                    @forelse ($tenggat as $t)
                        <a class="notif-deadline {{ $t['mendesak'] ? 'is-urgent' : '' }}" href="{{ route('mahasiswa.tugas.show', $t['id']) }}">
                            <span class="notif-deadline__top">
                                <span class="notif-deadline__title">{{ $t['judul'] }}</span>
                                <span class="notif-deadline__time">{{ $t['jam'] }}</span>
                            </span>
                            <span class="notif-deadline__sub">{{ $t['kode'] }} &middot; {{ $t['nama'] }}</span>
                            <span class="notif-deadline__left">{{ $t['tanggal'] }} &middot; {{ $t['sisa'] }}</span>
                        </a>
                    @empty
                        <p class="notif-card__empty">Tidak ada tugas yang menunggu dikumpulkan.</p>
                    @endforelse
                </section>
            </aside>
        </div>
    </section>
</x-layout>