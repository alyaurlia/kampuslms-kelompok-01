{{--
    View show: menampilkan detail satu pengguna.
    $user di sini adalah OBJEK model Eloquent (akses pakai ->), sesuai
    bentuk data yang dikirim controller lewat compact('user').

    Style kartu (.mk-card, .mk-banner, dst.) dipakai ulang dari
    layout.blade.php supaya identitas visual konsisten dengan
    courses/show.blade.php, meski datanya beda (nama, email, peran).
--}}
<x-layout :title="$user->name">

    {{-- Tombol kembali ditaruh di atas judul supaya konsisten dengan pola
         navigasi umum "list -> detail -> kembali ke list". --}}
    <a href="{{ route('users.index') }}" class="mk-back-link">
        &larr; Kembali ke Daftar Pengguna
    </a>

    @php
        // Palet warna & motif dirotasi berdasarkan email pengguna (crc32),
        // memakai variabel --color-card-N-* yang sama dengan
        // courses/show.blade.php, supaya identitas visual tiap pengguna
        // konsisten setiap kali halaman dibuka.
        $mkPatterns = ['diamond', 'triangle', 'circle', 'diamond', 'triangle'];
        $mkIndex = (crc32($user->email) % 5) + 1;
        $mkPattern = $mkPatterns[$mkIndex - 1];
    @endphp

    <div class="mk-card">

        <div class="mk-banner mk-pattern-{{ $mkPattern }}"
             style="background: linear-gradient(135deg, var(--color-card-{{ $mkIndex }}-from), var(--color-card-{{ $mkIndex }}-to));">
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
            <div style="display:flex; gap:0.75rem; margin-top:1.5rem;">
                <a href="{{ route('users.edit', $user->id) }}"
                   style="padding:0.5rem 1.2rem; border-radius:4px; border:1px solid var(--color-border); text-decoration:none; color:inherit; font-family:Arial, sans-serif; font-size:0.9rem;">
                    Edit
                </a>

                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                      onsubmit="return confirm('Yakin ingin menghapus pengguna {{ $user->name }}?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            style="padding:0.5rem 1.2rem; border-radius:4px; border:1px solid #f5c2c0; background:#fdecea; color:#b3261e; cursor:pointer; font-family:Arial, sans-serif; font-size:0.9rem;">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-layout>