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
    ];

    // --- Data event untuk kalender (dikirim ke JS) ---
    // format tanggal: 'YYYY-MM-DD'
    // tipe: 'today' (hijau), 'event' (pink), 'info' (biru)
    $eventKalender = [
        ['tanggal' => '2026-09-07', 'tipe' => 'event'],
        ['tanggal' => '2026-09-23', 'tipe' => 'info'],
    ];
@endphp

<x-layout title="Dasbor">

    <h1 class="home-title">
        Jumpa lagi, {{ $namaPengguna }}! 👋
    </h1>

    <div class="home-layout">

        {{-- ============ SEMESTER OVERVIEW ============ --}}
        <section class="home-card home-card--semester">

            <h2 class="home-card-title">Semester overview</h2>

            @foreach ($semesters as $semester)
                <details @if ($semester['aktif']) open @endif class="semester-item">

                    <summary class="semester-summary">
                        {{ $semester['label'] }}
                    </summary>

                    @if (count($semester['courses']) === 0)
                        <p class="semester-empty">
                            Belum ada data mata kuliah untuk semester ini.
                        </p>
                    @else
                        <div class="semester-courses">
                            @foreach ($semester['courses'] as $course)
                                <div class="semester-course">

                                    <span class="semester-course-name">
                                        {{ $course['nama'] }} - {{ $course['kode'] }}
                                    </span>

                                    <span aria-hidden="true" class="semester-course-star">&#9734;</span>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </details>
            @endforeach

        </section>

        {{-- ============ CALENDAR (LIGHT, DINAMIS) ============ --}}
        <section id="kalender-card" class="home-card home-card--calendar">

            <h2 class="home-card-title">Calendar</h2>

            {{-- Header navigasi bulan --}}
            <div class="kal-nav">
                <button id="kalender-prev" type="button" class="kal-nav-btn">&#8249;</button>
                <span id="kalender-label" class="kal-label"></span>
                <button id="kalender-next" type="button" class="kal-nav-btn">&#8250;</button>
            </div>

            {{-- Header hari --}}
            <div class="kal-days">
                @foreach (['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'] as $h)
                    <span class="kal-day">{{ $h }}</span>
                @endforeach
            </div>

            {{-- Grid tanggal diisi via JavaScript --}}
            <div id="kalender-grid"></div>

        </section>

    </div>

    <script>
        (function () {
            // Data event dari server (Blade) -> JS
            const eventData = {!! json_encode($eventKalender) !!};

            const namaBulan = [
                'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
                'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
            ];

            // State bulan/tahun yang sedang ditampilkan
            let tampilBulan = 8;  // 0-based, 8 = September
            let tampilTahun = 2026;

            const elLabel = document.getElementById('kalender-label');
            const elGrid = document.getElementById('kalender-grid');
            const elPrev = document.getElementById('kalender-prev');
            const elNext = document.getElementById('kalender-next');

            function formatTanggal(tahun, bulan, tanggal) {
                const bb = String(bulan + 1).padStart(2, '0');
                const dd = String(tanggal).padStart(2, '0');
                return `${tahun}-${bb}-${dd}`;
            }

            function cariEvent(tanggalStr) {
                return eventData.find(e => e.tanggal === tanggalStr);
            }

            function renderKalender() {
                elLabel.textContent = `${namaBulan[tampilBulan]} ${tampilTahun}`;
                elGrid.innerHTML = '';

                const today = new Date();
                const todayStr = formatTanggal(today.getFullYear(), today.getMonth(), today.getDate());

                // Hari pertama bulan ini, dikonversi supaya Senin = 0
                const firstDay = new Date(tampilTahun, tampilBulan, 1);
                let startOffset = firstDay.getDay() - 1; // Minggu=0 -> jadi -1, kita geser
                if (startOffset < 0) startOffset = 6;

                const jumlahHariBulanIni = new Date(tampilTahun, tampilBulan + 1, 0).getDate();
                const jumlahHariBulanLalu = new Date(tampilTahun, tampilBulan, 0).getDate();

                // Bangun array tanggal untuk grid (termasuk tanggal luar bulan)
                const sel = [];
                for (let i = 0; i < startOffset; i++) {
                    sel.push({ tgl: jumlahHariBulanLalu - startOffset + 1 + i, luarBulan: true });
                }
                for (let d = 1; d <= jumlahHariBulanIni; d++) {
                    sel.push({ tgl: d, luarBulan: false });
                }
                while (sel.length % 7 !== 0) {
                    sel.push({ tgl: sel.length - startOffset - jumlahHariBulanIni + 1, luarBulan: true });
                }

                // Render per baris (7 kolom)
                for (let i = 0; i < sel.length; i += 7) {
                    const baris = document.createElement('div');
                    baris.className = 'kal-row';

                    sel.slice(i, i + 7).forEach(item => {
                        const span = document.createElement('span');
                        span.textContent = item.tgl;
                        span.className = 'kal-cell';

                        if (item.luarBulan) {
                            span.classList.add('kal-cell--luar');
                        } else {
                            const tglStr = formatTanggal(tampilTahun, tampilBulan, item.tgl);
                            let tipe = null;

                            if (tglStr === todayStr) {
                                tipe = 'today';
                            } else {
                                const ev = cariEvent(tglStr);
                                if (ev) tipe = ev.tipe;
                            }

                            // tipe: 'today' | 'event' | 'info' (lihat .kal-cell--* di dashboard.css)
                            if (tipe) span.classList.add('kal-cell--' + tipe);
                        }

                        baris.appendChild(span);
                    });

                    elGrid.appendChild(baris);
                }
            }

            elPrev.addEventListener('click', function () {
                tampilBulan--;
                if (tampilBulan < 0) {
                    tampilBulan = 11;
                    tampilTahun--;
                }
                renderKalender();
            });

            elNext.addEventListener('click', function () {
                tampilBulan++;
                if (tampilBulan > 11) {
                    tampilBulan = 0;
                    tampilTahun++;
                }
                renderKalender();
            });

            renderKalender();
        })();
    </script>

</x-layout>