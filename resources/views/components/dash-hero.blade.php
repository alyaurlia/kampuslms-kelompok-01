{{--
    Banner atas dashboard (admin, dosen & mahasiswa).
    Pemakaian:
      <x-dash-hero portal="Portal Dosen" title="Dashboard Dosen" :live="true" search-label="Cari mata kuliah..." />
    Props:
      portal       : teks kecil di atas judul
      title        : judul halaman (h1)
      bell         : false = lonceng di banner disembunyikan (dipakai dashboard admin,
                     yang lonceng-nya sudah ada di navbar)
      live         : true = angka lonceng adalah jumlah notifikasi belum dibaca milik
                     user yang login, dan ikut diperbarui oleh polling milik
                     <x-notification-bell> di navbar. Isi `notif` diabaikan.
      notif        : angka badge lonceng bila `live` = false (kosongkan/null = tanpa badge)
      searchLabel  : placeholder kotak cari
      target       : selector CSS elemen yang difilter kotak cari
                     (default: daftar mata kuliah di Semester overview)
      notifHref    : tujuan klik lonceng (mis. "#tugas-menunggu-nilai" atau route(...));
                     kosong = lonceng hanya tombol biasa. Pada mode `live`, bawaannya
                     halaman daftar notifikasi bila route-nya sudah ada.
      notifLabel   : teks aksesibilitas lonceng
      sub          : kalimat di bawah sapaan
      status       : teks status kecil dengan titik hijau (mis. "Sistem aktif"), opsional
--}}
@props([
    'portal'      => 'Portal',
    'title'       => 'Dashboard',
    'bell'        => true,
    'live'        => false,
    'notif'       => null,
    'searchLabel' => 'Cari mata kuliah...',
    'sub'         => 'Semoga harimu menyenangkan!',
    'status'      => null,
    'notifHref'   => null,
    'notifLabel'  => 'Notifikasi',
    'target'      => '.home-overview .semester-course',
])

@php
    $namaUser = auth()->user()?->name ?? 'Pengguna';
    $inisial  = collect(preg_split('/\s+/', trim($namaUser)))
        ->filter()->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->take(2)->join('');
    $namaDepan = explode(' ', trim($namaUser))[0];

    if ($live) {
        $notif      = auth()->user()->unreadNotifications()->count();
        $notifHref  = $notifHref ?? (Route::has('notifikasi.index') ? route('notifikasi.index') : null);
        $labelLonceng = "{$notifLabel}, {$notif} belum dibaca";
    } else {
        $labelLonceng = $notifLabel . ($notif ? ': ' . $notif : '');
    }

    $ikonLonceng = '<svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 8a6 6 0 10-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 01-3.4 0"/></svg>';
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
            @if ($bell)
                @if ($notifHref)
                    <a href="{{ $notifHref }}" class="dash-hero__bell" aria-label="{{ $labelLonceng }}" title="{{ $notifLabel }}"
                       @if ($live) data-bell data-label="{{ $notifLabel }}" data-url="{{ route('notifikasi.belum-dibaca') }}" @endif>
                        {!! $ikonLonceng !!}
                        @if ($live)
                            <span class="dash-hero__badge" data-bell-badge @if (! $notif) hidden @endif>{{ $notif > 99 ? '99+' : $notif }}</span>
                        @elseif ($notif)
                            <span class="dash-hero__badge">{{ $notif > 99 ? '99+' : $notif }}</span>
                        @endif
                    </a>
                @else
                    <button type="button" class="dash-hero__bell" aria-label="{{ $labelLonceng }}" title="{{ $notifLabel }}"
                            @if ($live) data-bell data-label="{{ $notifLabel }}" data-url="{{ route('notifikasi.belum-dibaca') }}" @endif>
                        {!! $ikonLonceng !!}
                        @if ($live)
                            <span class="dash-hero__badge" data-bell-badge @if (! $notif) hidden @endif>{{ $notif > 99 ? '99+' : $notif }}</span>
                        @elseif ($notif)
                            <span class="dash-hero__badge">{{ $notif > 99 ? '99+' : $notif }}</span>
                        @endif
                    </button>
                @endif
            @endif
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