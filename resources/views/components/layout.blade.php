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
        // Daftar menu per peran: [label, nama route, pola untuk penanda aktif].
        // Sesuaikan nama route dengan yang dibuat di routes/web.php.
        $menus = [
            'admin' => [
                ['Dashboard',   'admin.dashboard',          'admin.dashboard'],
                ['Kelola User', 'admin.users.index',        'admin.users.*'],
                ['Mata Kuliah', 'admin.mata-kuliah.index',  'admin.mata-kuliah.*'],
            ],
            'dosen' => [
                ['Dashboard',   'dosen.dashboard',          'dosen.dashboard'],
                ['Kelas Saya',  'dosen.kelas.index',        'dosen.kelas.*'],
                ['Input Nilai', 'dosen.nilai.index',        'dosen.nilai.*'],
            ],
            'mahasiswa' => [
                ['Dashboard',   'mahasiswa.dashboard',          'mahasiswa.dashboard'],
                ['Mata Kuliah', 'mahasiswa.mata-kuliah.index',  'mahasiswa.mata-kuliah.*'],
                ['Nilai',       'mahasiswa.nilai.index',        'mahasiswa.nilai.*'],
            ],
        ];

        // Kalau role tidak diisi, pakai menu bawaan yang lama
        $menu = $menus[$role] ?? [
            ['Dashboard',   'dashboard',          'dashboard'],
            ['Mata Kuliah', 'mata-kuliah.index',  'mata-kuliah.*'],
            ['Tentang',     'tentang',            'tentang'],
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
                {{-- Route::has() mencegah error kalau route belum dibuat --}}
                <a href="{{ Route::has($routeName) ? route($routeName) : '#' }}"
                   class="{{ request()->routeIs($pattern) ? 'active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
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