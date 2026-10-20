@props(['title' => 'LMS Kampus', 'role' => null, 'fullFooter' => true])
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
        'resources/css/dashboard/admin.css',
        'resources/css/dashboard/dosen-mahasiswa.css',
        'resources/css/components/courses.css',
        'resources/css/components/buttons.css',
        'resources/css/courses/materials.css',
        'resources/css/courses/enrollments.css',
        'resources/css/courses/grading.css',
        'resources/css/courses/assignments.css',
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

            {{-- Pengunjung belum login (mis. di halaman Tentang): "Keluar" kembali ke halaman login.
                 Berupa tautan biasa di dalam <nav>, jadi otomatis ikut gaya dan hover menu lain. --}}
            @guest
                <a href="{{ Route::has('login') ? route('login') : url('/') }}">Keluar</a>
            @endguest

            {{-- Sudah login: logout harus POST (dengan @csrf), jadi berupa tombol di dalam form.
                 Gaya dan efek hover-nya ada di layout.css (.logout-form, .logout-btn). --}}
            @auth
                <form method="POST" action="{{ route('logout') }}" class="logout-form">
                    @csrf
                    <button type="submit" class="logout-btn">Keluar</button>
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

    {{-- Footer lengkap aktif secara bawaan di semua halaman.
         Untuk mematikannya di satu halaman: <x-layout :full-footer="false">.
         Dua bentuk dicek ($fullFooter dan 'full-footer') agar aman di semua versi Laravel. --}}
    @php
        $footerLengkap = ($fullFooter ?? false) || (${'full-footer'} ?? false);
    @endphp

    @if ($footerLengkap)
        {{-- Footer lengkap (bawaan semua halaman) --}}
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