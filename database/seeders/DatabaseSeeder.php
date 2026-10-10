<x-layout title="Tentang Kelompok" full-footer>

    {{-- Hero --}}
    <div class="hero-tentang">
        <h1>KampusLMS</h1>
        <p>
            LMS Kampus merupakan platform pembelajaran digital yang digunakan untuk mendukung kegiatan perkuliahan antara mahasiswa dan dosen. Melalui LMS, mahasiswa dapat mengakses materi pembelajaran, mengumpulkan tugas, mengikuti kuis, serta memperoleh informasi terkait perkuliahan, sedangkan dosen dapat mengelola materi dan aktivitas pembelajaran. Dengan adanya LMS, proses pembelajaran dapat dilakukan secara lebih mudah, terorganisir, dan terstruktur dalam satu platform.
        </p>
    </div>

    {{-- Tim / Kelompok --}}
    <h2 class="tentang-heading">Kelompok 01</h2>
    <div class="tim-grid">
        @php
            $anggota = [
                ['nama' => 'Ade Putri Amanda', 'nim' => '10241002'],
                ['nama' => 'Adelia Isra Ekaputri', 'nim' => '10241004'],
                ['nama' => 'Adelia Cyntia Renata', 'nim' => '10241003'],
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