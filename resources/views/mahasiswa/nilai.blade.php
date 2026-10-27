{{--
    Rekap Nilai mahasiswa: nilai per mata kuliah dari tugas yang sudah dinilai dosen.
    Data dari MahasiswaNilaiController. Gaya: resources/css/dashboard/nilai.css; banner
    memakai .dash-hero, kartu metrik .admin-metric, serta kontrol .notif-* dari notifikasi.css.
--}}
@php
    // Format angka gaya Indonesia (koma desimal).
    $fmt = fn ($n, int $d = 2) => number_format((float) $n, $d, ',', '.');

    $filterAktif = $q !== '' || $status !== null;

    // Kelas warna huruf: a (hijau), b (biru), c (kuning), d (merah).
    $kelasHuruf = fn (?string $h) => match (true) {
        $h === null => 'none',
        str_starts_with($h, 'A') => 'a',
        str_starts_with($h, 'B') => 'b',
        str_starts_with($h, 'C') => 'c',
        default => 'd',
    };

    $labelStatus = ['lulus' => 'Lulus', 'perbaikan' => 'Perlu Perbaikan', 'belum' => 'Belum Ada Nilai'];
    $chipStatus = ['lulus' => 'ok', 'perbaikan' => 'bad', 'belum' => 'muted'];

    $labelKondisi = [
        'dinilai' => ['Sudah Dinilai', 'info'],
        'menunggu' => ['Menunggu Nilai', 'warn'],
        'belum' => ['Belum Dikumpulkan', 'muted'],
        'terlewat' => ['Tidak Dikumpulkan', 'bad'],
    ];
@endphp

