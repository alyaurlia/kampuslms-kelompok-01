
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
            --color-bg: #FFF8F2;
            --color-surface: #FFFFFF;
            --color-ink: #2B1B22;
            --color-ink-soft: #8A6B75;
            --color-primary: #CB125E;
            --color-primary-dark: #8F0D42;
            --color-border: #F3D9C4;
            --color-accent: #DEA249;

            /* Palet kartu mata kuliah (dipakai di index & show lewat
               crc32(kode) % 5), diturunkan dari color palette
               Sour Apple - Apricot Blossom - Pavilion Peach -
               Candy Heart - Heather Berry - Paper Flower. */
            --color-card-1-from: #CB125E; --color-card-1-to: #8F0D42; /* paper flower (primary) */
            --color-card-2-from: #DEA249; --color-card-2-to: #B87F30; /* pavilion peach */
            --color-card-3-from: #A9BF55; --color-card-3-to: #839343; /* sour apple */
            --color-card-4-from: #F85988; --color-card-4-to: #D93E6C; /* heather berry */
            --color-card-5-from: #EBD22F; --color-card-5-to: #C9AF1F; /* apricot blossom */
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
            color: #F3D9C4;
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

        /* =========================================================
           Komponen kartu mata kuliah (dipakai bersama oleh
           courses/index.blade.php dan courses/show.blade.php)
           supaya tidak ada CSS yang diduplikasi di dua tempat.
           ========================================================= */

        .mk-back-link {
            font-family: Arial, sans-serif;
            font-size: 0.85rem;
            color: var(--color-ink-soft);
            text-decoration: none;
            display: inline-block;
            margin-bottom: 0.75rem;
        }
        .mk-back-link:hover { text-decoration: underline; }

        /* --- motif banner, dipakai baik di banner besar (show)
               maupun banner kecil di grid (index) --- */
        .mk-pattern-diamond::before {
            background-image:
                linear-gradient(45deg, rgba(255,255,255,0.25) 25%, transparent 25%),
                linear-gradient(-45deg, rgba(255,255,255,0.25) 25%, transparent 25%);
            background-size: 32px 32px;
        }
        .mk-pattern-triangle::before {
            background-image:
                linear-gradient(60deg, rgba(255,255,255,0.2) 25%, transparent 25.5%),
                linear-gradient(-60deg, rgba(255,255,255,0.2) 25%, transparent 25.5%);
            background-size: 36px 42px;
        }
        .mk-pattern-circle::before {
            background-image: radial-gradient(circle, rgba(255,255,255,0.25) 2px, transparent 2.5px);
            background-size: 24px 24px;
        }
        [class^="mk-pattern-"]::before,
        [class*=" mk-pattern-"]::before {
            content: "";
            position: absolute;
            inset: 0;
            opacity: 0.35;
        }

        .mk-badge {
            position: absolute;
            top: 16px;
            left: 16px;
            background: rgba(255, 255, 255, 0.92);
            color: var(--color-primary-dark);
            font-family: Arial, sans-serif;
            font-weight: 700;
            font-size: 0.75rem;
            letter-spacing: 0.02em;
            padding: 4px 12px;
            border-radius: 6px;
        }

        /* --- halaman detail (show.blade.php) --- */
        .mk-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 10px;
            overflow: hidden;
        }
        .mk-banner {
            position: relative;
            height: 170px;
        }
        .mk-body {
            padding: 1.5rem;
            font-family: Arial, sans-serif;
        }
        .mk-title {
            font-size: 1.5rem;
            margin: 0 0 1.25rem;
            color: var(--color-ink);
        }
        .mk-meta-row {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
            padding-bottom: 1.25rem;
            margin-bottom: 1.25rem;
            border-bottom: 1px solid var(--color-border);
        }
        .mk-meta-item {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }
        .mk-meta-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--color-ink-soft);
        }
        .mk-meta-value {
            font-size: 0.95rem;
            color: var(--color-ink);
        }
        .mk-desc-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: var(--color-ink-soft);
            margin: 0 0 0.5rem;
        }
        .mk-desc-text {
            font-size: 0.9rem;
            line-height: 1.6;
            margin: 0;
            color: var(--color-ink);
        }

        /* --- halaman daftar (index.blade.php) --- */
        .mk-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 1.5rem;
        }
        .mk-grid-card {
            display: block;
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 10px;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            transition: box-shadow 0.15s ease, transform 0.15s ease;
        }
        .mk-grid-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }
        .mk-grid-banner {
            position: relative;
            height: 120px;
        }
        .mk-grid-arrow {
            position: absolute;
            bottom: 12px;
            right: 12px;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.92);
            color: var(--color-primary-dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
        }
        .mk-grid-body {
            padding: 1rem 1.1rem 1.2rem;
            font-family: Arial, sans-serif;
        }
        .mk-grid-title {
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 0.4rem;
            color: var(--color-primary);
            line-height: 1.35;
        }
        .mk-grid-meta {
            font-size: 0.8rem;
            color: var(--color-ink-soft);
            margin: 0;
        }
    </style>
</head>
<body>

    <header class="app-header">
    <span class="brand">LMS Kampus</span>
   <nav>
    <a href="{{ route('dashboard') }}"
       style="{{ request()->routeIs('dashboard') ? 'color:#fff; text-decoration:underline;' : '' }}">
        Dashboard
    </a>
    <a href="{{ route('mata-kuliah.index') }}"
       style="{{ request()->routeIs('mata-kuliah.*') ? 'color:#fff; text-decoration:underline;' : '' }}">
        Mata Kuliah
    </a>
    <a href="{{ route('tentang') }}"
       style="{{ request()->routeIs('tentang') ? 'color:#fff; text-decoration:underline;' : '' }}">
        Tentang
    </a>
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