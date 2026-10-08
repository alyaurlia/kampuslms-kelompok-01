<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Akses Ditolak - KampusLMS</title>

    {{-- Style dipisah ke resources/css/errors/errors.css dan dimuat lewat Vite --}}
    @vite(['resources/css/errors/errors.css'])
</head>

<body>
    <div class="error-container">
        <div class="error-code">403</div>

        <h1>Akses Ditolak</h1>

        <p>
            Maaf, Anda tidak memiliki izin untuk mengakses
            halaman atau melakukan tindakan ini.
        </p>

        <a href="{{ url('/') }}" class="back-button">
            Kembali ke Halaman Utama
        </a>
    </div>
</body>
</html>