
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

    <h1 style="font-size:1.6rem; margin-bottom:1.5rem;">
        Jumpa lagi, {{ $namaPengguna }}! 👋
    </h1>

    <div style="display:flex; gap:1.5rem; align-items:flex-start; flex-wrap:wrap;">

        {{-- ============ SEMESTER OVERVIEW ============ --}}
        <section style="flex:1 1 500px; background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem;">

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

        {{-- ============ CALENDAR (LIGHT, DINAMIS) ============ --}}
        <section id="kalender-card" style="flex:0 1 320px; background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem; font-family:Arial, sans-serif;">

            <h2 style="font-size:1.1rem; margin-top:0; margin-bottom:1rem;">Calendar</h2>

            {{-- Header navigasi bulan --}}
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1rem;">
                <button id="kalender-prev" type="button" style="cursor:pointer; background:none; border:none; color:var(--color-ink-soft); font-size:1.1rem; padding:0.25rem 0.5rem; line-height:1;">&#8249;</button>
                <span id="kalender-label" style="font-weight:600; font-size:0.95rem;"></span>
                <button id="kalender-next" type="button" style="cursor:pointer; background:none; border:none; color:var(--color-ink-soft); font-size:1.1rem; padding:0.25rem 0.5rem; line-height:1;">&#8250;</button>
            </div>

            {{-- Header hari --}}
            <div style="display:grid; grid-template-columns:repeat(7, 1fr); text-align:center; margin-bottom:0.5rem;">
                @foreach (['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'] as $h)
                    <span style="font-size:0.7rem; color:var(--color-ink-soft); font-weight:600;">{{ $h }}</span>
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

            function warnaTipe(tipe) {
                switch (tipe) {
                    case 'today': return '#22c55e';
                    case 'event': return '#ec4899';
                    case 'info':  return '#38bdf8';
                    default: return 'transparent';
                }
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
                    baris.style.display = 'grid';
                    baris.style.gridTemplateColumns = 'repeat(7, 1fr)';
                    baris.style.textAlign = 'center';
                    baris.style.marginBottom = '0.35rem';

                    sel.slice(i, i + 7).forEach(item => {
                        const span = document.createElement('span');
                        span.textContent = item.tgl;

                        let tipe = null;
                        if (!item.luarBulan) {
                            const tglStr = formatTanggal(tampilTahun, tampilBulan, item.tgl);
                            if (tglStr === todayStr) {
                                tipe = 'today';
                            } else {
                                const ev = cariEvent(tglStr);
                                if (ev) tipe = ev.tipe;
                            }
                        }

                        span.style.display = 'inline-flex';
                        span.style.alignItems = 'center';
                        span.style.justifyContent = 'center';
                        span.style.width = '28px';
                        span.style.height = '28px';
                        span.style.margin = '0 auto';
                        span.style.borderRadius = '50%';
                        span.style.fontSize = '0.8rem';
                        span.style.fontWeight = tipe ? '700' : '400';
                        span.style.background = warnaTipe(tipe);
                        span.style.color = item.luarBulan
                            ? '#c9ccd1'
                            : (tipe ? '#ffffff' : 'var(--color-ink)');

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