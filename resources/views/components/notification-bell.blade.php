{{--
    Lonceng notifikasi di navbar (admin, dosen, mahasiswa).
    Dipakai sekali di layout, di dalam <nav>:  <x-notification-bell />

    - Tujuan klik: route "{peran}.notifikasi". Kalau route itu belum ada
      (mis. mahasiswa), lonceng tidak ditampilkan sama sekali.
    - Angka awal dihitung server, lalu diperbarui polling tiap 30 detik ke
      route notifikasi.belum-dibaca. Keduanya memakai App\Support\JumlahNotifikasi.
    - Gaya memakai kelas .nav-bell dan .nav-bell__badge di layout.css.
--}}
@php
    $user = auth()->user();
    $routeLonceng = $user ? $user->role . '.notifikasi' : null;
    $punyaLonceng = $user && Route::has($routeLonceng);
    $jumlah = $punyaLonceng ? \App\Support\JumlahNotifikasi::untuk($user) : 0;
@endphp

@if ($punyaLonceng)
    <a href="{{ route($routeLonceng) }}"
       class="nav-bell {{ request()->routeIs($routeLonceng) ? 'active' : '' }}"
       data-bell
       data-url="{{ route('notifikasi.belum-dibaca') }}"
       aria-label="Notifikasi{{ $jumlah ? ', ' . $jumlah . ' baru' : '' }}"
       title="Notifikasi">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
            <path d="M13.7 21a2 2 0 0 1-3.4 0"></path>
        </svg>
        @if ($jumlah > 0)
            <span class="nav-bell__badge" data-bell-badge>{{ $jumlah > 99 ? '99+' : $jumlah }}</span>
        @endif
    </a>

    <script>
        (() => {
            const bell = document.querySelector('[data-bell]');
            if (!bell) return;

            const URL_JUMLAH = bell.dataset.url;
            const INTERVAL_MS = 30000;
            let timer = null;

            // Badge dibuat atau dihapus sesuai angka, jadi tidak perlu aturan CSS tambahan.
            const tampilkan = (n) => {
                let badge = bell.querySelector('[data-bell-badge]');

                if (n === 0) {
                    if (badge) badge.remove();
                } else {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'nav-bell__badge';
                        badge.setAttribute('data-bell-badge', '');
                        bell.appendChild(badge);
                    }
                    badge.textContent = n > 99 ? '99+' : String(n);
                }

                bell.setAttribute('aria-label', n > 0 ? `Notifikasi, ${n} baru` : 'Notifikasi');
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