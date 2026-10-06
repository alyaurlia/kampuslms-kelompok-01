@php
    $rp = \Illuminate\Support\Str::before(request()->route()->getName(), 'mata-kuliah');
@endphp

<x-layout :title="$mataKuliah->name">

    <a href="{{ route($rp . 'mata-kuliah.index') }}" class="mk-back-link">
        &larr; Kembali ke Daftar Mata Kuliah
    </a>

    @php
        $mkPatterns = ['diamond', 'triangle', 'circle', 'diamond', 'triangle'];
        $mkIndex = (crc32($mataKuliah->code) % 5) + 1;
        $mkPattern = $mkPatterns[$mkIndex - 1];
    @endphp

    <div class="mk-card">
        <div class="mk-banner mk-pattern-{{ $mkPattern }}"
             style="background: linear-gradient(135deg, var(--color-card-{{ $mkIndex }}-from), var(--color-card-{{ $mkIndex }}-to));">
            <span class="mk-badge">{{ $mataKuliah->code }}</span>
        </div>

        <div class="mk-body">
            <h1 class="mk-title">{{ $mataKuliah->name }}</h1>

            <div class="mk-meta-row">
                <div class="mk-meta-item">
                    <span class="mk-meta-label">SKS</span>
                    <span class="mk-meta-value">{{ $mataKuliah->sks }}</span>
                </div>
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Dosen Pengampu</span>
                    <span class="mk-meta-value">{{ $mataKuliah->lecturer->name ?? '-' }}</span>
                </div>
            </div>

            <div class="mk-desc">
                <h2 class="mk-desc-label">Deskripsi</h2>
                <p class="mk-desc-text">{{ $mataKuliah->description }}</p>
            </div>
        </div>
    </div>

</x-layout>