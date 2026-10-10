{{-- Variabel dari AssignmentController@show: $assignment, $course, $role --}}
<x-layout :title="$assignment->title">

@php
    $user      = auth()->user();
    $canEdit   = Route::has($role . '.tugas.edit') && $user->can('update', $assignment);
    $canDelete = Route::has($role . '.tugas.destroy') && $user->can('delete', $assignment);
    $mySubmission = $mySubmission ?? null;
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
                {{-- Status (draft/published) terlihat oleh dosen dan admin; mahasiswa hanya melihat tugas published --}}
                @if (in_array($role, ['dosen', 'admin']))
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

    @if ($role === 'mahasiswa')
    <section class="mk-card">
        <div class="mk-body">
            <h2 class="mk-desc-label">Pengumpulan Saya</h2>

            @if (session('success'))
                <p>{{ session('success') }}</p>
            @endif
            @error('file')
                <p>{{ $message }}</p>
            @enderror

            @if ($mySubmission)
                <p>
                    Dikumpulkan {{ $mySubmission->submitted_at->translatedFormat('d F Y, H:i') }}
                    @if ($mySubmission->is_late)
                        <strong>(Terlambat)</strong>
                    @endif
                </p>
                <p>
                    <a href="{{ route('pengumpulan.unduh', $mySubmission) }}">
                        {{ $mySubmission->original_name }}
                    </a>
                </p>

                @if ($mySubmission->grade && $mySubmission->grade->is_published)
                    <p>Nilai: {{ $mySubmission->grade->score }} / {{ $assignment->max_score }}</p>
                    @if ($mySubmission->grade->feedback)
                        <p>{!! nl2br(e($mySubmission->grade->feedback)) !!}</p>
                    @endif
                @endif
            @else
                @can('submit', $assignment)
                    @if (now()->gt($assignment->due_at))
                        <p><strong>Batas waktu sudah lewat. Pengumpulan akan ditandai terlambat.</strong></p>
                    @endif

                    <form method="POST"
                          action="{{ route('mahasiswa.tugas.kumpul', $assignment) }}"
                          enctype="multipart/form-data">
                        @csrf
                        <input type="file" name="file" required>
                        <textarea name="note" placeholder="Catatan (opsional)">{{ old('note') }}</textarea>
                        <button type="submit" class="mk-btn-primary">Kumpulkan</button>
                    </form>
                @endcan
            @endif
        </div>
    </section>
@endif

</x-layout>