{{--
    Lonceng notifikasi di navbar.

    Dosen & mahasiswa (bawaan):  <x-notification-bell />
        Menampilkan jumlah notifikasi belum dibaca milik user yang login,
        diperbarui tiap 30 detik.

    Admin:  <x-notification-bell :href="route('admin.notifikasi')" satuan="aktivitas hari ini" :active="..." />
        Menuju feed aktivitas. Feed itu dibentuk dari tabel materials, assignments,
        dan submissions (bukan tabel notifications), jadi angkanya adalah jumlah
        aktivitas hari ini (lihat App\Support\JumlahNotifikasi).

    Props:
        href    tujuan tautan (bawaan: halaman daftar notifikasi bila route-nya sudah ada)
        live    true = tampilkan angka + polling, false = ikon saja
        satuan  kata di belakang angka untuk pembaca layar (bawaan: "belum dibaca")
        active  true = tandai sebagai halaman yang sedang dibuka
        label   teks untuk pembaca layar dan tooltip
--}}
@props(['href' => null, 'live' => true, 'active' => false, 'label' => 'Notifikasi', 'satuan' => 'belum dibaca'])

@php
    $jumlah = $live ? \App\Support\JumlahNotifikasi::untuk(auth()->user()) : 0;

    // Halaman daftar notifikasi dibuat di Build nomor 5. Sebelum route-nya ada,
    // lonceng tidak menuju ke mana-mana (pola yang sama dengan menu di layout).
    $tujuan = $href ?? (Route::has('notifikasi.index') ? route('notifikasi.index') : '#');

    $namaAksesibel = $live ? "{$label}, {$jumlah} {$satuan}" : $label;
@endphp

<a href="{{ $tujuan }}"
   class="bell {{ $active ? 'is-active' : '' }}"
   @if ($live) data-bell data-label="{{ $label }}" data-satuan="{{ $satuan }}" data-url="{{ route('notifikasi.belum-dibaca') }}" @endif
   @if ($active) aria-current="page" @endif
   aria-label="{{ $namaAksesibel }}"
   title="{{ $label }}">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
         stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
        <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
    </svg>
    @if ($live)
        <span class="bell__badge" data-bell-badge @if ($jumlah === 0) hidden @endif>
            {{ $jumlah > 99 ? '99+' : $jumlah }}
        </span>
    @endif
</a>

@if ($live)
    <script>
        // Memperbarui semua lonceng bertanda [data-bell]: yang di navbar (komponen ini)
        // dan yang di banner dashboard dosen/mahasiswa (komponen dash-hero mode live).
        // Banner dimuat setelah skrip ini, jadi elemennya dicari ulang tiap kali (bukan disimpan di awal).
        (() => {
            const URL_JUMLAH = @json(route('notifikasi.belum-dibaca'));
            const INTERVAL_MS = 30000;
            let timer = null;

            const tampilkan = (n) => {
                document.querySelectorAll('[data-bell]').forEach((bell) => {
                    const badge = bell.querySelector('[data-bell-badge]');
                    if (badge) {
                        badge.hidden = n === 0;
                        badge.textContent = n > 99 ? '99+' : String(n);
                    }
                    bell.setAttribute('aria-label', `${bell.dataset.label || 'Notifikasi'}, ${n} ${bell.dataset.satuan || 'belum dibaca'}`);
                });
            };

            const perbarui = async () => {
                try {
                    const res = await fetch(URL_JUMLAH, {
                        // Accept JSON membuat sesi yang habis dijawab 401, bukan dialihkan ke halaman login.
                        headers: { 'Accept': 'application/json' },
                        cache: 'no-store',
                    });

                    if (res.status === 401) { berhenti(); return; }
                    if (!res.ok) return;

                    const data = await res.json();
                    tampilkan(Number(data.count) || 0);
                } catch (_) {
                    // Jaringan putus sesaat: biarkan, coba lagi di putaran berikutnya.
                }
            };

            const mulai = () => { if (!timer) timer = setInterval(perbarui, INTERVAL_MS); };
            const berhenti = () => { clearInterval(timer); timer = null; };

            // Tab yang tidak terlihat tidak perlu memanggil server.
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    berhenti();
                } else {
                    perbarui();
                    mulai();
                }
            });

            mulai();
        })();
    </script>
@endif