<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login - Kampus LMS</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <!-- Tailwind via CDN (sementara, sebelum pakai sistem auth resmi) -->
        <script src="https://cdn.tailwindcss.com"></script>

        {{-- Warna kustom halaman login dipisah ke resources/css/welcome.css --}}
        @vite(['resources/css/welcome.css'])
    </head>
    <body class="login-body min-h-screen flex items-center justify-center">

        <div class="login-card bg-white rounded-2xl shadow-2xl p-10 w-full max-w-md mx-4 border-t-4">

            <div class="flex justify-center mb-8">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Logo Kampus LMS" class="h-24 object-contain">
            </div>

            <form method="POST" action="#" class="space-y-4">
                @csrf

                <div>
                    <input type="text" name="nim" placeholder="NIM"
                        class="login-input w-full border-2 rounded-full px-5 py-3 focus:outline-none transition">
                </div>

                <div>
                    <input type="password" name="password" placeholder="Kata Sandi"
                        class="login-input w-full border-2 rounded-full px-5 py-3 focus:outline-none transition">
                </div>

                <button type="submit"
                    class="login-button w-full text-white font-semibold py-3 rounded-full transition hover:opacity-90">
                    Masuk
                </button>

                <div class="text-center">
                    <a href="#" class="login-link text-sm hover:underline font-medium">
                        Lupa kata sandi?
                    </a>
                </div>
            </form>
        </div>

    </body>
</html>