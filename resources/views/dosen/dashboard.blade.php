{{--
    Dashboard dosen: ringkasan mata kuliah yang diampu user yang sedang login.
    Tampilan kartu memakai gaya .admin-metric (sama seperti dashboard admin)
    (didefinisikan di resources/css/components/dashboard.css).
--}}
@php
    $mkDiampu = \App\Models\Course::where('lecturer_id', auth()->id());

    $totalMk      = (clone $mkDiampu)->count();
    $totalAktif   = (clone $mkDiampu)->where('status', 'active')->count();
    $totalPeserta = (clone $mkDiampu)->withCount('students')->get()->sum('students_count');

    // Seluruh mahasiswa yang terdata di sistem (sama dengan kartu Mahasiswa di dashboard admin).
    $totalMahasiswa = \App\Models\User::where('role', 'mahasiswa')->count();

    // Pengumpulan pada tugas di mata kuliah yang diampu dosen ini yang belum punya nilai.
    // Angkanya turun sendiri begitu pengumpulan dinilai.
    $menungguNilai = \App\Models\Submission::query()
        ->whereIn('assignment_id',
            \App\Models\Assignment::whereIn('course_id', (clone $mkDiampu)->select('id'))->select('id'))
        ->whereDoesntHave('grade')
        ->count();

    // Status tugas pada mata kuliah yang diampu dosen ini (kartu Pengelolaan Tugas).
    // Hanya tugas dari mata kuliah miliknya, bukan seluruh tugas di sistem.
    $tugasDosen = \App\Models\Assignment::whereIn('course_id', (clone $mkDiampu)->select('id'));

    $totalTugas     = (clone $tugasDosen)->count();
    $tugasPublished = (clone $tugasDosen)->where('status', 'published')->count();
    $tugasDraft     = (clone $tugasDosen)->where('status', 'draft')->count();

    $persentasePublished = $totalTugas > 0
        ? round(($tugasPublished / $totalTugas) * 100)
        : 0;

    // Data kartu (semua angka berasal dari database).
    $cards = [
        [
            'no'    => 1,
            'ikon'  => 'buku',
            'judul' => 'Mata Kuliah Diampu',
            'nilai' => $totalMk,
            'desc'  => 'Total mata kuliah yang kamu ampu pada semester berjalan.',
        ],
        [
            'no'    => 2,
            'ikon'  => 'cek',
            'judul' => 'Mata Kuliah Aktif',
            'nilai' => $totalAktif,
            'desc'  => 'Mata kuliah yang saat ini sedang berlangsung dan terbuka untuk mahasiswa.',
        ],
        [
            'no'    => 3,
            'ikon'  => 'orang',
            'judul' => 'Total Peserta',
            'nilai' => $totalPeserta,
            'desc'  => 'Jumlah peserta yang terdaftar di seluruh mata kuliah yang kamu ampu.',
        ],
        [
            'no'    => 4,
            'ikon'  => 'topi',
            'judul' => 'Total Mahasiswa',
            'nilai' => $totalMahasiswa,
            'desc'  => 'Jumlah seluruh mahasiswa yang terdata di dalam sistem LMS Kampus.',
        ],
        [
            'no'    => 5,
            'ikon'  => 'berkas',
            'id'    => 'tugas-menunggu-nilai',
            'judul' => 'Tugas Menunggu Nilai',
            'nilai' => $menungguNilai,
            'desc'  => 'Pengumpulan tugas mahasiswa yang belum kamu beri penilaian.',
        ],
    ];
    // Blok "Semester overview": mata kuliah yang diampu dosen ini, sama seperti
    // dashboard mahasiswa (tanpa judul semester, nama menjadi tautan ke detail).
    // Untuk hanya menampilkan yang aktif: tambahkan ->where('status', 'active').
    $courses = (clone $mkDiampu)->orderBy('name')->get(['id', 'code', 'name', 'sks']);

    $mataKuliahSemester = $courses->map(fn ($c) => [
        'nama' => $c->name,
        'kode' => $c->code,
        'url'  => route('dosen.mata-kuliah.show', $c),
    ])->all();
    // Ikon kartu (gambar garis, warna ikut aksen kartu)
    $ikonSvg = [
        'buku'  => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16z"></path><path d="M4 5.5v16"></path><path d="M8 7h8"></path><path d="M8 11h6"></path>',
        'cek'   => '<circle cx="12" cy="12" r="9"></circle><path d="M8 12.5l3 3 5-6"></path>',
        'orang' => '<circle cx="9" cy="7" r="4"></circle><path d="M3 21v-2a6 6 0 0 1 12 0v2"></path><path d="M16 3.5a4 4 0 0 1 0 7"></path><path d="M18 14a5 5 0 0 1 3 4.6V21"></path>',
        'topi'  => '<path d="M2 10l10-5 10 5-10 5L2 10z"></path><path d="M6 12.5V17c2.5 2 9.5 2 12 0v-4.5"></path><path d="M22 10v6"></path>',
        'berkas'=> '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><path d="M8 13h8"></path><path d="M8 17h5"></path>',
        'bintang'=> '<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"></path>',
        'tumpuk'=> '<path d="M12 3l9 5-9 5-9-5 9-5z"></path><path d="M3 13l9 5 9-5"></path>',
    ];
    $warna = ['pink', 'purple', 'blue', 'orange', 'pink'];
