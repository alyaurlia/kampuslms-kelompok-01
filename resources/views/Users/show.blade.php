{{--
    View show: menampilkan detail satu pengguna.
    $user di sini adalah OBJEK model Eloquent (akses pakai ->), sesuai
    bentuk data yang dikirim controller lewat compact('user').

    Style kartu (.mk-card, .mk-banner, dst.) dipakai ulang dari
    resources/css/courses.css supaya identitas visual konsisten dengan
    courses/show.blade.php, meski datanya beda (nama, email, peran).
    Tombol aksi memakai class dari resources/css/users.css.
--}}
<x-layout :title="$user->name">

    {{-- Tombol kembali ditaruh di atas judul supaya konsisten dengan pola
         navigasi umum "list -> detail -> kembali ke list". --}}
    <a href="{{ route('users.index') }}" class="mk-back-link">
        &larr; Kembali ke Daftar Pengguna
    </a>

    @php
        // Palet warna & motif dirotasi berdasarkan email pengguna (crc32),
        // memakai class .mk-bg-N (lihat courses.css) yang sama dengan
        // courses/show.blade.php, supaya identitas visual tiap pengguna
        // konsisten setiap kali halaman dibuka.
        $mkPatterns = ['diamond', 'triangle', 'circle', 'diamond', 'triangle'];
        $mkIndex = (crc32($user->email) % 5) + 1;
        $mkPattern = $mkPatterns[$mkIndex - 1];
    @endphp

    <div class="mk-card">

        <div class="mk-banner mk-pattern-{{ $mkPattern }} mk-bg-{{ $mkIndex }}">
            <span class="mk-badge">{{ ucfirst($user->role) }}</span>
        </div>

        <div class="mk-body">
            <h1 class="mk-title">{{ $user->name }}</h1>

            <div class="mk-meta-row">
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Email</span>
                    <span class="mk-meta-value">{{ $user->email }}</span>
                </div>
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Peran</span>
                    <span class="mk-meta-value">{{ ucfirst($user->role) }}</span>
                </div>
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Terdaftar Sejak</span>
                    <span class="mk-meta-value">{{ $user->created_at->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            {{-- Tombol aksi: edit / hapus, sejajar dengan tombol serupa
                 di users/index.blade.php --}}
            <div class="user-detail-actions">
                <a href="{{ route('users.edit', $user->id) }}" class="user-detail-edit">
                    Edit
                </a>

                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus pengguna {{ $user->name }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="user-detail-delete">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-layout>