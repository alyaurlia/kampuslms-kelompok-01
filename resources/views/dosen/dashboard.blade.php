
{{--
    Dashboard dosen: ringkasan mata kuliah yang diampu user yang sedang login.
--}}
@php
    $mkDiampu = \App\Models\Course::where('lecturer_id', auth()->id());

    $totalMk = (clone $mkDiampu)->count();
    $totalAktif = (clone $mkDiampu)->where('status', 'active')->count();
    $totalPeserta = (clone $mkDiampu)->withCount('students')->get()->sum('students_count');

    $tugasDosen = \App\Models\Assignment::whereIn(
        'course_id',
        (clone $mkDiampu)->select('id')
    );

    $totalTugas = (clone $tugasDosen)->count();
    $tugasPublished = (clone $tugasDosen)->where('status', 'published')->count();
    $tugasDraft = (clone $tugasDosen)->where('status', 'draft')->count();

    $persentasePublished = $totalTugas > 0
        ? round(($tugasPublished / $totalTugas) * 100)
        : 0;

    $cards = [
        [
            'no' => 1,
            'ikon' => 'buku',
            'judul' => 'Mata Kuliah Diampu',
            'nilai' => $totalMk,
            'desc' => 'Total mata kuliah yang kamu ampu pada semester berjalan.',
        ],
        [
            'no' => 2,
            'ikon' => 'cek',
            'judul' => 'Mata Kuliah Aktif',
            'nilai' => $totalAktif,
            'desc' => 'Mata kuliah yang saat ini sedang berlangsung dan terbuka untuk mahasiswa.',
        ],
        [
            'no' => 3,
            'ikon' => 'orang',
            'judul' => 'Total Peserta',
            'nilai' => $totalPeserta,
            'desc' => 'Jumlah peserta yang terdaftar di seluruh mata kuliah yang kamu ampu.',
        ],
    ];

    $courses = (clone $mkDiampu)->orderBy('name')->get(['id', 'code', 'name', 'sks']);

    $mataKuliahSemester = $courses->map(fn ($c) => [
        'nama' => $c->name,
        'kode' => $c->code,
        'url' => route('dosen.mata-kuliah.show', $c),
    ])->all();

    $ikonSvg = [
        'buku' => '<path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16z"></path><path d="M4 5.5v16"></path><path d="M8 7h8"></path><path d="M8 11h6"></path>',
        'cek' => '<circle cx="12" cy="12" r="9"></circle><path d="M8 12.5l3 3 5-6"></path>',
        'orang' => '<circle cx="9" cy="7" r="4"></circle><path d="M3 21v-2a6 6 0 0 1 12 0v2"></path><path d="M16 3.5a4 4 0 0 1 0 7"></path><path d="M18 14a5 5 0 0 1 3 4.6V21"></path>',
    ];

    $warna = ['pink', 'purple', 'blue'];
@endphp

<x-layout title="Dashboard Dosen" role="dosen" full-footer>
    <section class="dash">
        <x-dash-hero
            portal="Portal Dosen"
            title="Dashboard Dosen"
            :bell="false"
            search-label="Cari mata kuliah..."
        />

        <div class="admin-metrics admin-metrics--flow">
            @foreach ($cards as $card)
                <article class="admin-metric admin-metric--{{ $warna[$loop->index % count($warna)] }}">
                    <div class="admin-metric__top">
                        <div class="admin-metric__icon">
                            <svg viewBox="0 0 24 24" aria-hidden="true">
                                {!! $ikonSvg[$card['ikon']] !!}
                            </svg>
                        </div>
                        <span class="admin-metric__index">
                            {{ sprintf('%02d', $card['no']) }}
                        </span>
                    </div>

                    <div class="admin-metric__number">{{ $card['nilai'] }}</div>
                    <div class="admin-metric__label">{{ $card['judul'] }}</div>
                    <div class="admin-metric__caption">{{ $card['desc'] }}</div>
                </article>
            @endforeach
        </div>

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

    <x-home-overview
        semester=""
        :mata-kuliah="$mataKuliahSemester"
        :show-calendar="false"
        empty-text="Belum ada mata kuliah yang kamu ampu."
    />
</x-layout>
