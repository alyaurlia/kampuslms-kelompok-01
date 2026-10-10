{{--
    View show: detail satu mata kuliah (banner, kode, SKS, dosen, status, deskripsi).

    Style (.mk-*) ada di resources/css/components/courses.css.
    Variabel dari CourseController@show:
      $mataKuliah  -> objek model Course
      $routePrefix -> mis. 'admin.mata-kuliah' (view yang sama dipakai tiap peran)
--}}
<x-layout :title="$mataKuliah->name">

    @php
        $user       = auth()->user();
        $mkPatterns = ['diamond', 'triangle', 'circle', 'diamond', 'triangle'];
        $mkIndex    = (crc32($mataKuliah->code) % 5) + 1;
        $mkPattern  = $mkPatterns[$mkIndex - 1];

        $canEdit = Route::has($routePrefix . '.edit')
            && $user->can('update', $mataKuliah);
    @endphp

    <a href="{{ route($routePrefix . '.index') }}" class="mk-back-link">
        &larr; Kembali ke Daftar Mata Kuliah
    </a>

    <article class="mk-card">

        <div class="mk-banner mk-pattern-{{ $mkPattern }} mk-bg-{{ $mkIndex }}">
            <span class="mk-badge">{{ $mataKuliah->code }}</span>
        </div>

        <div class="mk-body">

            <h1 class="mk-title">{{ $mataKuliah->name }}</h1>

            <div class="mk-meta-row">
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Kode</span>
                    <span class="mk-meta-value">{{ $mataKuliah->code }}</span>
                </div>

                <div class="mk-meta-item">
                    <span class="mk-meta-label">SKS</span>
                    <span class="mk-meta-value">{{ $mataKuliah->sks }} SKS</span>
                </div>

                <div class="mk-meta-item">
                    <span class="mk-meta-label">Dosen Pengampu</span>
                    <span class="mk-meta-value">{{ $mataKuliah->lecturer->name ?? '-' }}</span>
                </div>

                <div class="mk-meta-item">
                    <span class="mk-meta-label">Status</span>
                    <span class="mk-meta-value">{{ ucfirst($mataKuliah->status) }}</span>
                </div>
            </div>

            <h2 class="mk-desc-label">Deskripsi</h2>

            @if (filled($mataKuliah->description))
                {{-- e() meng-escape HTML dulu, baru baris baru diubah jadi <br> --}}
                <p class="mk-desc-text">{!! nl2br(e($mataKuliah->description)) !!}</p>
            @else
                <p class="mk-desc-text">Belum ada deskripsi untuk mata kuliah ini.</p>
            @endif

            @if ($canEdit)
                <div class="form-actions" style="margin-top:1.5rem;">
                    <a href="{{ route($routePrefix . '.edit', $mataKuliah) }}" class="btn btn-primary">
                        Edit Mata Kuliah
                    </a>
                </div>
            @endif

        </div>
    </article>

</x-layout>