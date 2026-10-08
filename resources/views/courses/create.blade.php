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

        <form action="{{ route($routePrefix . '.store') }}" method="POST">
            @csrf

            {{-- Status --}}
            <div class="form-group form-group--lg">
                <label for="status" class="form-label">
                    Status
                </label>

                <select name="status" id="status" class="form-control">
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

                @error('status')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kode --}}
            <div class="form-group">
                <label for="code" class="form-label">
                    Kode Mata Kuliah
                </label>

                <input
                    type="text"
                    name="code"
                    id="code"
                    value="{{ old('code') }}"
                    class="form-control"
                >

                @error('code')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nama --}}
            <div class="form-group">
                <label for="name" class="form-label">
                    Nama Mata Kuliah
                </label>

                <input
                    type="text"
                    name="name"
                    id="name"
                    value="{{ old('name') }}"
                    class="form-control"
                >

                @error('name')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- SKS --}}
            <div class="form-group">
                <label for="sks" class="form-label">
                    SKS
                </label>

                <input
                    type="number"
                    name="sks"
                    id="sks"
                    min="1"
                    max="6"
                    value="{{ old('sks') }}"
                    class="form-control"
                >

                @error('sks')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dosen --}}
            <div class="form-group">
                <label for="lecturer_id" class="form-label">
                    ID Dosen
                </label>

                <input
                    type="number"
                    name="lecturer_id"
                    id="lecturer_id"
                    value="{{ old('lecturer_id') }}"
                    class="form-control"
                >

                @error('lecturer_id')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Deskripsi --}}
            <div class="form-group form-group--lg">
                <label for="description" class="form-label">
                    Deskripsi
                </label>

                <textarea
                    name="description"
                    id="description"
                    rows="4"
                    class="form-control"
                >{{ old('description') }}</textarea>

                @error('description')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-actions">

                <button type="submit" class="btn btn-primary">
                    Simpan
                </button>

                <a href="{{ route($routePrefix . '.index') }}" class="btn btn-secondary">
                    Batal
                </a>

            </div>

        </form>

    </section>

</x-layout>