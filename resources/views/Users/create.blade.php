<x-layout title="Tambah Pengguna">

    <h1 class="form-title">Tambah Pengguna</h1>

    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="form-card">

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            {{-- Nama --}}
            <div class="form-group">
                <label for="name" class="form-label">
                    Nama Lengkap
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
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
                    value="{{ old('email') }}"
                    class="form-control"
                >
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label for="password" class="form-label">
                    Kata Sandi
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                >
            </div>

            {{-- Konfirmasi Password --}}
            <div class="form-group">
                <label for="password_confirmation" class="form-label">
                    Konfirmasi Kata Sandi
                </label>

                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-control"
                >
            </div>

            {{-- Role --}}
            <div class="form-group form-group--lg">
                <label for="role" class="form-label">
                    Peran
                </label>

                <select name="role" id="role" class="form-control">
                    <option value="">-- Pilih Peran --</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="dosen" {{ old('role') === 'dosen' ? 'selected' : '' }}>Dosen</option>
                    <option value="mahasiswa" {{ old('role') === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                </select>
            </div>

            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>

                <a href="{{ route('users.index') }}" class="btn btn-secondary">
                    Batal
                </a>

            </div>

        </form>

    </section>

</x-layout>