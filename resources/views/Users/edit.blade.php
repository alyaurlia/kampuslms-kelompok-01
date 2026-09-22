<x-layout title="Edit Pengguna">

    <h1 style="font-size:1.6rem; margin-bottom:1.5rem;">Edit Pengguna</h1>

    @if ($errors->any())
        <div style="background:#fdecea; border:1px solid #f5c2c0; border-radius:6px; padding:1rem; margin-bottom:1.5rem; font-family:Arial, sans-serif;">
            <ul style="margin:0; padding-left:1.2rem; color:#b3261e;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem; max-width:600px;">

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama --}}
            <div style="margin-bottom:1rem;">
                <label for="name" style="display:block; margin-bottom:0.4rem;">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $user->name) }}"
                    style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
                >
            </div>

            {{-- Email --}}
            <div style="margin-bottom:1rem;">
                <label for="email" style="display:block; margin-bottom:0.4rem;">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $user->email) }}"
                    style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
                >
            </div>

            {{-- Password (opsional) --}}
            <div style="margin-bottom:1rem;">
                <label for="password" style="display:block; margin-bottom:0.4rem;">
                    Kata Sandi Baru
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    placeholder="Kosongkan jika tidak ingin mengubah"
                    style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
                >
                <p style="margin:0.3rem 0 0; font-size:0.8rem; color:var(--color-ink-soft);">
                    Biarkan kosong jika tidak ingin mengganti kata sandi.
                </p>
            </div>

            {{-- Konfirmasi Password --}}
            <div style="margin-bottom:1rem;">
                <label for="password_confirmation" style="display:block; margin-bottom:0.4rem;">
                    Konfirmasi Kata Sandi Baru
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
                >
            </div>

            {{-- Role --}}
            <div style="margin-bottom:1.5rem;">
                <label for="role" style="display:block; margin-bottom:0.4rem;">
                    Peran
                </label>

                <select
                    name="role"
                    id="role"
                    style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
                >
                    <option value="">-- Pilih Peran --</option>
                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="dosen" {{ old('role', $user->role) === 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="mahasiswa" {{ old('role', $user->role) === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                </select>
            </div>

            <div style="display:flex; gap:0.75rem;">

                <button
                    type="submit"
                    style="padding:0.6rem 1.2rem; border:none; border-radius:4px; background:var(--color-card-1-from); color:#fff; cursor:pointer;"
                >
                    Simpan Perubahan
                </button>

                
                    href="{{ route('users.index') }}"
                    style="padding:0.6rem 1.2rem; border-radius:4px; border:1px solid var(--color-border); text-decoration:none; color:inherit;"
                >
                    Batal
                </a>

            </div>

        </form>

    </section>

</x-layout>