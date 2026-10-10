<x-layout title="Atur Ulang Kata Sandi">

    <h1 class="form-title">Atur Ulang Kata Sandi</h1>

    @if ($errors->any())
        <div class="alert alert-error" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="form-card auth-card">

        <form action="{{ route('password.update') }}" method="POST" autocomplete="off">
            @csrf

            {{-- Token dari tautan email --}}
            <input type="hidden" name="token" value="{{ $token }}">

            {{-- Email (terisi dari tautan) --}}
            <div class="form-group">
                <label for="email" class="form-label">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    value="{{ old('email', $email) }}"
                    maxlength="150"
                    @readonly($email)
                    class="form-control"
                >
            </div>

            {{-- Kata sandi baru --}}
            <div class="form-group">
                <label for="password" class="form-label">
                    Kata Sandi Baru
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    autocomplete="new-password"
                    autofocus
                    class="form-control"
                >
                <p class="form-hint">Minimal 8 karakter.</p>
            </div>

            {{-- Konfirmasi --}}
            <div class="form-group form-group--lg">
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

            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    Simpan Kata Sandi Baru
                </button>

                <a href="{{ route('login') }}" class="btn btn-secondary">
                    Batal
                </a>

            </div>

        </form>

    </section>

</x-layout>