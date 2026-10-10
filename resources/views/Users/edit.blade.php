<x-layout title="Edit Pengguna">

    <h1 class="form-title">Edit Pengguna</h1>

    @if ($errors->any())
        <div class="alert alert-error" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="form-card">

        <form action="{{ route('admin.users.update', $user) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div class="form-group">
                <label for="name" class="form-label">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $user->name) }}"
                    maxlength="150"
                    class="form-control"
                >
            </div>

            {{-- Email --}}
            <div class="form-group">
                <label for="email" class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $user->email) }}"
                    maxlength="150"
                    autocomplete="off"
                    class="form-control"
                >
            </div>

            {{-- Password (opsional) --}}
            <div class="form-group">
                <label for="password" class="form-label">
                    Kata Sandi Baru
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Kosongkan jika tidak ingin mengubah"
                    autocomplete="new-password"
                    class="form-control"
                >
                <p class="form-hint">
                    Biarkan kosong jika tidak ingin mengganti kata sandi.
                </p>
            </div>

            {{-- Konfirmasi Password --}}
            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    Konfirmasi Kata Sandi Baru
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    autocomplete="new-password"
                    class="form-control"
                >
            </div>

            {{-- Role --}}
            <div class="form-group">
                <label for="role" class="form-label">
                    Peran
                </label>

                <select name="role" id="role" class="form-control">
                    <option value="">-- Pilih Peran --</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="dosen" {{ old('role', $user->role) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="mahasiswa" {{ old('role', $user->role) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                </select>

                @if ($user->is(auth()->user()))
                    <p class="form-hint">
                        Anda sedang mengedit akun Anda sendiri, jadi peran tidak dapat diubah.
                    </p>
                @endif
            </div>

            {{-- NIM / NIP --}}
            <div class="form-group form-group--lg">
                <label for="nim_nip" class="form-label">
                    NIM / NIP
                </label>

                <input
                    type="text"
                    name="nim_nip"
                    id="nim_nip"
                    value="{{ old('nim_nip', $user->nim_nip) }}"
                    maxlength="30"
                    class="form-control"
                >

                <p class="form-hint">
                    Dipakai sebagai nama pengguna saat login, jadi wajib diisi untuk semua peran.
                </p>
            </div>

            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    Batal
                </a>

            </div>

        </form>

    </section>

</x-layout>