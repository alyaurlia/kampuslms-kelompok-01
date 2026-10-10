<x-layout title="Lupa Kata Sandi">

    <div class="auth-wrap">

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

            <div class="auth-card__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" width="30" height="30" fill="none" stroke="currentColor"
                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="4" y="11" width="16" height="10" rx="2"/>
                    <path d="M8 11V8a4 4 0 0 1 8 0v3"/>
                    <circle cx="12" cy="16" r="1.2" fill="currentColor" stroke="none"/>
                </svg>
            </div>

            <h1 class="form-title">Lupa Kata Sandi</h1>

            <p class="form-hint">
                Masukkan email yang terdaftar pada akun Anda. Kami akan mengirimkan
                tautan untuk mengatur ulang kata sandi.
            </p>

            <form action="{{ route('password.email') }}" method="POST">
                @csrf

                {{-- Email --}}
                <div class="form-group form-group--lg">
                    <label for="email" class="form-label">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        maxlength="150"
                        autocomplete="email"
                        autofocus
                        class="form-control"
                    >
                </div>

                <div class="form-actions">

                    <button type="submit" class="btn btn-primary">
                        Kirim Tautan Reset
                    </button>

                    <a href="{{ route('login') }}" class="btn btn-secondary">
                        Kembali ke Halaman Masuk
                    </a>

                </div>

            </form>

        </section>

    </div>

</x-layout>