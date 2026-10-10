@props(['title' => 'LMS Kampus', 'role' => null, 'fullFooter' => false])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Title dinamis per halaman, di-escape otomatis oleh {{ }} --}}
    <title>{{ $title }} — LMS Kampus</title>

    {{-- Style dipisah ke resources/css/ dan dimuat lewat Vite --}}
    @vite([
        'resources/css/components/layout.css',
        'resources/css/components/dashboard.css',
        'resources/css/components/courses.css',
        'resources/css/courses/forms.css',
        'resources/css/users/users.css',
        'resources/css/tentang.css',
    ])
</head>
<body>

    @php
        // Kalau role tidak dikirim dari halaman, ambil dari user yang login
        $role = $role ?? auth()->user()?->role;

        $menus = [
            'admin' => [
                ['Dashboard',   'admin.dashboard',          'admin.dashboard'],
                ['Kelola User', 'admin.users.index',        'admin.users.*'],
                ['Mata Kuliah', 'admin.mata-kuliah.index',  'admin.mata-kuliah.*'],
            ],
            'dosen' => [
                ['Dashboard',   'dosen.dashboard',          'dosen.dashboard'],
                ['Mata Kuliah', 'dosen.mata-kuliah.index',  'dosen.mata-kuliah.*'],
            ],
            'mahasiswa' => [
                ['Dashboard',   'mahasiswa.dashboard',          'mahasiswa.dashboard'],
                ['Mata Kuliah', 'mahasiswa.mata-kuliah.index',  'mahasiswa.mata-kuliah.*'],
            ],
        ];

        $menu = $menus[$role] ?? [
            ['Tentang', 'tentang', 'tentang'],
        ];
    @endphp

    <header class="app-header">
        <span class="brand">
            LMS Kampus
            @if($role)
                <span class="role-badge">{{ ucfirst($role) }}</span>
            @endif
        </span>
        <nav>
            @foreach($menu as [$label, $routeName, $pattern])
                <a href="{{ Route::has($routeName) ? route($routeName) : '#' }}"
                   class="{{ request()->routeIs($pattern) ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach

            @auth
                <form method="POST" action="{{ route('logout') }}" style="display:inline; margin-left:1.5rem;">
                    @csrf
                    <button type="submit"
                            style="background:none; border:none; padding:0; cursor:pointer; color:#F3D9C4; font-family:Arial, Helvetica, sans-serif; font-size:0.9rem;">
                        Keluar
                    </button>
                </form>
            @endauth
        </nav>
    </header>

    <main>
        {{-- Flash message: berlaku untuk seluruh halaman yang memakai layout ini --}}
        @if (session('success'))
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error" role="alert">{{ session('error') }}</div>
        @endif

        {{-- Slot default: tempat konten tiap halaman disisipkan --}}
        {{ $slot }}
    </main>

    {{-- Footer lengkap aktif lewat <x-layout full-footer>.
         Dua bentuk dicek ($fullFooter dan 'full-footer') agar aman di semua versi Laravel. --}}
    @php
        $footerLengkap = ($fullFooter ?? false) || (${'full-footer'} ?? false);
    @endphp

    @if ($footerLengkap)
        {{-- Footer lengkap (dipakai dashboard admin / dosen / mahasiswa) --}}
        <footer class="site-footer">
            <div class="site-footer__inner">
                <div class="site-footer__brand">
                    <span class="site-footer__logo">LMS Kampus</span>
                    <span class="site-footer__sep" aria-hidden="true"></span>
                    <span class="site-footer__tagline">Sistem Pembelajaran Daring</span>
                </div>

                <p class="site-footer__desc">
                    LMS Kampus adalah sistem pembelajaran daring yang membantu dosen dan
                    mahasiswa mengelola mata kuliah, tugas, dan penilaian dalam satu tempat.
                </p>

                <div class="site-footer__social">
                    <a href="#" aria-label="YouTube">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M21.6 7.2a2.5 2.5 0 0 0-1.8-1.8C18.2 5 12 5 12 5s-6.2 0-7.8.4A2.5 2.5 0 0 0 2.4 7.2C2 8.8 2 12 2 12s0 3.2.4 4.8a2.5 2.5 0 0 0 1.8 1.8C5.8 19 12 19 12 19s6.2 0 7.8-.4a2.5 2.5 0 0 0 1.8-1.8c.4-1.6.4-4.8.4-4.8s0-3.2-.4-4.8zM10 15V9l5.2 3z"/></svg>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                    </a>
                    <a href="#" aria-label="Facebook">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M14 8h2.5V5H14c-2.2 0-3.5 1.6-3.5 3.7V11H8v3h2.5v7h3v-7H16l.5-3h-3V9c0-.6.3-1 1-1z"/></svg>
                    </a>
                    <a href="#" aria-label="X">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 4l16 16M20 4L4 20"/></svg>
                    </a>
                    <a href="#" aria-label="TikTok">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 4v10.5a3 3 0 1 1-3-3"/><path d="M14 4c.3 2.2 1.8 3.7 4 4"/></svg>
                    </a>
                    {{-- Tombol ke halaman Tentang --}}
                    <a href="{{ Route::has('tentang') ? route('tentang') : '#' }}" aria-label="Tentang" title="Tentang">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 11v5"/><circle cx="12" cy="7.5" r="0.6" fill="currentColor"/></svg>
                    </a>
                </div>

                <div class="site-footer__bottom">
                    &copy; {{ date('Y') }} LMS Kampus &middot; <em>Belajar kapan saja, di mana saja</em>
                </div>
            </div>
        </footer>
    @else
        <footer class="app-footer">
            &copy; {{ date('Y') }} LMS Kampus
        </footer>
    @endif

</body>
</html>