<x-layout title="Tentang Kelompok">

    <style>
        .hero-tentang {
            background: linear-gradient(135deg, var(--color-primary, #7c1d3f) 0%, #4a0f27 100%);
            border-radius: 14px;
            padding: 2.5rem 2rem;
            color: #fff;
            margin-bottom: 2rem;
        }
        .hero-tentang h1 { font-size: 1.9rem; margin: 0 0 0.5rem; }
        .hero-tentang p { font-size: 0.95rem; opacity: 0.9; margin: 0; max-width: 640px; line-height: 1.6; }

        .fitur-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.25rem;
            margin-bottom: 2rem;
        }
        .fitur-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            padding: 1.25rem;
            text-align: center;
        }
        .fitur-icon { font-size: 1.8rem; margin-bottom: 0.5rem; }
        .fitur-title { font-size: 0.9rem; font-weight: 700; margin: 0 0 0.3rem; }
        .fitur-desc { font-size: 0.78rem; color: var(--color-ink-soft); line-height: 1.5; margin: 0; }

        .tim-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.25rem;
        }
        .tim-card {
            background: var(--color-surface);
            border: 1px solid var(--color-border);
            border-radius: 12px;
            padding: 1.5rem 1rem;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .tim-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.08);
        }
        .tim-avatar {
            width: 56px; height: 56px; border-radius: 50%;
            background: linear-gradient(135deg, var(--color-primary, #7c1d3f), #b34a6f);
            color: #fff; font-weight: 700; font-size: 1.1rem;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 0.75rem;
        }
        .tim-nama { font-size: 0.9rem; font-weight: 700; margin: 0 0 0.2rem; }
        .tim-nim { font-size: 0.78rem; color: var(--color-ink-soft); margin: 0; }
    </style>

    {{-- Hero --}}
    <div class="hero-tentang">
        <h1>KampusLMS</h1>
        <p>
            LMS Kampus merupakan platform pembelajaran digital yang digunakan untuk mendukung kegiatan perkuliahan antara mahasiswa dan dosen. Melalui LMS, mahasiswa dapat mengakses materi pembelajaran, mengumpulkan tugas, mengikuti kuis, serta memperoleh informasi terkait perkuliahan, sedangkan dosen dapat mengelola materi dan aktivitas pembelajaran. Dengan adanya LMS, proses pembelajaran dapat dilakukan secara lebih mudah, terorganisir, dan terstruktur dalam satu platform.
        </p>
    </div>

    {{-- Tim / Kelompok --}}
    <h2 style="font-size:1.2rem; margin-bottom:1rem;">Kelompok 01</h2>
    <div class="tim-grid">
        @php
            $anggota = [
                ['nama' => 'Ade Putri Amanda', 'nim' => '10241002'],
                ['nama' => 'Adelia Isra Ekaputri', 'nim' => '10241004'],
                ['nama' => 'Adelia Cyntia Renata', 'nim' => '10241006'],
                ['nama' => 'Alya Auralia', 'nim' => '10241008'],
                ['nama' => 'Andika Putra Pratama', 'nim' => '10241010'],
            ];
        @endphp

        @foreach ($anggota as $orang)
            @php
                $inisial = collect(explode(' ', $orang['nama']))->map(fn($k) => strtoupper($k[0]))->take(2)->join('');
            @endphp
            <div class="tim-card">
                <div class="tim-avatar">{{ $inisial }}</div>
                <p class="tim-nama">{{ $orang['nama'] }}</p>
                <p class="tim-nim">{{ $orang['nim'] }}</p>
            </div>
        @endforeach
    </div>

</x-layout>