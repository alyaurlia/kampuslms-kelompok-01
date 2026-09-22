<x-layout title="Tambah Mata Kuliah">

    <h1 style="font-size:1.6rem; margin-bottom:1.5rem;">Tambah Mata Kuliah</h1>

    @if ($errors->any())
        <div style="background:#fdecea; border:1px solid #f5c2c0; border-radius:6px; padding:1rem; margin-bottom:1.5rem; font-family:Arial, sans-serif;">
            <ul style="margin:0; padding-left:1.2rem; color:#b3261e;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem; max-width:600px;">

        <form action="{{ route('mata-kuliah.store') }}" method="POST">
            @csrf

{{-- Status --}}
<div style="margin-bottom:1.5rem;">
    <label for="status" style="display:block; margin-bottom:0.4rem;">
        Status
    </label>

    <select
        name="status"
        id="status"
        style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
    >
        <option value="">-- Pilih Status --</option>
        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>
            Draft
        </option>
        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>
            Active
        </option>
        <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>
            Archived
        </option>
    </select>
</div>

            {{-- Kode --}}
            <div style="margin-bottom:1rem;">
                <label for="code" style="display:block; margin-bottom:0.4rem;">
                    Kode Mata Kuliah
                </label>

                <input
                    type="text"
                    name="code"
                    id="code"
                    value="{{ old('code') }}"
                    style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
                >
            </div>

            {{-- Nama --}}
            <div style="margin-bottom:1rem;">
                <label for="name" style="display:block; margin-bottom:0.4rem;">
                    Nama Mata Kuliah
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
                >
            </div>

            {{-- SKS --}}
            <div style="margin-bottom:1rem;">
                <label for="sks" style="display:block; margin-bottom:0.4rem;">
                    SKS
                </label>

                <input
                    type="number"
                    name="sks"
                    id="sks"
                    min="1"
                    max="6"
                    value="{{ old('sks') }}"
                    style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
                >
            </div>

            {{-- Dosen --}}
            <div style="margin-bottom:1rem;">
                <label for="lecturer_id" style="display:block; margin-bottom:0.4rem;">
                    ID Dosen
                </label>

                <input
                    type="number"
                    name="lecturer_id"
                    id="lecturer_id"
                    value="{{ old('lecturer_id') }}"
                    style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
                >
            </div>

            {{-- Deskripsi --}}
            <div style="margin-bottom:1.5rem;">
                <label for="description" style="display:block; margin-bottom:0.4rem;">
                    Deskripsi
                </label>

                <textarea
                    name="description"
                    id="description"
                    rows="4"
                    style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;"
                >{{ old('description') }}</textarea>
            </div>

            <div style="display:flex; gap:0.75rem;">

                <button
                    type="submit"
                    style="padding:0.6rem 1.2rem; border:none; border-radius:4px; background:var(--color-card-1-from); color:#fff; cursor:pointer;"
                >
                    Simpan
                </button>

                <a
                    href="{{ route('mata-kuliah.index') }}"
                    style="padding:0.6rem 1.2rem; border-radius:4px; border:1px solid var(--color-border); text-decoration:none; color:inherit;"
                >
                    Batal
                </a>

            </div>

        </form>

    </section>

</x-layout>