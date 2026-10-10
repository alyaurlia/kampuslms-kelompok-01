{{-- Variabel dari MaterialController@show: $material, $course, $role --}}
<x-layout :title="$material->title">

    @php
        $user      = auth()->user();
        $canEdit   = Route::has($role . '.materi.edit') && $user->can('update', $material);
        $canDelete = Route::has($role . '.materi.destroy') && $user->can('delete', $material);
    @endphp

    <a href="{{ route($role . '.mata-kuliah.materi.index', $course) }}" class="mk-back-link">
        &larr; Kembali ke Daftar Materi
    </a>

    <article class="mk-card">
        <div class="mk-body">
            <h1 class="mk-title">{{ $material->title }}</h1>

            <div class="mk-meta-row">
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Mata Kuliah</span>
                    <span class="mk-meta-value">{{ $course->name }}</span>
                </div>
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Tipe</span>
                    <span class="mk-meta-value">{{ $material->type === 'file' ? 'Berkas' : 'Tautan' }}</span>
                </div>
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Diunggah oleh</span>
                    <span class="mk-meta-value">{{ $material->uploader->name ?? '-' }}</span>
                </div>
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Tanggal</span>
                    <span class="mk-meta-value">{{ $material->created_at->translatedFormat('d F Y') }}</span>
                </div>
            </div>

            <h2 class="mk-desc-label">Deskripsi</h2>
            @if (filled($material->description))
                <p class="mk-desc-text">{!! nl2br(e($material->description)) !!}</p>
            @else
                <p class="mk-desc-text">Tidak ada deskripsi.</p>
            @endif

            <div class="mk-form-actions">
                @if ($material->url)
                    <a href="{{ $material->url }}" target="_blank" rel="noopener" class="mk-btn-primary">
                        {{ $material->type === 'file' ? 'Unduh / Buka Berkas' : 'Buka Tautan' }}
                    </a>
                @endif

                @if ($canEdit)
                    <a href="{{ route($role . '.materi.edit', $material) }}" class="mk-btn-ghost">Edit</a>
                @endif

                @if ($canDelete)
                    <form action="{{ route($role . '.materi.destroy', $material) }}" method="POST"
                          onsubmit="return confirm('Yakin ingin menghapus materi ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="mk-btn-danger">Hapus</button>
                    </form>
                @endif
            </div>

            @if ($material->type === 'file' && $material->original_name)
                <p class="mk-hint">
                    {{ $material->original_name }}
                    @if ($material->file_size)
                        ({{ number_format($material->file_size / 1024, 0) }} KB)
                    @endif
                </p>
            @endif
        </div>
    </article>

</x-layout>