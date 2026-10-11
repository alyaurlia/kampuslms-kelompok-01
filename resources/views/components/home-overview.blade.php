{{--
    Blok "Semester overview" + "Calendar" (dari tampilan dashboard awal).
    Dipakai di dosen/dashboard dan mahasiswa/dashboard.
    Style: resources/css/components/dashboard.css (.home-*, .semester-*, .kal-*)

    Props:
      $semester   : nama semester (string). Kosongkan (null) untuk menampilkan
                    daftar mata kuliah saja, tanpa judul semester.
      $mataKuliah : daftar mata kuliah, tiap item ['nama' => ..., 'kode' => ...]
                    dan opsional 'url' (nama menjadi tautan ke halaman detail)
      $events     : tanggal bertanda di kalender, format ['YYYY-MM-DD' => 'event'|'info']
      $showCalendar : tampilkan blok kalender? (bawaan true; false = hanya Semester overview,
                    kartunya melebar penuh)
      $emptyText  : teks bila daftar mata kuliah kosong (opsional; bawaan menyesuaikan
                    ada/tidaknya judul semester)
--}}
@props([
    'semester'   => 'Semester Gasal (A) 2026/2027',
    'mataKuliah' => [],
    'events'     => [],
    'emptyText'  => null,
    'showCalendar' => true,
])

<div class="home-layout home-overview {{ $showCalendar ? '' : 'home-layout--single' }}" style="margin-top: 2.5rem;">

    {{-- ===== Semester overview: kartu + dropdown daftar mata kuliah ===== --}}
    <section class="home-card home-card--semester so-card">
        <div class="so-head">
            <h2 class="home-card-title so-title">Semester overview</h2>
            <span class="so-count">{{ count($mataKuliah) }}</span>
        </div>

        {{-- Dropdown (tertutup bawaan). Judulnya nama semester; bila semester kosong
             dipakai teks "Daftar Mata Kuliah". Kotak cari di banner membuka dropdown ini otomatis. --}}
        <details class="semester-item so-drop">
            <summary class="semester-summary so-summary">
                <span class="so-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor"
                         stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16z"></path>
                        <path d="M4 5.5v16"></path><path d="M8 7h8"></path><path d="M8 11h6"></path>
                    </svg>
                </span>
                <span class="so-label">{{ filled($semester) ? $semester : 'Daftar Mata Kuliah' }}</span>
                <span class="so-meta">{{ count($mataKuliah) }} mata kuliah</span>
                <svg class="so-chevron" viewBox="0 0 24 24" width="20" height="20" fill="none"
                     stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                     stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
            </summary>

            @if (count($mataKuliah))
                <div class="semester-courses so-body">
                    @foreach ($mataKuliah as $mk)
                        <div class="semester-course">
                            @if (!empty($mk['url']))
                                <a href="{{ $mk['url'] }}" class="semester-course-name"
                                   style="color: inherit; text-decoration: none;">{{ $mk['nama'] }} - {{ $mk['kode'] }}</a>
                            @else
                                <span class="semester-course-name">{{ $mk['nama'] }} - {{ $mk['kode'] }}</span>
                            @endif
                            <span class="semester-course-star" aria-hidden="true">&#9734;</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="semester-empty so-body">
                    {{ $emptyText ?? (filled($semester) ? 'Belum ada mata kuliah pada semester ini.' : 'Belum ada mata kuliah yang kamu ikuti.') }}
                </p>
            @endif
        </details>
    </section>

    @if ($showCalendar)
    {{-- ===== Calendar ===== --}}
    <section class="home-card home-card--calendar">
        <h2 class="home-card-title">Calendar</h2>

        <div class="kal-nav">
            <button type="button" class="kal-nav-btn" id="kal-prev" aria-label="Bulan sebelumnya">&#8249;</button>
            <span class="kal-label" id="kal-label"></span>
            <button type="button" class="kal-nav-btn" id="kal-next" aria-label="Bulan berikutnya">&#8250;</button>
        </div>

        <div class="kal-days">
            @foreach (['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'] as $hari)
                <span class="kal-day">{{ $hari }}</span>
            @endforeach
        </div>

        <div id="kal-grid"></div>
    </section>
    @endif
</div>

@if ($showCalendar)
<script>
    (function () {
        const events = @json($events);
        const bulan = ['January', 'February', 'March', 'April', 'May', 'June', 'July',
                       'August', 'September', 'October', 'November', 'December'];

        const grid  = document.getElementById('kal-grid');
        const label = document.getElementById('kal-label');
        const now   = new Date();
        let tahun = now.getFullYear();
        let bln   = now.getMonth();

        const pad = (n) => String(n).padStart(2, '0');

        function sel(d, m, y, luar) {
            const el = document.createElement('span');
            el.className = 'kal-cell';
            el.textContent = d;

            if (luar) {
                el.classList.add('kal-cell--luar');
                return el;
            }

            const key = y + '-' + pad(m + 1) + '-' + pad(d);
            const hariIni = d === now.getDate() && m === now.getMonth() && y === now.getFullYear();

            if (hariIni) {
                el.classList.add('kal-cell--today');
            } else if (events[key]) {
                el.classList.add('kal-cell--' + events[key]);
            }
            return el;
        }

        function render() {
            label.textContent = bulan[bln] + ' ' + tahun;
            grid.innerHTML = '';

            // Senin = 0 ... Minggu = 6
            const awal   = (new Date(tahun, bln, 1).getDay() + 6) % 7;
            const jumlah = new Date(tahun, bln + 1, 0).getDate();
            const prevJumlah = new Date(tahun, bln, 0).getDate();

            const sel_ = [];
            for (let i = awal - 1; i >= 0; i--) sel_.push(sel(prevJumlah - i, bln - 1, tahun, true));
            for (let d = 1; d <= jumlah; d++)   sel_.push(sel(d, bln, tahun, false));
            for (let d = 1; sel_.length % 7 !== 0; d++) sel_.push(sel(d, bln + 1, tahun, true));

            for (let i = 0; i < sel_.length; i += 7) {
                const baris = document.createElement('div');
                baris.className = 'kal-row';
                sel_.slice(i, i + 7).forEach((c) => baris.appendChild(c));
                grid.appendChild(baris);
            }
        }

        document.getElementById('kal-prev').addEventListener('click', function () {
            bln--; if (bln < 0) { bln = 11; tahun--; } render();
        });
        document.getElementById('kal-next').addEventListener('click', function () {
            bln++; if (bln > 11) { bln = 0; tahun++; } render();
        });

        render();
    })();
</script>
@endif