@endphp

<x-layout title="Dashboard Dosen" role="dosen" full-footer>
    <section class="dash">
        <x-dash-hero portal="Portal Dosen" title="Dashboard Dosen" :notif="$cards[4]['nilai']" notif-href="#tugas-menunggu-nilai" notif-label="Tugas Menunggu Nilai" search-label="Cari mata kuliah..." />

        <div class="admin-metrics admin-metrics--flow">
            @foreach ($cards as $card)
                <article @if (isset($card['id'])) id="{{ $card['id'] }}" @endif class="admin-metric admin-metric--{{ $warna[$loop->index % count($warna)] }}">
                    <div class="admin-metric__top">
                        <div class="admin-metric__icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">{!! $ikonSvg[$card['ikon']] !!}</svg>
                        </div>
                        <span class="admin-metric__index">{{ sprintf('%02d', $card['no']) }}</span>
                    </div>

                    <div class="admin-metric__number">{{ $card['nilai'] }}</div>
                    <div class="admin-metric__label">{{ $card['judul'] }}</div>
                    <div class="admin-metric__caption">{{ $card['desc'] }}</div>
                </article>
            @endforeach
        </div>

        {{-- PENGELOLAAN TUGAS (dipindah dari dashboard admin) --}}
        <section class="admin-task-panel">
            <div class="admin-task-panel__heading">
                <div>
                    <p class="admin-section-eyebrow">PENGELOLAAN</p>
                    <h2 class="admin-section-title">Tugas</h2>
                    <p class="admin-section-description">
                        Melihat tugas di mata kuliah yang kamu ampu: yang sudah siap dikerjakan dan yang masih disimpan sebagai draf.
                    </p>
                </div>

                <div class="admin-task-total">
                    <span>Jumlah tugas</span>
                    <strong>{{ $totalTugas }}</strong>
                </div>
            </div>

            <div class="admin-task-progress">
                <div class="admin-task-progress__top">
                    <span>Tugas yang sudah diterbitkan</span>
                    <strong>{{ $persentasePublished }}%</strong>
                </div>
                <div class="admin-task-progress__track">
                    <div
                        class="admin-task-progress__bar"
                        style="width: {{ $persentasePublished }}%;"
                    ></div>
                </div>
            </div>

            <div class="admin-task-row">
                <div class="admin-task-row__identity">
                    <div class="admin-task-row__icon admin-task-row__icon--published">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M20 6L9 17l-5-5"></path>
                        </svg>
                    </div>
                    <div>
                        <h3>Tugas diterbitkan</h3>
                        <p>Sudah dapat dilihat dan dikerjakan mahasiswa.</p>
                    </div>
                </div>
                <div class="admin-task-row__count">{{ $tugasPublished }}</div>
            </div>

            <div class="admin-task-row admin-task-row--draft">
                <div class="admin-task-row__identity">
                    <div class="admin-task-row__icon admin-task-row__icon--draft">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M12 20h9"></path>
                            <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3>Tersimpan sebagai draf</h3>
                        <p>Masih disiapkan dan belum diberikan kepada mahasiswa.</p>
                    </div>
                </div>
                <div class="admin-task-row__count">{{ $tugasDraft }}</div>
            </div>
        </section>
    </section>

    {{-- semester="" (string kosong) = tanpa judul semester, hanya daftar mata kuliah.
         Jangan pakai null: null membuat @props memakai nilai bawaan komponen. --}}
    <x-home-overview
        semester=""
        :mata-kuliah="$mataKuliahSemester"
        :show-calendar="false"
        empty-text="Belum ada mata kuliah yang kamu ampu."
    />
</x-layout>