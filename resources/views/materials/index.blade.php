{{-- Variabel dari MaterialController@index: $course, $materials (paginator), $role --}}
<x-layout title="Materi — {{ $course->name }}">

    @php
        $user      = auth()->user();
        $canCreate = Route::has($role . '.mata-kuliah.materi.create')
            && $user->can('create', [\App\Models\Material::class, $course]);
    @endphp

    <a href="{{ route($role . '.mata-kuliah.show', $course) }}" class="mk-back-link">
        &larr; Kembali ke {{ $course->name }}
    </a>

    <div class="mk-page-header">
        <h1 class="mk-page-title">Materi &mdash; {{ $course->name }}</h1>

        @if ($canCreate)
            <a href="{{ route($role . '.mata-kuliah.materi.create', $course) }}" class="mk-btn-add">
                + Tambah Materi
            </a>
        @endif
    </div>

    @if ($materials->isEmpty())
        <section class="mk-empty">
            <p>Belum ada materi untuk mata kuliah ini.</p>
        </section>
    @else
        <div class="mk-list">
            @foreach ($materials as $material)
                @php
                    $canEdit   = Route::has($role . '.materi.edit') && $user->can('update', $material);
                    $canDelete = Route::has($role . '.materi.destroy') && $user->can('delete', $material);
                @endphp

                <div class="mk-list-item">
                    <div class="mk-list-main">
                        <a href="{{ route($role . '.materi.show', $material) }}" class="mk-list-title">
                            {{ $material->title }}
                        </a>
                        <p class="mk-list-meta">
                            <span class="mk-chip mk-chip--{{ $material->type }}">
                                {{ $material->type === 'file' ? 'Berkas' : 'Tautan' }}
                            </span>
                            Diunggah {{ $material->created_at->translatedFormat('d F Y') }}
                            @if ($material->type === 'file' && $material->original_name)
                                &middot; {{ $material->original_name }}
                            @endif
                        </p>
                    </div>

                    @if ($canEdit || $canDelete)
                        <div class="mk-list-actions">
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
                    @endif
                </div>
            @endforeach
        </div>

        <div class="mk-pagination">
            {{ $materials->links('pagination::bootstrap-4') }}
        </div>
    @endif

</x-layout>