@props(['title' => 'LMS Kampus', 'role' => null])
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
        {{-- Slot default: tempat konten tiap halaman disisipkan --}}
        {{ $slot }}
    </main>

    <footer class="app-footer">
        &copy; {{ date('Y') }} LMS Kampus
    </footer>

</body>
</html>