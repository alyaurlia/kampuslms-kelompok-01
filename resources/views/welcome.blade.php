
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
    </head>
    <body class="min-h-screen flex items-center justify-center"
          style="background: linear-gradient(135deg, #A9BF55, #CB125E);">

        <div class="bg-white rounded-2xl shadow-2xl p-10 w-full max-w-md mx-4 border-t-4" style="border-color: #CB125E;">

            <div class="flex justify-center mb-8">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Logo Kampus LMS" class="h-24 object-contain">
            </div>

            <form method="POST" action="#" class="space-y-4">
                @csrf

                <div>
                    <input type="text" name="nim" placeholder="NIM"
                        class="w-full border-2 rounded-full px-5 py-3 focus:outline-none transition"
                        style="border-color: #DEA249;"
                        onfocus="this.style.borderColor='#CB125E'; this.style.boxShadow='0 0 0 3px #F8979733'"
                        onblur="this.style.borderColor='#DEA249'; this.style.boxShadow='none'">
                </div>

                <div>
                    <input type="password" name="password" placeholder="Kata Sandi"
                        class="w-full border-2 rounded-full px-5 py-3 focus:outline-none transition"
                        style="border-color: #DEA249;"
                        onfocus="this.style.borderColor='#CB125E'; this.style.boxShadow='0 0 0 3px #F8979733'"
                        onblur="this.style.borderColor='#DEA249'; this.style.boxShadow='none'">
                </div>

                <button type="submit"
                    class="w-full text-white font-semibold py-3 rounded-full transition hover:opacity-90"
                    style="background: linear-gradient(90deg, #F85988, #CB125E);">
                    Masuk
                </button>

                <div class="text-center">
                    <a href="#" class="text-sm hover:underline font-medium" style="color: #CB125E;">
                        Lupa kata sandi?
                    </a>
                </div>
            </form>
        </div>

    </body>
</html>