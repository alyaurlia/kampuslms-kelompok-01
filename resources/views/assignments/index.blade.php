{{-- Variabel dari AssignmentController@index: $course, $assignments (paginator), $role --}}
<x-layout title="Tugas — {{ $course->name }}">

    @php
        $user      = auth()->user();
        $canCreate = Route::has($role . '.mata-kuliah.tugas.create')
            && $user->can('create', [\App\Models\Assignment::class, $course]);
    @endphp

    <a href="{{ route($role . '.mata-kuliah.show', $course) }}" class="mk-back-link">
        &larr; Kembali ke {{ $course->name }}
    </a>

    <div class="mk-page-header">
        <h1 class="mk-page-title">Tugas &mdash; {{ $course->name }}</h1>

        @if ($canCreate)
            <a href="{{ route($role . '.mata-kuliah.tugas.create', $course) }}" class="mk-btn-add">
                + Tambah Tugas
            </a>
        @endif
    </div>

    @if ($assignments->isEmpty())
        <section class="mk-empty">
            <p>Belum ada tugas untuk mata kuliah ini.</p>
        </section>
    @else
        <div class="mk-list">
            @foreach ($assignments as $assignment)
                @php
                    $canEdit   = Route::has($role . '.tugas.edit') && $user->can('update', $assignment);
                    $canDelete = Route::has($role . '.tugas.destroy') && $user->can('delete', $assignment);
                @endphp

                <div class="mk-list-item">
                    <div class="mk-list-main">
                        <a href="{{ route($role . '.tugas.show', $assignment) }}" class="mk-list-title">
                            {{ $assignment->title }}
                        </a>
                        <p class="mk-list-meta">
                            {{-- Status (draft/published) terlihat oleh dosen dan admin --}}
                            @if (in_array($role, ['dosen', 'admin']))
                                <span class="mk-chip mk-chip--{{ $assignment->status }}">{{ ucfirst($assignment->status) }}</span>
                            @endif
                            Batas: {{ $assignment->due_at->translatedFormat('d F Y, H:i') }}
                            &middot; Nilai maks. {{ $assignment->max_score }}
                            @if ($assignment->file_path)
                                &middot; Ada lampiran
                            @endif
                        </p>
                    </div>

                    @if ($canEdit || $canDelete)
                        <div class="mk-list-actions">
                            @if ($canEdit)
                                <a href="{{ route($role . '.tugas.edit', $assignment) }}" class="mk-btn-ghost">Edit</a>
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
            @endforeach
        </div>

        <div class="mk-pagination">
            {{ $assignments->links('pagination::bootstrap-4') }}
        </div>
    @endif

</x-layout>