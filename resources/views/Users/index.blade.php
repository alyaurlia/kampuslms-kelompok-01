{{--
    View index: menampilkan daftar pengguna dalam bentuk tabel.
    Berbeda dari mata-kuliah (grid kartu) karena data pengguna lebih
    cocok ditampilkan sebagai baris (nama, email, role, aksi).

    Style tabel didefinisikan di resources/css/users.css; header, filter,
    notifikasi, dan pagination memakai class dari resources/css/courses.css.

    $users adalah hasil paginate() dari Eloquent, jadi setiap elemen
    adalah OBJEK model (akses pakai ->), bukan array asosiatif.
--}}
<x-layout title="Daftar Pengguna">

    <div class="mk-page-header">
        <h1 class="mk-page-title">Daftar Pengguna</h1>

        <a href="{{ route('users.create') }}" class="mk-btn-add">
            + Tambah Pengguna
        </a>
    </div>

    {{-- Notifikasi sukses dari redirect create/update/delete --}}
    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form pencarian & filter role --}}
    <form method="GET" action="{{ route('users.index') }}" class="mk-filter">

        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari nama atau email..."
            class="mk-filter-input"
        >

        <select name="role" class="mk-filter-select">
            <option value="">-- Semua Peran --</option>
            <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
            <option value="dosen" {{ request('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
            <option value="mahasiswa" {{ request('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
        </select>

        <button type="submit" class="mk-filter-btn">
            Cari
        </button>

        @if (request('q') || request('role'))
            <a href="{{ route('users.index') }}" class="mk-filter-reset">
                Reset
            </a>
        @endif
    </form>

    @if ($users->isEmpty())
        <section class="mk-empty">
            <p>Belum ada data pengguna.</p>
        </section>
    @else
        <div class="user-table-wrap">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Peran</th>
                        <th class="user-th-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <a href="{{ route('users.show', $user->id) }}" class="user-name-link">
                                    {{ $user->name }}
                                </a>
                            </td>
                            <td class="user-td-muted">
                                {{ $user->email }}
                            </td>
                            <td>
                                <span class="user-role-badge">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td class="user-td-right">
                                <div class="user-actions">
                                    <a href="{{ route('users.edit', $user->id) }}" class="user-action-edit">
                                        Edit
                                    </a>

                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus pengguna {{ $user->name }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="user-action-delete">
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
        <div class="mk-pagination">
            {{ $users->links() }}
        </div>
    @endif

</x-layout>