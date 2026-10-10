<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Belum Masuk - KampusLMS</title>

    {{-- Style dipisah ke resources/css/errors/errors.css dan dimuat lewat Vite --}}
    @vite(['resources/css/errors/errors.css'])
</head>

<body>
    <div class="error-container">
        <div class="error-code">401</div>

        <h1>Belum Masuk</h1>

        <p>
            Maaf, Anda perlu masuk terlebih dahulu
            untuk mengakses halaman ini.
        </p>

        <a href="{{ route('login') }}" class="back-button">
            Masuk ke Akun
        </a>
    </div>
</body>
</html>