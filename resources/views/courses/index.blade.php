{{--
    View index: daftar mata kuliah dalam bentuk grid kartu.

    Style kartu (.mk-*) didefinisikan terpusat di resources/css/components/courses.css.

    $mataKuliah hasil paginate() dari Eloquent: setiap elemen adalah OBJEK model.

    $routePrefix dikirim controller (mis. 'admin.mata-kuliah') supaya view yang
    sama melayani admin, dosen, dan mahasiswa. Tombol Tambah/Edit/Hapus hanya
    tampil kalau route-nya ada untuk peran tersebut DAN CoursePolicy mengizinkan.

    Flash message (success/error) ditampilkan oleh components/layout.blade.php,
    jadi tidak perlu ditulis lagi di sini.
--}}
<x-layout title="Daftar Mata Kuliah">

    @php
        $user      = auth()->user();
        $canCreate = Route::has($routePrefix . '.create')
            && $user->can('create', \App\Models\Course::class);
    @endphp

    <div class="mk-page-header">
        <h1 class="mk-page-title">Daftar Mata Kuliah</h1>

        @if ($canCreate)
            <a href="{{ route($routePrefix . '.create') }}" class="mk-btn-add">
                + Tambah Mata Kuliah
            </a>
        @endif
    </div>

    {{-- Form pencarian & filter status --}}
    <form method="GET" action="{{ route($routePrefix . '.index') }}" class="mk-filter">

        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari kode atau nama mata kuliah..."
            class="mk-filter-input"
        >

        <select name="status" class="mk-filter-select">
            <option value="">-- Semua Status --</option>
            @foreach (['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'] as $value => $label)
                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
            @endforeach
        </select>

        <button type="submit" class="mk-filter-btn">
            Cari
        </button>

        @if (request('q') || request('status'))
            <a href="{{ route($routePrefix . '.index') }}" class="mk-filter-reset">
                Reset
            </a>
        @endif
    </form>

    @if ($mataKuliah->isEmpty())
        <section class="mk-empty">
            <p>Belum ada data mata kuliah.</p>
        </section>
    @else
        <div class="mk-grid">
            @foreach ($mataKuliah as $mk)
                @php
                    $mkPatterns = ['diamond', 'triangle', 'circle', 'diamond', 'triangle'];
                    $mkIndex    = (crc32($mk->code) % 5) + 1;
                    $mkPattern  = $mkPatterns[$mkIndex - 1];

                    // Tombol hanya muncul bila route-nya ada untuk peran ini
                    // DAN CoursePolicy mengizinkan (update/delete = admin saja).
                    $canEdit   = Route::has($routePrefix . '.edit')
                        && $user->can('update', $mk);
                    $canDelete = Route::has($routePrefix . '.destroy')
                        && $user->can('delete', $mk);
                @endphp

                <div class="mk-grid-card">

                    {{-- Area klik untuk lihat detail: banner + judul + meta --}}
                    <a href="{{ route($routePrefix . '.show', $mk) }}" class="mk-grid-link">
                        <div class="mk-grid-banner mk-pattern-{{ $mkPattern }} mk-bg-{{ $mkIndex }}">
                            <span class="mk-badge">{{ $mk->code }}</span>
                            <span class="mk-grid-arrow" aria-hidden="true">&#10132;</span>
                        </div>

                        <div class="mk-grid-body">
                            <h2 class="mk-grid-title">{{ $mk->name }}</h2>
                            <p class="mk-grid-meta">{{ $mk->sks }} SKS &middot; {{ $mk->lecturer->name ?? '-' }}</p>
                        </div>
                    </a>

                    {{-- Footer aksi: di luar <a> di atas supaya klik edit/hapus
                         tidak ikut men-trigger navigasi ke halaman detail. --}}
                    @if ($canEdit || $canDelete)
                        <div class="mk-grid-actions">
                            @if ($canEdit)
                                <a href="{{ route($routePrefix . '.edit', $mk) }}" class="mk-action-edit">
                                    Edit
                                </a>
                            @endif

                            @if ($canDelete)
                                <form action="{{ route($routePrefix . '.destroy', $mk) }}" method="POST"
                                      class="mk-action-form"
                                      onsubmit="return confirm('Yakin ingin menghapus mata kuliah ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="mk-action-delete">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

        {{-- Pagination: controller sudah memakai withQueryString(), jadi ?q=...&status=...
             ikut terbawa. Markup bootstrap-4 dipilih karena proyek ini tidak memakai Tailwind. --}}
        <div class="mk-pagination">
            {{ $mataKuliah->links('pagination::bootstrap-4') }}
        </div>
    @endif

</x-layout>