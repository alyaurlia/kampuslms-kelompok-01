<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Halaman Tidak Ditemukan - KampusLMS</title>

    {{-- Style dipisah ke resources/css/errors/errors.css dan dimuat lewat Vite --}}
    @vite(['resources/css/errors/errors.css'])
</head>

<body>
    <div class="error-container">
        <div class="error-code">404</div>

        <h1>Halaman Tidak Ditemukan</h1>

        <p>
            Maaf, halaman yang Anda cari tidak ada,
            sudah dipindahkan, atau alamatnya salah ketik.
        </p>

        <a href="{{ url('/') }}" class="back-button">
            Kembali ke Halaman Utama
        </a>
    </div>
</body>
</html>