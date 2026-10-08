{{--
    View index: menampilkan daftar mata kuliah dalam bentuk grid kartu,
    meniru gaya kartu "Kursusku" di Beranda ITK (banner bermotif + badge
    kode + tombol aksi bulat).

    Style kartu (.mk-grid, .mk-grid-card, dst.) didefinisikan terpusat di
    resources/css/courses.css supaya konsisten dan tidak diduplikasi dengan
    courses/show.blade.php.

    $mataKuliah sekarang hasil paginate() dari Eloquent, jadi setiap
    elemennya adalah OBJEK model (akses pakai ->), bukan array asosiatif.

    $routePrefix dikirim controller (mis. 'admin.mata-kuliah') supaya view
    yang sama melayani admin, dosen, dan mahasiswa. Tombol Tambah/Edit/Hapus
    hanya tampil kalau route-nya memang ada untuk peran tersebut.
--}}
<x-layout title="Daftar Mata Kuliah">

    @php
        $user      = auth()->user();
        $canCreate = Route::has($routePrefix . '.create');
        $canDelete = Route::has($routePrefix . '.destroy');
    @endphp

    <div class="mk-page-header">
        <h1 class="mk-page-title">Daftar Mata Kuliah</h1>

        @if ($canCreate)
            <a href="{{ route($routePrefix . '.create') }}" class="mk-btn-add">
                + Tambah Mata Kuliah
            </a>
        @endif
    </div>

    {{-- Notifikasi sukses dari redirect create/update/delete --}}
    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

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
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
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
                    $mkIndex = (crc32($mk->code) % 5) + 1;
                    $mkPattern = $mkPatterns[$mkIndex - 1];

                    // Edit: route harus ada untuk peran ini DAN user adalah admin
                    // atau dosen pengampu (sama dengan canManageCourse di controller).
                    $canEdit = Route::has($routePrefix . '.edit')
                        && ($user?->role === 'admin' || $mk->lecturer_id === $user?->id);
                @endphp

                <div class="mk-grid-card">

                    {{-- Area klik untuk lihat detail: banner + judul + meta --}}
                    <a href="{{ route($routePrefix . '.show', $mk->id) }}" class="mk-grid-link">
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
                                <a href="{{ route($routePrefix . '.edit', $mk->id) }}" class="mk-action-edit">
                                    Edit
                                </a>
                            @endif

                            @if ($canDelete)
                                <form action="{{ route($routePrefix . '.destroy', $mk->id) }}" method="POST"
                                      class="mk-action-form"
                                      onsubmit="return confirm('Yakin ingin menghapus mata kuliah {{ $mk->name }}?');">
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

        {{-- Pagination — otomatis membawa query string (?q=...&status=...)
             karena controller sudah pakai withQueryString() --}}
        <div class="mk-pagination">
            {{ $mataKuliah->links() }}
        </div>
    @endif

</x-layout>