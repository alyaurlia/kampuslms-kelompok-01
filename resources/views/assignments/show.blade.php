{{-- Variabel dari AssignmentController@show: $assignment, $course, $role --}}
<x-layout :title="$assignment->title">

    @php
        $user      = auth()->user();
        $canEdit   = Route::has($role . '.tugas.edit') && $user->can('update', $assignment);
        $canDelete = Route::has($role . '.tugas.destroy') && $user->can('delete', $assignment);
    @endphp

    <a href="{{ route($role . '.mata-kuliah.tugas.index', $course) }}" class="mk-back-link">
        &larr; Kembali ke Daftar Tugas
    </a>

    <article class="mk-card">
        <div class="mk-body">
            <h1 class="mk-title">{{ $assignment->title }}</h1>

            <div class="mk-meta-row">
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Mata Kuliah</span>
                    <span class="mk-meta-value">{{ $course->name }}</span>
                </div>
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Batas Waktu</span>
                    <span class="mk-meta-value">{{ $assignment->due_at->translatedFormat('d F Y, H:i') }}</span>
                </div>
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Nilai Maksimal</span>
                    <span class="mk-meta-value">{{ $assignment->max_score }}</span>
                </div>
                <div class="mk-meta-item">
                    <span class="mk-meta-label">Terlambat</span>
                    <span class="mk-meta-value">{{ $assignment->allow_late ? 'Diizinkan' : 'Tidak diizinkan' }}</span>
                </div>
                @if ($role === 'dosen')
                    <div class="mk-meta-item">
                        <span class="mk-meta-label">Status</span>
                        <span class="mk-meta-value">{{ ucfirst($assignment->status) }}</span>
                    </div>
                @endif
            </div>

            <h2 class="mk-desc-label">Instruksi</h2>
            <p class="mk-desc-text">{!! nl2br(e($assignment->instructions)) !!}</p>

            @if ($assignment->attachment_url)
                <h2 class="mk-desc-label mk-desc-label--spaced">Lampiran</h2>
                <p class="mk-desc-text">
                    <a href="{{ $assignment->attachment_url }}" target="_blank" rel="noopener">
                        {{ $assignment->original_name }}
                    </a>
                    @if ($assignment->file_size)
                        ({{ number_format($assignment->file_size / 1024, 0) }} KB)
                    @endif
                </p>
            @endif

            @if ($canEdit || $canDelete)
                <div class="mk-form-actions">
                    @if ($canEdit)
                        <a href="{{ route($role . '.tugas.edit', $assignment) }}" class="mk-btn-primary">Edit</a>
                    @endif

                    @if ($canDelete)
                        <form action="{{ route($role . '.tugas.destroy', $assignment) }}" method="POST"
                              onsubmit="return confirm('Yakin ingin menghapus tugas ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="mk-btn-danger">Hapus</button>
                        </form>
                    @endif
                </div>
            @endif
        </div>
    </article>

</x-layout>