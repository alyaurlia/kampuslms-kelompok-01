@php
    $rp = \Illuminate\Support\Str::before(request()->route()->getName(), 'mata-kuliah');
@endphp

<x-layout title="Edit Mata Kuliah">

    <h1 class="form-title">Edit Mata Kuliah</h1>

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

        <form action="{{ route($routePrefix . '.update', $mataKuliah->id) }}" method="POST">
    <section style="background:var(--color-surface); border:1px solid var(--color-border); border-radius:6px; padding:1.5rem; max-width:600px;">

        <form action="{{ route($rp . 'mata-kuliah.update', $mataKuliah->id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Status --}}
            <div class="form-group">
                <label for="status" class="form-label form-label--sans">
                    Status
                </label>

                <select name="status" id="status" class="form-control">
                    <option value="">-- Pilih Status --</option>
                    <option value="draft" {{ old('status', $mataKuliah->status) === 'draft' ? 'selected' : '' }}>
                        Draft
                    </option>
                    <option value="active" {{ old('status', $mataKuliah->status) === 'active' ? 'selected' : '' }}>
                        Active
                    </option>
                    <option value="archived" {{ old('status', $mataKuliah->status) === 'archived' ? 'selected' : '' }}>
                        Archived
                    </option>
                </select>

                @error('status')
                    <p class="form-error">{{ $message }}</p>
            <div style="margin-bottom:1rem;">
                <label for="status" style="display:block; margin-bottom:0.4rem; font-family:Arial, sans-serif;">Status</label>
                <select name="status" id="status"
                        style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
                    <option value="">-- Pilih Status --</option>
                    <option value="draft" {{ old('status', $mataKuliah->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="active" {{ old('status', $mataKuliah->status) === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="archived" {{ old('status', $mataKuliah->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
                @error('status')
                    <p style="color:#b3261e; font-size:0.85rem; margin-top:0.3rem;">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kode --}}
            <div class="form-group">
                <label for="code" class="form-label form-label--sans">
                    Kode Mata Kuliah
                </label>

                <input
                    type="text"
                    name="code"
                    id="code"
                    value="{{ old('code', $mataKuliah->code) }}"
                    class="form-control"
                >

            <div style="margin-bottom:1rem;">
                <label for="code" style="display:block; margin-bottom:0.4rem; font-family:Arial, sans-serif;">Kode Mata Kuliah</label>
                <input type="text" name="code" id="code" value="{{ old('code', $mataKuliah->code) }}"
                       style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
                @error('code')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama --}}
            <div class="form-group">
                <label for="name" class="form-label form-label--sans">
                    Nama Mata Kuliah
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name', $mataKuliah->name) }}"
                    class="form-control"
                >

            <div style="margin-bottom:1rem;">
                <label for="name" style="display:block; margin-bottom:0.4rem; font-family:Arial, sans-serif;">Nama Mata Kuliah</label>
                <input type="text" name="name" id="name" value="{{ old('name', $mataKuliah->name) }}"
                       style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- SKS --}}
            <div class="form-group">
                <label for="sks" class="form-label form-label--sans">
                    SKS
                </label>

                <input
                    type="number"
                    name="sks"
                    id="sks"
                    min="1"
                    max="6"
                    value="{{ old('sks', $mataKuliah->sks) }}"
                    class="form-control"
                >

            <div style="margin-bottom:1rem;">
                <label for="sks" style="display:block; margin-bottom:0.4rem; font-family:Arial, sans-serif;">SKS</label>
                <input type="number" name="sks" id="sks" min="1" max="6" value="{{ old('sks', $mataKuliah->sks) }}"
                       style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
                @error('sks')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dosen --}}
            <div class="form-group">
                <label for="lecturer_id" class="form-label form-label--sans">
                    Dosen
                </label>

                <input
                    type="number"
                    name="lecturer_id"
                    id="lecturer_id"
                    value="{{ old('lecturer_id', $mataKuliah->lecturer_id) }}"
                    class="form-control"
                >

            <div style="margin-bottom:1rem;">
                <label for="lecturer_id" style="display:block; margin-bottom:0.4rem; font-family:Arial, sans-serif;">ID Dosen</label>
                <input type="number" name="lecturer_id" id="lecturer_id" value="{{ old('lecturer_id', $mataKuliah->lecturer_id) }}"
                       style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">
                @error('lecturer_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="form-group form-group--lg">
                <label for="description" class="form-label form-label--sans">
                    Deskripsi
                </label>

                <textarea
                    name="description"
                    id="description"
                    rows="4"
                    class="form-control"
                >{{ old('description', $mataKuliah->description) }}</textarea>

            <div style="margin-bottom:1.5rem;">
                <label for="description" style="display:block; margin-bottom:0.4rem; font-family:Arial, sans-serif;">Deskripsi</label>
                <textarea name="description" id="description" rows="4"
                          style="width:100%; padding:0.5rem; border:1px solid var(--color-border); border-radius:4px;">{{ old('description', $mataKuliah->description) }}</textarea>
                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    Update
                </button>

                <a href="{{ route($routePrefix . '.index') }}" class="btn btn-secondary">
            <div style="display:flex; gap:0.75rem;">
                <button type="submit"
                        style="padding:0.6rem 1.2rem; border:none; border-radius:4px; background:var(--color-card-1-from); color:#fff; cursor:pointer;">
                    Update
                </button>
                <a href="{{ route($rp . 'mata-kuliah.index') }}"
                   style="padding:0.6rem 1.2rem; border-radius:4px; border:1px solid var(--color-border); text-decoration:none; color:inherit;">
                    Batal
                </a>
            </div>

        </form>
    </section>

</x-layout>