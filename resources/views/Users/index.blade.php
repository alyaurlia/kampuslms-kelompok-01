<x-layout title="Kelola Pengguna">

    <div class="users-header">
        <div>
            <h1 class="form-title">Kelola Pengguna</h1>
            <p class="users-subtitle">Daftar akun admin, dosen, dan mahasiswa KampusLMS.</p>
        </div>

        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
            + Tambah Pengguna
        </a>
    </div>

    {{-- Pencarian & filter peran (GET, supaya bisa di-bookmark dan ikut pagination) --}}
    <form method="GET" action="{{ route('admin.users.index') }}" class="users-filter">
        <input
            type="search"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari nama, email, atau NIM/NIP"
            class="form-control"
        >

        <select name="role" class="form-control">
            <option value="">Semua peran</option>
            @foreach (['admin' => 'Admin', 'dosen' => 'Dosen', 'mahasiswa' => 'Mahasiswa'] as $value => $label)
                <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary">Cari</button>

        @if (request()->filled('q') || request()->filled('role'))
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>

    <section class="form-card users-card">

        @if ($users->count())

            <div class="table-wrap">
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIM / NIP</th>
                            <th>Peran</th>
                            <th class="col-actions">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.users.show', $user) }}" class="users-name">{{ $user->name }}</a>
                                    @if ($user->is(auth()->user()))
                                        <span class="users-self">Anda</span>
                                    @endif
                                    <div class="users-email">{{ $user->email }}</div>
                                </td>

                                <td>{{ $user->nim_nip ?? '—' }}</td>

                                <td>
                                    <span class="badge badge--{{ $user->role }}">{{ ucfirst($user->role) }}</span>
                                </td>

                                <td class="col-actions">
                                    <a href="{{ route('admin.users.show', $user) }}" class="link-action">Lihat</a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="link-action">Edit</a>

                                    {{-- Tidak ada tombol hapus untuk akun sendiri --}}
                                    @unless ($user->is(auth()->user()))
                                        <form
                                            action="{{ route('admin.users.destroy', $user) }}"
                                            method="POST"
                                            class="inline-form"
                                            onsubmit="return confirm('Hapus pengguna ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="link-action link-action--danger">Hapus</button>
                                        </form>
                                    @endunless
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="users-count">
                Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }} dari {{ $users->total() }} pengguna
            </p>

            {{-- Markup bootstrap-4 dipilih karena proyek ini tidak memakai Tailwind --}}
            {{ $users->links('pagination::bootstrap-4') }}

        @else

            <p class="users-empty">Tidak ada pengguna yang cocok dengan pencarian Anda.</p>

        @endif

    </section>

</x-layout>