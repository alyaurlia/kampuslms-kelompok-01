{{--
    Variabel dari SubmissionListController@index:
      $assignments : tugas published milik dosen (dengan course, submissions_count)
      $selected    : tugas yang dipilih, atau null
      $rows        : paginator { student, submission, status } atau null
      $stats       : ['total', 'submitted', 'belum', 'late'] atau null
      $status      : filter status aktif (semua|sudah|belum|terlambat)
      $q           : kata kunci pencarian
--}}
<x-layout title="Daftar Pengumpulan" role="dosen" full-footer>
    <div class="submission-page">
        <header class="submission-hero">
            <div class="submission-hero-content">
                <span class="submission-eyebrow">
                    <span class="eyebrow-dot"></span>
                    PORTAL DOSEN
                </span>

                <h1>Daftar Pengumpulan</h1>

                <p>
                    Pantau progres pengumpulan tugas mahasiswa dengan lebih mudah.
                </p>

                <div class="hero-decoration">
                    <span class="hero-decoration-dot"></span>
                    <span class="hero-decoration-dot"></span>
                    <span class="hero-decoration-dot"></span>
                </div>
            </div>

            <a href="{{ route('dosen.dashboard') }}" class="submission-back">
                <span aria-hidden="true">&larr;</span>
                Kembali ke Dashboard
            </a>

            <span class="hero-orb hero-orb-one"></span>
            <span class="hero-orb hero-orb-two"></span>
            <span class="hero-orb hero-orb-three"></span>
        </header>

        @php
            $pct = ($stats && $stats['total'] > 0)
                ? round(($stats['submitted'] / $stats['total']) * 100)
                : 0;
        @endphp

        {{-- Ringkasan: hanya tampil setelah satu tugas dipilih --}}
        @if ($stats)
            <section class="submission-stats" aria-label="Ringkasan pengumpulan">
                <article class="submission-stat stat-total">
                    <div class="stat-top">
                        <span class="stat-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="4" y="3" width="16" height="18" rx="3"/>
                                <path d="M8 8h8M8 12h8M8 16h5"/>
                            </svg>
                        </span>
                        <span class="stat-tag">Keseluruhan</span>
                    </div>

                    <span class="stat-label">Total Mahasiswa</span>
                    <strong class="stat-number" data-count="{{ $stats['total'] }}">0</strong>
                    <small>Mahasiswa terdaftar</small>
                    <span class="stat-bottom-line"></span>
                </article>

                <article class="submission-stat stat-submitted">
                    <div class="stat-top">
                        <span class="stat-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="m5 12 4 4L19 6"/>
                                <circle cx="12" cy="12" r="9"/>
                            </svg>
                        </span>
                        <span class="stat-tag">Selesai</span>
                    </div>

                    <span class="stat-label">Sudah Mengumpulkan</span>
                    <strong class="stat-number" data-count="{{ $stats['submitted'] }}">0</strong>
                    <small>{{ $pct }}% dari total mahasiswa (termasuk yang terlambat)</small>
                    <div class="stat-progress">
                        <span style="width: {{ $pct }}%"></span>
                    </div>
                </article>

                <article class="submission-stat stat-pending">
                    <div class="stat-top">
                        <span class="stat-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="9"/>
                                <path d="M12 7v5l3 2"/>
                            </svg>
                        </span>
                        <span class="stat-tag">Menunggu</span>
                    </div>

                    <span class="stat-label">Belum Mengumpulkan</span>
                    <strong class="stat-number" data-count="{{ $stats['belum'] }}">0</strong>
                    <small>Menunggu pengumpulan</small>
                    <span class="stat-bottom-line"></span>
                </article>

                <article class="submission-stat stat-late">
                    <div class="stat-top">
                        <span class="stat-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 3 22 20H2L12 3Z"/>
                                <path d="M12 9v5m0 3h.01"/>
                            </svg>
                        </span>
                        <span class="stat-tag">Perhatian</span>
                    </div>

                    <span class="stat-label">Terlambat</span>
                    <strong class="stat-number" data-count="{{ $stats['late'] }}">0</strong>
                    <small>Perlu diperiksa</small>
                    <span class="stat-bottom-line"></span>
                </article>
            </section>
        @endif

        <section class="submission-panel">
            <div class="submission-panel-heading">
                <div>
                    <span class="submission-eyebrow">MONITORING AKADEMIK</span>
                    <h2>Pengumpulan Mahasiswa</h2>
                    @if ($selected)
                        <p>
                            {{ $selected->title }} &middot; {{ $selected->course->name }}
                            &middot; Tenggat:
                            {{ $selected->due_at?->translatedFormat('d M Y, H.i') ?? 'Tanpa batas waktu' }}
                            &middot;
                            <a href="{{ route('dosen.tugas.show', $selected) }}">Buka tugas &amp; beri nilai</a>
                        </p>
                    @else
                        <p>Pilih tugas untuk melihat status pengumpulan.</p>
                    @endif
                </div>

                @if ($rows)
                    <div class="submission-total">
                        <span class="total-indicator"></span>
                        <span>{{ $rows->count() }}</span>
                        dari {{ $rows->total() }} mahasiswa ditampilkan
                    </div>
                @endif
            </div>

            {{-- Semua kontrol memakai GET ke server, supaya hasilnya benar walau data banyak halaman. --}}
            <form method="GET" action="{{ route('dosen.pengumpulan.index') }}" class="submission-toolbar">
                <label class="submission-filter-wrap">
                    <span>Tugas</span>
                    <select name="tugas" aria-label="Pilih tugas" onchange="this.form.submit()">
                        <option value="">— Pilih tugas —</option>
                        @foreach ($assignments as $assignment)
                            <option value="{{ $assignment->id }}" @selected($selected && $selected->id === $assignment->id)>
                                {{ $assignment->course->code }} &middot; {{ $assignment->title }}
                                ({{ $assignment->submissions_count }})
                            </option>
                        @endforeach
                    </select>
                </label>

                @if ($selected)
                    <label class="submission-search">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"/>
                            <path d="m16 16 4 4"/>
                        </svg>
                        <input
                            type="search"
                            name="q"
                            value="{{ $q }}"
                            maxlength="100"
                            placeholder="Cari nama atau NIM, lalu tekan Enter..."
                            aria-label="Cari mahasiswa"
                        >
                    </label>

                    <label class="submission-filter-wrap">
                        <span>Filter status</span>
                        <select name="status" aria-label="Filter status pengumpulan" onchange="this.form.submit()">
                            <option value="semua" @selected($status === 'semua')>Semua status</option>
                            <option value="sudah" @selected($status === 'sudah')>Sudah mengumpulkan</option>
                            <option value="belum" @selected($status === 'belum')>Belum mengumpulkan</option>
                            <option value="terlambat" @selected($status === 'terlambat')>Terlambat</option>
                        </select>
                    </label>
                @endif

                <noscript><button type="submit">Terapkan</button></noscript>
            </form>

            @if ($assignments->isEmpty())
                <div class="submission-empty">
                    <span class="empty-icon" aria-hidden="true">⌕</span>
                    <strong>Belum ada tugas yang dipublikasikan</strong>
                    <p>Tugas berstatus draft tidak muncul di sini.</p>
                </div>

            @elseif (! $selected)
                <div class="submission-empty">
                    <span class="empty-icon" aria-hidden="true">⌕</span>
                    <strong>Pilih tugas terlebih dahulu</strong>
                    <p>Gunakan pilihan "Tugas" di atas untuk melihat siapa yang sudah dan belum mengumpulkan.</p>
                </div>

            @elseif ($rows->isEmpty())
                <div class="submission-empty">
                    <span class="empty-icon" aria-hidden="true">⌕</span>
                    <strong>Data tidak ditemukan</strong>
                    <p>Coba gunakan kata kunci atau filter status yang berbeda.</p>
                </div>

            @else
                <div class="submission-table-wrapper">
                    <table class="submission-table">
                        <thead>
                            <tr>
                                <th>MAHASISWA</th>
                                <th>NAMA TUGAS</th>
                                <th>TENGGAT</th>
                                <th>WAKTU PENGUMPULAN</th>
                                <th>STATUS</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($rows as $row)
                                <tr>
                                    <td>
                                        <div class="student-cell">
                                            <span class="student-avatar">
                                                {{ collect(preg_split('/\s+/', trim($row->student->name)))->filter()->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->take(2)->implode('') }}
                                            </span>
                                            <div>
                                                <strong>{{ $row->student->name }}</strong>
                                                <small>{{ $row->student->nim_nip }}</small>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="assignment-name">{{ $selected->title }}</span>
                                    </td>

                                    <td>{{ $selected->due_at?->translatedFormat('d M Y, H.i') ?? '—' }}</td>

                                    <td>
                                        @if ($row->submission)
                                            {{ $row->submission->submitted_at->translatedFormat('d M Y, H.i') }}
                                            <br>
                                            <small>
                                                <a href="{{ route('pengumpulan.unduh', $row->submission) }}">{{ $row->submission->original_name }}</a>
                                                @if ($row->submission->file_size_human)
                                                    &middot; {{ $row->submission->file_size_human }}
                                                @endif
                                            </small>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td>
                                        @if ($row->status === 'tepat_waktu')
                                            <span class="status-pill status-submitted">
                                                <span></span> Sudah mengumpulkan
                                            </span>
                                        @elseif ($row->status === 'terlambat')
                                            <span class="status-pill status-late">
                                                <span></span> Terlambat
                                            </span>
                                        @else
                                            <span class="status-pill status-pending">
                                                <span></span> Belum mengumpulkan
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($rows->hasPages())
                    <div class="submission-pagination">
                        {{ $rows->links() }}
                    </div>
                @endif
            @endif

            <div class="submission-panel-footer">
                <span class="footer-status-dot"></span>
                <span>Ringkasan status pengumpulan tugas</span>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Animasi angka pada kartu ringkasan.
            document.querySelectorAll('.stat-number').forEach(function (element) {
                const target = Number(element.dataset.count || 0);
                const duration = 700;
                const startTime = performance.now();

                function animateNumber(now) {
                    const progress = Math.min((now - startTime) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);

                    element.textContent = Math.round(target * eased);

                    if (progress < 1) {
                        requestAnimationFrame(animateNumber);
                    }
                }

                requestAnimationFrame(animateNumber);
            });
        });
    </script>
</x-layout>