<x-layout title="Rekap Nilai" role="mahasiswa" full-footer>
    <section class="nilai">

        {{-- BANNER --}}
        <header class="dash-hero">
            <div class="dash-hero__top">
                <div>
                    <p class="dash-hero__portal">Portal Mahasiswa</p>
                    <h1 class="dash-hero__title">Rekap Nilai</h1>
                </div>
            </div>
            <p class="dash-hero__sub">
                Pantau nilai tugas, nilai akhir sementara per mata kuliah, dan feedback dari dosen pengampu.
            </p>
            <span class="dash-hero__status">
                <i></i>Nilai sementara &middot; {{ $ringkasan['tugasDinilai'] }} dari {{ $ringkasan['tugasTotal'] }} tugas sudah dinilai
            </span>
        </header>

        {{-- KARTU RINGKASAN --}}
        <section class="admin-metrics" aria-label="Ringkasan nilai">
            <article class="admin-metric admin-metric--pink">
                <div class="admin-metric__top">
                    <div class="admin-metric__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 2l3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1z"></path>
                        </svg>
                    </div>
                    <span class="admin-metric__index">01</span>
                </div>
                <div class="admin-metric__number">
                    {{ $ringkasan['ips'] !== null ? $fmt($ringkasan['ips']) : '-' }}<small class="nilai-unit">/ 4,00</small>
                </div>
                <div class="admin-metric__label">IP Sementara</div>
                <div class="admin-metric__caption">
                    @if ($ringkasan['ips'] !== null)
                        Dihitung dari {{ $ringkasan['mkBernilai'] }} mata kuliah yang sudah bernilai ({{ $ringkasan['sksBernilai'] }} SKS).
                    @else
                        Belum ada tugas yang dinilai.
                    @endif
                </div>
            </article>

            <article class="admin-metric admin-metric--purple">
                <div class="admin-metric__top">
                    <div class="admin-metric__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M3 17l6-6 4 4 8-8"></path>
                            <path d="M14 7h7v7"></path>
                        </svg>
                    </div>
                    <span class="admin-metric__index">02</span>
                </div>
                <div class="admin-metric__number">
                    {{ $ringkasan['rata'] !== null ? $fmt($ringkasan['rata'], 1) : '-' }}<small class="nilai-unit">/ 100</small>
                </div>
                <div class="admin-metric__label">Rata-rata Nilai</div>
                <div class="admin-metric__caption">Rata-rata nilai akhir sementara seluruh mata kuliah yang sudah bernilai.</div>
            </article>

            <article class="admin-metric admin-metric--blue">
                <div class="admin-metric__top">
                    <div class="admin-metric__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16z"></path>
                            <path d="M8 7h8"></path>
                            <path d="M8 11h6"></path>
                        </svg>
                    </div>
                    <span class="admin-metric__index">03</span>
                </div>
                <div class="admin-metric__number">{{ $ringkasan['sksAmbil'] }}<small class="nilai-unit">SKS</small></div>
                <div class="admin-metric__label">Beban SKS Diikuti</div>
                <div class="admin-metric__caption">
                    {{ $ringkasan['mkTotal'] }} mata kuliah aktif.
                    {{ $ringkasan['mkLulus'] }} lulus, {{ $ringkasan['mkPerbaikan'] }} perlu perbaikan.
                </div>
            </article>

            <article class="admin-metric admin-metric--orange">
                <div class="admin-metric__top">
                    <div class="admin-metric__icon">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="12" cy="12" r="9"></circle>
                            <path d="M12 7v5l3 2"></path>
                        </svg>
                    </div>
                    <span class="admin-metric__index">04</span>
                </div>
                <div class="admin-metric__number">{{ $ringkasan['menunggu'] }}<small class="nilai-unit">tugas</small></div>
                <div class="admin-metric__label">Menunggu Penilaian</div>
                <div class="admin-metric__caption">Sudah kamu kumpulkan, belum diberi nilai oleh dosen.</div>
            </article>
        </section>

        {{-- FILTER --}}
        <form method="GET" action="{{ route('mahasiswa.nilai') }}" class="notif-filter nilai-filter">
            <label class="notif-search">
                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="M21 21l-4.3-4.3"></path>
                </svg>
                <input type="search" name="q" value="{{ $q }}" maxlength="100"
                       placeholder="Cari kode, mata kuliah, atau dosen..." aria-label="Cari mata kuliah">
            </label>

            <select name="status" class="notif-select" aria-label="Status">
                <option value="">Semua status</option>
                @foreach ($opsiStatus as $kunci => $label)
                    <option value="{{ $kunci }}" @selected($status === $kunci)>{{ $label }}</option>
                @endforeach
            </select>

            <button type="submit" class="notif-btn notif-btn--primary">Terapkan</button>

            @if ($filterAktif)
                <a href="{{ route('mahasiswa.nilai') }}" class="notif-btn">Reset</a>
            @endif
        </form>

        {{-- TABEL HASIL --}}
        <section class="nilai-card" aria-label="Daftar hasil evaluasi">
            <div class="nilai-card__head">
                <h2>Daftar Hasil Evaluasi</h2>
                <span>{{ $daftar->count() }} mata kuliah &middot; {{ $daftar->sum('sks') }} SKS</span>
            </div>
            <p class="nilai-card__hint">Klik baris untuk melihat rincian nilai tiap tugas dan feedback dosen.</p>

            @if ($daftar->isNotEmpty())
                <div class="nilai-table" role="table">
                    <div class="nilai-head" role="row">
                        <span>Kode</span>
                        <span>Mata Kuliah &amp; Dosen</span>
                        <span class="is-num">SKS</span>
                        <span class="is-num">Nilai</span>
                        <span class="is-center">Huruf</span>
                        <span class="is-num">Mutu</span>
                        <span>Status</span>
                        <span></span>
                    </div>

                    @foreach ($daftar as $mk)
                        <details class="nilai-row">
                            <summary class="nilai-row__sum">
                                <span class="nilai-cell nilai-cell--kode" data-label="Kode">{{ $mk['kode'] }}</span>
                                <span class="nilai-cell nilai-cell--mk" data-label="Mata Kuliah">
                                    <strong>{{ $mk['nama'] }}</strong>
                                    <small>{{ $mk['dosen'] }}</small>
                                </span>
                                <span class="nilai-cell is-num" data-label="SKS">{{ $mk['sks'] }}</span>
                                <span class="nilai-cell is-num" data-label="Nilai">{{ $mk['nilai'] !== null ? $fmt($mk['nilai']) : '-' }}</span>
                                <span class="nilai-cell is-center" data-label="Huruf">
                                    <span class="nilai-huruf nilai-huruf--{{ $kelasHuruf($mk['huruf']) }}">{{ $mk['huruf'] ?? '-' }}</span>
                                </span>
                                <span class="nilai-cell is-num" data-label="Mutu">
                                    @if ($mk['mutu'] !== null)
                                        {{ $fmt($mk['mutu']) }}
                                        <small>(Mutu: {{ $fmt($mk['mutu'] * $mk['sks']) }})</small>
                                    @else
                                        -
                                    @endif
                                </span>
                                <span class="nilai-cell" data-label="Status">
                                    <span class="notif-chip notif-chip--{{ $chipStatus[$mk['status']] }}">{{ $labelStatus[$mk['status']] }}</span>
                                </span>
                                <span class="nilai-chevron" aria-hidden="true">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"></path></svg>
                                </span>
                            </summary>

                            <div class="nilai-detail">
                                <p class="nilai-detail__title">
                                    Rincian tugas &middot; {{ $mk['dinilai'] }} dari {{ $mk['jumlahTugas'] }} tugas sudah dinilai
                                </p>

                                @forelse ($mk['rincian'] as $r)
                                    <div class="nilai-tugas">
                                        <div class="nilai-tugas__top">
                                            <strong>{{ $r['judul'] }}</strong>
                                            <span class="nilai-tugas__chips">
                                                @if ($r['terlambat'])
                                                    <span class="notif-chip notif-chip--bad">Terlambat</span>
                                                @endif
                                                <span class="notif-chip notif-chip--{{ $labelKondisi[$r['kondisi']][1] }}">{{ $labelKondisi[$r['kondisi']][0] }}</span>
                                            </span>
                                        </div>
                                        <div class="nilai-tugas__meta">
                                            <span>Batas {{ $r['batas'] }}</span>
                                            @if ($r['skor'] !== null)
                                                <span class="nilai-tugas__skor">{{ $r['skor'] }} / {{ $r['max'] }} ({{ $r['persen'] }}%)</span>
                                                <span>Dinilai {{ $r['dinilaiPada'] }}</span>
                                            @endif
                                        </div>
                                        @if ($r['skor'] !== null)
                                            <div class="notif-bar" role="img" aria-label="{{ $r['persen'] }} persen">
                                                <div class="notif-bar__fill" style="width: {{ min(100, $r['persen']) }}%;"></div>
                                            </div>
                                        @endif
                                        @if ($r['feedback'])
                                            <p class="notif-note">{{ $r['feedback'] }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <p class="nilai-detail__empty">Belum ada tugas terbit di mata kuliah ini.</p>
                                @endforelse

                                <a class="notif-btn" href="{{ route('mahasiswa.mata-kuliah.show', $mk['id']) }}">Buka Mata Kuliah</a>
                            </div>
                        </details>
                    @endforeach
                </div>

                <div class="nilai-total">
                    <div class="nilai-total__list">
                        <span>SKS diikuti: <strong>{{ $ringkasan['sksAmbil'] }}</strong></span>
                        <span>SKS bernilai: <strong>{{ $ringkasan['sksBernilai'] }}</strong></span>
                        <span>SKS lulus: <strong>{{ $ringkasan['sksLulus'] }}</strong></span>
                        <span>Total mutu (K &times; N): <strong>{{ $fmt($ringkasan['totalMutu']) }}</strong></span>
                    </div>
                    <div class="nilai-total__ips">
                        <small>IP Sementara</small>
                        <strong>{{ $ringkasan['ips'] !== null ? $fmt($ringkasan['ips']) : '-' }}</strong>
                    </div>
                </div>
            @else
                <div class="notif-empty">
                    <p>
                        @if ($jumlahSemua === 0)
                            Kamu belum terdaftar di mata kuliah aktif, jadi belum ada nilai untuk ditampilkan.
                        @else
                            Tidak ada mata kuliah yang cocok dengan filter ini.
                        @endif
                    </p>
                    @if ($filterAktif)
                        <a class="notif-btn" href="{{ route('mahasiswa.nilai') }}">Reset filter</a>
                    @endif
                </div>
            @endif
        </section>

        <p class="nilai-note">
            Nilai akhir adalah rata-rata nilai tugas yang sudah dinilai (dinormalkan ke skala 100), sehingga bisa berubah
            ketika dosen menilai tugas lain. Huruf dan bobot mutu mengikuti skala: A &ge; 85, A- &ge; 80, B+ &ge; 75, B &ge; 70,
            B- &ge; 65, C+ &ge; 60, C &ge; 55, D &ge; 40. Lulus bila nilai &ge; 55.
        </p>
    </section>
</x-layout>
