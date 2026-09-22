{{--
    View index: menampilkan daftar pengguna dalam bentuk tabel.
    Berbeda dari mata-kuliah (grid kartu) karena data pengguna lebih
    cocok ditampilkan sebagai baris (nama, email, role, aksi).

    $users adalah hasil paginate() dari Eloquent, jadi setiap elemen
    adalah OBJEK model (akses pakai ->), bukan array asosiatif.
--}}
<x-layout title="Daftar Pengguna">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1 style="font-size:1.6rem; margin:0;">Daftar Pengguna</h1>

        <a href="{{ route('users.create') }}"
           style="padding:0.5rem 1rem; border-radius:4px; background:var(--color-card-1-from); color:#fff; text-decoration:none; font-family:Arial, sans-serif; font-size:0.9rem;">
            + Tambah Pengguna
        </a>
    </div>

    {{-- Notifikasi sukses dari redirect create/update/delete --}}
    @if (session('success'))
        <div style="background:#e6f4ea; border:1px solid #b7dfc2; border-radius:6px; padding:1rem; margin-bottom:1.5rem; font-family:Arial, sans-serif; color:#1e7e34;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form pencarian & filter role --}}
    <form method="GET" action="{{ route('users.index') }}"
          style="display:flex; gap:0.75rem; margin-bottom:1.5rem; flex-wrap:wrap;">

        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari nama atau email..."
            style="flex:1; min-width:200px; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
        >

        <select name="role"
                style="padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
            <option value="">-- Semua Peran --</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
            <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
        </select>

        <button type="submit"
                style="padding:0.5rem 1rem; border-radius:4px; border:none; background:var(--color-card-1-from); color:#fff; cursor:pointer;">
            Cari
        </button>

        @if (request('q') || request('role'))
            <a href="{{ route('users.index') }}"
               style="padding:0.5rem 1rem; border-radius:4px; border:1px solid var(--color-border); text-decoration:none; color:inherit;">
                Reset
            </a>
        @endif
    </form>

    @if ($users->isEmpty())
        <section style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem;">
            <p style="margin:0; color:var(--color-ink-soft); text-align:center; font-family:Arial, sans-serif;">
                Belum ada data pengguna.
            </p>
        </section>
    @else
        <div style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:10px; overflow:hidden; font-family:Arial, sans-serif;">
            <table style="width:100%; border-collapse:collapse; font-size:0.9rem;">
                <thead>
                    <tr style="background:var(--color-bg); text-align:left;">
                        <th style="padding:0.75rem 1rem; border-bottom:1px solid var(--color-border);">Nama</th>
                        <th style="padding:0.75rem 1rem; border-bottom:1px solid var(--color-border);">Email</th>
                        <th style="padding:0.75rem 1rem; border-bottom:1px solid var(--color-border);">Peran</th>
                        <th style="padding:0.75rem 1rem; border-bottom:1px solid var(--color-border); text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr style="border-bottom:1px solid var(--color-border);">
                            <td style="padding:0.75rem 1rem;">
                                <a href="{{ route('users.show', $user->id) }}" style="color:var(--color-primary); text-decoration:none; font-weight:600;">
                                    {{ $user->name }}
                                </a>
                            </td>
                            <td style="padding:0.75rem 1rem; color:var(--color-ink-soft);">
                                {{ $user->email }}
                            </td>
                            <td style="padding:0.75rem 1rem;">
                                <span style="padding:0.2rem 0.6rem; border-radius:12px; font-size:0.75rem; font-weight:600; background:var(--color-bg); color:var(--color-primary-dark); border:1px solid var(--color-border);">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td style="padding:0.75rem 1rem; text-align:right;">
                                <div style="display:flex; gap:0.5rem; justify-content:flex-end;">
                                    <a href="{{ route('users.edit', $user->id) }}"
                                       style="padding:0.35rem 0.8rem; border-radius:4px; border:1px solid var(--color-border); text-decoration:none; color:inherit; font-size:0.85rem;">
                                        Edit
                                    </a>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus pengguna {{ $user->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                style="padding:0.35rem 0.8rem; border-radius:4px; border:1px solid #f5c2c0; background:#fdecea; color:#b3261e; cursor:pointer; font-size:0.85rem;">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination — otomatis membawa query string (?q=...&role=...)
             karena controller sudah pakai withQueryString() --}}
        <div style="margin-top:2rem;">
            {{ $users->links() }}
        </div>
    @endif

</x-layout>