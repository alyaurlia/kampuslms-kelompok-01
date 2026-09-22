{{--
    View index: menampilkan daftar mata kuliah dalam bentuk grid kartu,
    meniru gaya kartu "Kursusku" di Beranda ITK (banner bermotif + badge
    kode + tombol aksi bulat).

    Style kartu (.mk-grid, .mk-grid-card, dst.) didefinisikan terpusat di
    layout.blade.php supaya konsisten dan tidak diduplikasi dengan
    courses/show.blade.php.

    $mataKuliah sekarang hasil paginate() dari Eloquent, jadi setiap
    elemennya adalah OBJEK model (akses pakai ->), bukan array asosiatif.
--}}
<x-layout title="Daftar Mata Kuliah">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
        <h1 style="font-size:1.6rem; margin:0;">Daftar Mata Kuliah</h1>

        <a href="{{ route('mata-kuliah.create') }}"
           style="padding:0.5rem 1rem; border-radius:4px; background:var(--color-card-1-from); color:#fff; text-decoration:none; font-family:Arial, sans-serif; font-size:0.9rem;">
            + Tambah Mata Kuliah
        </a>
    </div>

    {{-- Notifikasi sukses dari redirect create/update/delete --}}
    @if (session('success'))
        <div style="background:#e6f4ea; border:1px solid #b7dfc2; border-radius:6px; padding:1rem; margin-bottom:1.5rem; font-family:Arial, sans-serif; color:#1e7e34;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Form pencarian & filter status --}}
    <form method="GET" action="{{ route('mata-kuliah.index') }}"
          style="display:flex; gap:0.75rem; margin-bottom:1.5rem; flex-wrap:wrap;">

        <input
            type="text"
            name="q"
            value="{{ request('q') }}"
            placeholder="Cari kode atau nama mata kuliah..."
            style="flex:1; min-width:200px; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
        >

        <select name="status"
                style="padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
            <option value="">-- Semua Status --</option>
            <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
            <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
        </select>

        <button type="submit"
                style="padding:0.5rem 1rem; border-radius:4px; border:none; background:var(--color-card-1-from); color:#fff; cursor:pointer;">
            Cari
        </button>

        @if (request('q') || request('status'))
            <a href="{{ route('mata-kuliah.index') }}"
               style="padding:0.5rem 1rem; border-radius:4px; border:1px solid var(--color-border); text-decoration:none; color:inherit;">
                Reset
            </a>
        @endif
    </form>

    @if ($mataKuliah->isEmpty())
        <section style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem;">
            <p style="margin:0; color:var(--color-ink-soft); text-align:center; font-family:Arial, sans-serif;">
                Belum ada data mata kuliah.
            </p>
        </section>
    @else
        <div class="mk-grid">
            @foreach ($mataKuliah as $mk)
                @php
                    $mkPatterns = ['diamond', 'triangle', 'circle', 'diamond', 'triangle'];
                    $mkIndex = (crc32($mk->code) % 5) + 1;
                    $mkPattern = $mkPatterns[$mkIndex - 1];
                @endphp

                <div class="mk-grid-card" style="display:flex; flex-direction:column;">

                    {{-- Area klik untuk lihat detail: banner + judul + meta --}}
                    <a href="{{ route('mata-kuliah.show', $mk->id) }}" style="text-decoration:none; color:inherit;">
                        <div class="mk-grid-banner mk-pattern-{{ $mkPattern }}"
                             style="background: linear-gradient(135deg, var(--color-card-{{ $mkIndex }}-from), var(--color-card-{{ $mkIndex }}-to));">
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
                    <div style="display:flex; gap:0.5rem; padding:0 1rem 1rem 1rem; margin-top:auto;">
                        <a href="{{ route('mata-kuliah.edit', $mk->id) }}"
                           style="flex:1; text-align:center; padding:0.4rem; border-radius:4px; border:1px solid var(--color-border); text-decoration:none; color:inherit; font-family:Arial, sans-serif; font-size:0.85rem;">
                            Edit
                        </a>

                        <form action="{{ route('mata-kuliah.destroy', $mk->id) }}" method="POST"
                              style="flex:1;"
                              onsubmit="return confirm('Yakin ingin menghapus mata kuliah {{ $mk->name }}?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    style="width:100%; padding:0.4rem; border-radius:4px; border:1px solid #f5c2c0; background:#fdecea; color:#b3261e; cursor:pointer; font-family:Arial, sans-serif; font-size:0.85rem;">
                                Hapus
                            </button>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>

        {{-- Pagination — otomatis membawa query string (?q=...&status=...)
             karena controller sudah pakai withQueryString() --}}
        <div style="margin-top:2rem;">
            {{ $mataKuliah->links() }}
        </div>
    @endif

</x-layout>