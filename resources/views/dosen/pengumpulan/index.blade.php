
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
            /*
             * Data berikut masih menggunakan data statis dari halaman lama.
             * Ganti dengan data database setelah struktur tabel dan relasinya
             * diketahui. Jangan gunakan angka ini sebagai laporan aktual.
             */
            $students = [
                [
                    'name' => 'Andi Nugraha',
                    'nim' => '10241001',
                    'assignment' => 'Praktikum Laravel',
                    'deadline' => '18 Okt 2026, 23.59',
                    'submitted_at' => '18 Okt 2026, 20.15',
                    'status' => 'submitted',
                ],
                [
                    'name' => 'Dina Safitri',
                    'nim' => '10241002',
                    'assignment' => 'Praktikum Laravel',
                    'deadline' => '18 Okt 2026, 23.59',
                    'submitted_at' => '19 Okt 2026, 08.30',
                    'status' => 'late',
                ],
                [
                    'name' => 'Rizky Pratama',
                    'nim' => '10241003',
                    'assignment' => 'Praktikum Laravel',
                    'deadline' => '18 Okt 2026, 23.59',
                    'submitted_at' => '—',
                    'status' => 'pending',
                ],
                [
                    'name' => 'Nadia Amelia',
                    'nim' => '10241004',
                    'assignment' => 'Praktikum Laravel',
                    'deadline' => '18 Okt 2026, 23.59',
                    'submitted_at' => '17 Okt 2026, 14.20',
                    'status' => 'submitted',
                ],
                [
                    'name' => 'Fajar Aditya',
                    'nim' => '10241005',
                    'assignment' => 'Praktikum Laravel',
                    'deadline' => '18 Okt 2026, 23.59',
                    'submitted_at' => '—',
                    'status' => 'pending',
                ],
            ];

            $totalStudents = 24;
            $submittedCount = 16;
            $pendingCount = 5;
            $lateCount = 3;
            $submissionPercentage = $totalStudents > 0
                ? round(($submittedCount / $totalStudents) * 100)
                : 0;
        @endphp

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
                <strong class="stat-number" data-count="{{ $totalStudents }}">0</strong>
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
                <strong class="stat-number" data-count="{{ $submittedCount }}">0</strong>
                <small>{{ $submissionPercentage }}% dari total mahasiswa</small>
                <div class="stat-progress">
                    <span style="width: {{ $submissionPercentage }}%"></span>
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
                <strong class="stat-number" data-count="{{ $pendingCount }}">0</strong>
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
                <strong class="stat-number" data-count="{{ $lateCount }}">0</strong>
                <small>Perlu diperiksa</small>
                <span class="stat-bottom-line"></span>
            </article>
        </section>

        <section class="submission-panel">
            <div class="submission-panel-heading">
                <div>
                    <span class="submission-eyebrow">MONITORING AKADEMIK</span>
                    <h2>Pengumpulan Mahasiswa</h2>
                    <p>Periksa status dan waktu pengumpulan tugas.</p>
                </div>

                <div class="submission-total">
                    <span class="total-indicator"></span>
                    <span id="visibleStudentCount">{{ count($students) }}</span>
                    mahasiswa ditampilkan
                </div>
            </div>

            <div class="submission-toolbar">
                <label class="submission-search">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle cx="11" cy="11" r="7"/>
                        <path d="m16 16 4 4"/>
                    </svg>
                    <input
                        type="search"
                        id="submissionSearch"
                        placeholder="Cari nama atau NIM mahasiswa..."
                        aria-label="Cari mahasiswa"
                    >
                </label>

                <label class="submission-filter-wrap">
                    <span>Filter status</span>
                    <select id="submissionFilter" aria-label="Filter status pengumpulan">
                        <option value="all">Semua status</option>
                        <option value="submitted">Sudah mengumpulkan</option>
                        <option value="pending">Belum mengumpulkan</option>
                        <option value="late">Terlambat</option>
                    </select>
                </label>
            </div>

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

                    <tbody id="submissionTableBody">
                        @foreach ($students as $student)
                            <tr data-status="{{ $student['status'] }}">
                                <td>
                                    <div class="student-cell">
                                        <span class="student-avatar">
                                            {{ collect(explode(' ', $student['name']))->map(fn ($part) => mb_substr($part, 0, 1))->take(2)->implode('') }}
                                        </span>
                                        <div>
                                            <strong>{{ $student['name'] }}</strong>
                                            <small>{{ $student['nim'] }}</small>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <span class="assignment-name">
                                        {{ $student['assignment'] }}
                                    </span>
                                </td>

                                <td>{{ $student['deadline'] }}</td>
                                <td>{{ $student['submitted_at'] }}</td>

                                <td>
                                    @if ($student['status'] === 'submitted')
                                        <span class="status-pill status-submitted">
                                            <span></span> Sudah mengumpulkan
                                        </span>
                                    @elseif ($student['status'] === 'late')
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

            <div id="submissionEmpty" class="submission-empty" hidden>
                <span class="empty-icon" aria-hidden="true">⌕</span>
                <strong>Data tidak ditemukan</strong>
                <p>Coba gunakan kata kunci atau filter status yang berbeda.</p>
            </div>

            <div class="submission-panel-footer">
                <span class="footer-status-dot"></span>
                <span>Ringkasan status pengumpulan tugas</span>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const search = document.getElementById('submissionSearch');
            const filter = document.getElementById('submissionFilter');
            const tableBody = document.getElementById('submissionTableBody');
            const empty = document.getElementById('submissionEmpty');
            const visibleCount = document.getElementById('visibleStudentCount');

            if (!search || !filter || !tableBody || !empty) {
                return;
            }

            const rows = Array.from(tableBody.querySelectorAll('tr'));

            function updateTable() {
                const keyword = search.value.trim().toLowerCase();
                const selectedStatus = filter.value;
                let count = 0;

                rows.forEach(function (row) {
                    const matchesKeyword = row.textContent.toLowerCase().includes(keyword);
                    const matchesStatus =
                        selectedStatus === 'all' ||
                        row.dataset.status === selectedStatus;

                    const visible = matchesKeyword && matchesStatus;
                    row.hidden = !visible;

                    if (visible) {
                        count++;
                    }
                });

                empty.hidden = count !== 0;
                visibleCount.textContent = count;
            }

            search.addEventListener('input', updateTable);
            filter.addEventListener('change', updateTable);

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

            updateTable();
        });
    </script>
</x-layout>
