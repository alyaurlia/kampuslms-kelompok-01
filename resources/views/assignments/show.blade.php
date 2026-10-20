{{-- Variabel dari AssignmentController@show: $assignment, $course, $role, $mySubmission, $submissions --}}
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

    <article class="mk-card as-card">
        <div class="mk-body">

            {{-- Kepala: ikon, judul tugas, dan status (draft/published hanya untuk dosen & admin) --}}
            <div class="as-head">
                <span class="as-head__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><path d="M14 2v6h6"></path><path d="M8 13h8"></path><path d="M8 17h5"></path></svg>
                </span>
                <div class="as-head__text">
                    <p class="as-eyebrow">Tugas</p>
                    <h1 class="mk-title as-title">{{ $assignment->title }}</h1>
                </div>
                @if (in_array($role, ['dosen', 'admin']))
                    <span class="mk-chip mk-chip--{{ $assignment->status }} as-status">{{ ucfirst($assignment->status) }}</span>
                @endif
            </div>

            {{-- Ringkasan: satu baris info di dalam kartu (tanpa kartu terpisah) --}}
            <div class="as-info">
                <div class="as-info__item">
                    <span class="as-info__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21.5v-16z"></path><path d="M4 5.5v16"></path><path d="M8 7h8"></path><path d="M8 11h6"></path></svg>
                    </span>
                    <div class="as-info__text">
                        <span class="as-info__label">Mata Kuliah</span>
                        <span class="as-info__value">{{ $course->name }}</span>
                    </div>
                </div>
                <div class="as-info__item">
                    <span class="as-info__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                    </span>
                    <div class="as-info__text">
                        <span class="as-info__label">Batas Waktu</span>
                        <span class="as-info__value">{{ $assignment->due_at?->translatedFormat('d F Y, H:i') ?? 'Tanpa batas waktu' }}</span>
                    </div>
                </div>
                <div class="as-info__item">
                    <span class="as-info__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"></path></svg>
                    </span>
                    <div class="as-info__text">
                        <span class="as-info__label">Nilai Maksimal</span>
                        <span class="as-info__value">{{ $assignment->max_score }}</span>
                    </div>
                </div>
                <div class="as-info__item">
                    <span class="as-info__icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M12 3l9 16H3L12 3z"></path><path d="M12 10v4"></path><path d="M12 17h.01"></path></svg>
                    </span>
                    <div class="as-info__text">
                        <span class="as-info__label">Terlambat</span>
                        <span class="as-info__value">{{ $assignment->allow_late ? 'Diizinkan' : 'Tidak diizinkan' }}</span>
                    </div>
                </div>
            </div>

            <div class="as-section">
                <h2 class="mk-desc-label">Instruksi</h2>
                <p class="mk-desc-text as-text">{!! nl2br(e($assignment->instructions)) !!}</p>
            </div>

            @if ($assignment->attachment_url)
                <div class="as-section">
                    <h2 class="mk-desc-label">Lampiran</h2>
                    {{-- attachment_url = route unduh (tugas.lampiran), berkas dikirim sebagai lampiran: tanpa tab baru --}}
                    <a href="{{ $assignment->attachment_url }}" class="as-file">
                        <span class="as-file__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24"><path d="M21 12.5l-8.6 8.6a5 5 0 0 1-7.1-7.1l8.6-8.6a3.3 3.3 0 0 1 4.7 4.7l-8.6 8.6a1.7 1.7 0 0 1-2.4-2.4l7.9-7.9"></path></svg>
                        </span>
                        <span class="as-file__name">{{ $assignment->original_name }}</span>
                        @if ($assignment->file_size_human)
                            <span class="as-file__size">{{ $assignment->file_size_human }}</span>
                        @endif
                    </a>
                </div>
            @endif

            @if ($canEdit || $canDelete)
                <div class="mk-form-actions as-actions">
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

    {{-- Daftar pengumpulan + penilaian: hanya dosen & admin ($submissions dari controller) --}}
    @if (in_array($role, ['dosen', 'admin']))
        @include('assignments._grading')
    @endif

    @if ($role === 'mahasiswa')
    @php
        // Nilai "basi": mahasiswa mengumpulkan ulang SETELAH dinilai.
        $myGrade = $mySubmission?->grade;
        $staleGrade = $myGrade && $myGrade->graded_at
            && $mySubmission->submitted_at->gt($myGrade->graded_at);
    @endphp
    <section class="mk-card as-card">
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
                    @if ($mySubmission->file_size_human)
                        <small>({{ $mySubmission->file_size_human }})</small>
                    @endif
                </p>

                @if ($mySubmission->revisions->count() > 1)
                    <p><strong>Riwayat pengumpulan</strong></p>
                    <ul>
                        @foreach ($mySubmission->revisions->sortByDesc('version_number') as $revision)
                            <li>
                                Versi {{ $revision->version_number }} &middot;
                                {{ $revision->submitted_at->translatedFormat('d F Y, H:i') }}
                                @if ($revision->is_late)
                                    <strong>(Terlambat)</strong>
                                @endif
                                &middot;
                                <a href="{{ route('pengumpulan.revisi.unduh', [$mySubmission, $revision]) }}">{{ $revision->original_name }}</a>
                                @if ($revision->file_size_human)
                                    ({{ $revision->file_size_human }})
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($myGrade && $myGrade->is_published)
                    <p>Nilai: {{ $myGrade->score }} / {{ $assignment->max_score }}</p>
                    @if ($staleGrade)
                        <p><em>Nilai ini untuk pengumpulan sebelumnya. Pengumpulan terbaru Anda menunggu dinilai ulang.</em></p>
                    @endif
                    @if ($myGrade->feedback)
                        <p>{!! nl2br(e($myGrade->feedback)) !!}</p>
                    @endif
                @endif
            @endif

            {{-- Form kumpul / kumpul ulang. Muncul selama AssignmentPolicy::submit mengizinkan
                 (terdaftar, published, MK active, belum lewat deadline atau allow_late). --}}
            @can('submit', $assignment)
                @if ($assignment->isLateAt(now()))
                    <p><strong>Batas waktu sudah lewat. Pengumpulan akan ditandai terlambat.</strong></p>
                @endif

                @if ($mySubmission)
                    <p><em>Mengumpulkan ulang menambah versi baru. Versi sebelumnya tetap tersimpan di riwayat.</em></p>
                @endif

                <form method="POST"
                      action="{{ route('mahasiswa.tugas.kumpul', $assignment) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" accept=".pdf,.doc,.docx" required>
                    <textarea name="note" placeholder="Catatan (opsional)">{{ old('note', $mySubmission?->note) }}</textarea>
                    <button type="submit" class="mk-btn-primary">
                        {{ $mySubmission ? 'Kumpulkan Ulang' : 'Kumpulkan' }}
                    </button>
                </form>
            @else
                {{-- Form disembunyikan oleh AssignmentPolicy::submit. Beri tahu alasannya bila karena deadline. --}}
                @if (! $assignment->allow_late && $assignment->isLateAt(now()))
                    <p><strong>Batas waktu sudah lewat dan pengumpulan terlambat tidak diizinkan.</strong></p>
                @endif
            @endcan
        </div>
    </section>
@endif

</x-layout>