
@props(['title' => 'LMS Kampus'])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Title dinamis per halaman, di-escape otomatis oleh {{ }} --}}
    <title>{{ $title }} — LMS Kampus</title>

    <style>
        :root {
            --color-bg: #F5F6F8;
            --color-surface: #FFFFFF;
            --color-ink: #1C2430;
            --color-ink-soft: #5B6472;
            --color-primary: #234E70;
            --color-primary-dark: #16324A;
            --color-border: #DDE1E7;
            --color-accent: #C98A3C;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Georgia', 'Times New Roman', serif;
            background: var(--color-bg);
            color: var(--color-ink);
            line-height: 1.55;
        }

        header.app-header {
            background: var(--color-primary-dark);
            color: #fff;
            padding: 1.1rem 2rem;
            display: flex;
            align-items: baseline;
            justify-content: space-between;
        }

        header.app-header .brand {
            font-size: 1.15rem;
            font-weight: 700;
            letter-spacing: 0.02em;
        }

        header.app-header nav a {
            color: #E4E9EF;
            text-decoration: none;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.9rem;
            margin-left: 1.5rem;
        }

        header.app-header nav a:hover {
            color: #fff;
            text-decoration: underline;
        }

        main {
            max-width: 920px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem 4rem;
        }

        footer.app-footer {
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 0.8rem;
            color: var(--color-ink-soft);
            padding: 1.5rem;
        }
    </style>
</head>
<body>

    <header class="app-header">
        <span class="brand">LMS Kampus</span>
        <nav>
            <a href="{{ route('dashboard') }}">Dasbor</a>
        </nav>
    </header>

    <main>
        {{-- Slot default: tempat konten tiap halaman disisipkan --}}
        {{ $slot }}
    </main>

    <footer class="app-footer">
        &copy; {{ date('Y') }} LMS Kampus
    </footer>

</body>
</html>