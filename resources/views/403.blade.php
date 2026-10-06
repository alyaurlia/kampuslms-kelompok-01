<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - KampusLMS</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            color: #1f2937;
        }

        .error-container {
            width: 90%;
            max-width: 500px;
            text-align: center;
            background-color: white;
            padding: 45px 35px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .error-code {
            font-size: 72px;
            font-weight: bold;
            color: #dc2626;
            margin-bottom: 10px;
        }

        h1 {
            font-size: 24px;
            margin-bottom: 12px;
        }

        p {
            font-size: 15px;
            line-height: 1.6;
            color: #6b7280;
            margin-bottom: 25px;
        }

        .back-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
        }

        .back-button:hover {
            background-color: #1d4ed8;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-code">403</div>

        <h1>Akses Ditolak</h1>

        <p>
            Maaf, Anda tidak memiliki izin untuk mengakses halaman atau melakukan tindakan ini.
        </p>

        @auth
            <a href="{{ route('dashboard') }}" class="back-button">
                Kembali ke Dashboard
            </a>
        @else
            <a href="{{ url('/') }}" class="back-button">
                Kembali ke Halaman Utama
            </a>
        @endauth
    </div>
</body>
</html>