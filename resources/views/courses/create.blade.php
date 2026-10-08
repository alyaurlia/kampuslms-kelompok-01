@php
    $rp = \Illuminate\Support\Str::before(request()->route()->getName(), 'mata-kuliah');
@endphp

<x-layout title="Tambah Mata Kuliah">

    <h1 class="form-title">Tambah Mata Kuliah</h1>

    @if ($errors->any())
        <div class="alert-error">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="form-card">

        <form action="{{ route($rp . 'mata-kuliah.store') }}" method="POST">
            @csrf

            {{-- Status --}}
            <div style="margin-bottom:1.5rem;">
                <label for="status" style="display:block; margin-bottom:0.4rem;">Status</label>
                <select name="status" id="status"
                        style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
                    <option value="">-- Pilih Status --</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="archived" {{ old('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                @error('status')
                    <p style="color:#b3261e; font-size:0.85rem; margin-top:0.3rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kode --}}
            <div style="margin-bottom:1rem;">
                <label for="code" style="display:block; margin-bottom:0.4rem;">Kode Mata Kuliah</label>
                <input type="text" name="code" id="code" value="{{ old('code') }}"
                       style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
                @error('code')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama --}}
            <div style="margin-bottom:1rem;">
                <label for="name" style="display:block; margin-bottom:0.4rem;">Nama Mata Kuliah</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}"
                       style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- SKS --}}
            <div style="margin-bottom:1rem;">
                <label for="sks" style="display:block; margin-bottom:0.4rem;">SKS</label>
                <input type="number" name="sks" id="sks" min="1" max="6" value="{{ old('sks') }}"
                       style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
                @error('sks')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dosen --}}
            <div style="margin-bottom:1rem;">
                <label for="lecturer_id" style="display:block; margin-bottom:0.4rem;">ID Dosen</label>
                <input type="number" name="lecturer_id" id="lecturer_id" value="{{ old('lecturer_id') }}"
                       style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
                @error('lecturer_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div style="margin-bottom:1.5rem;">
                <label for="description" style="display:block; margin-bottom:0.4rem;">Deskripsi</label>
                <textarea name="description" id="description" rows="4"
                          style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">{{ old('description') }}</textarea>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex; gap:0.75rem;">
                <button type="submit"
                        style="padding:0.6rem 1.2rem; border:none; border-radius:4px; background:var(--color-card-1-from); color:#fff; cursor:pointer;">
                    Simpan
                </button>
                <a href="{{ route($rp . 'mata-kuliah.index') }}"
                   style="padding:0.6rem 1.2rem; border-radius:4px; border:1px solid var(--color-border); text-decoration:none; color:inherit;">
                    Batal
                </a>
            </div>

        </form>
    </section>

</x-layout>