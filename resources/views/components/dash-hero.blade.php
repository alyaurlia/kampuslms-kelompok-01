{{--
    Banner atas dashboard (dosen & mahasiswa).
    Pemakaian:
      <x-dash-hero portal="Portal Dosen" title="Dashboard Dosen" :notif="18" search-label="Cari mata kuliah..." />
    Props:
      portal       : teks kecil di atas judul
      title        : judul halaman (h1)
      notif        : angka badge lonceng (kosongkan/null = tanpa badge)
      searchLabel  : placeholder kotak cari
      target       : selector CSS elemen yang difilter kotak cari
                     (default: daftar mata kuliah di Semester overview)
      sub          : kalimat di bawah sapaan
      status       : teks status kecil dengan titik hijau (mis. "Sistem aktif"), opsional
--}}
@props([
    'portal'      => 'Portal',
    'title'       => 'Dashboard',
    'notif'       => null,
    'searchLabel' => 'Cari mata kuliah...',
    'sub'         => 'Semoga harimu menyenangkan!',
    'status'      => null,
    'target'      => '.home-overview .semester-course',
])

@php
    $namaUser = auth()->user()?->name ?? 'Pengguna';
    $inisial  = collect(preg_split('/\s+/', trim($namaUser)))
        ->filter()->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->take(2)->join('');
    $namaDepan = explode(' ', trim($namaUser))[0];
@endphp

<header class="dash-hero">
    <div class="dash-hero__top">
        <div>
            <p class="dash-hero__portal">{{ $portal }}</p>
            <h1 class="dash-hero__title">{{ $title }}</h1>
            @if ($status)
                <span class="dash-hero__status"><i></i>{{ $status }}</span>
            @endif
        </div>

        <div class="dash-hero__actions">
            <button type="button" class="dash-hero__bell" aria-label="Notifikasi">
                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                    <path d="M13.7 21a2 2 0 01-3.4 0"/>
                </svg>
                @if ($notif)
                    <span class="dash-hero__badge">{{ $notif > 99 ? '99+' : $notif }}</span>
                @endif
            </button>
            <div class="dash-hero__avatar" title="{{ $namaUser }}">{{ $inisial }}</div>
        </div>
    </div>

    <p class="dash-hero__hello">Halo, {{ $namaDepan }} <span aria-hidden="true">👋</span></p>
    <p class="dash-hero__sub">{{ $sub }}</p>

    <div class="dash-hero__search" role="search">
        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" aria-hidden="true">
            <circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/>
        </svg>
        <input type="search" id="dash-hero-search" placeholder="{{ $searchLabel }}"
               aria-label="{{ $searchLabel }}" autocomplete="off">
    </div>
</header>

<script>
    // Filter daftar mata kuliah di blok "Semester overview" saat mengetik.
    (function () {
        var input = document.getElementById('dash-hero-search');
        var target = @json($target);
        if (!input) return;
        input.addEventListener('input', function () {
            var q = input.value.trim().toLowerCase();
            document.querySelectorAll(target).forEach(function (el) {
                el.hidden = q !== '' && el.textContent.toLowerCase().indexOf(q) === -1;
            });
            if (q !== '') {
                document.querySelectorAll('.home-overview details').forEach(function (d) { d.open = true; });
            }
        });
    })();
</script>