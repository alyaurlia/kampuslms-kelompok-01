{{--
    View edit: form ubah mata kuliah (hanya admin; lihat CoursePolicy::update).

    Variabel dari CourseController@edit:
      $mataKuliah  -> objek model Course
      $routePrefix -> mis. 'admin.mata-kuliah'
      $lecturers   -> daftar user ber-role dosen (id, name, nim_nip)
--}}
<x-layout title="Edit Mata Kuliah">

    <h1 class="form-title">Edit Mata Kuliah</h1>

    @if ($errors->any())
        <div class="alert alert-error" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="form-card">

        <form action="{{ route($routePrefix . '.update', $mataKuliah) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Status --}}
            <div class="form-group">
                <label for="status" class="form-label form-label--sans">
                    Status
                </label>

                <select name="status" id="status" class="form-control">
                    <option value="">-- Pilih Status --</option>
                    @foreach (['draft' => 'Draft', 'active' => 'Active', 'archived' => 'Archived'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('status', $mataKuliah->status) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                @error('status')
                    <p class="form-error">{{ $message }}</p>
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
                    maxlength="20"
                    class="form-control"
                >

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
                    maxlength="150"
                    class="form-control"
                >

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

                @error('sks')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            {{-- Dosen pengampu --}}
            <div class="form-group">
                <label for="lecturer_id" class="form-label form-label--sans">
                    Dosen Pengampu
                </label>

                <select name="lecturer_id" id="lecturer_id" class="form-control">
                    <option value="">-- Pilih Dosen --</option>
                    @foreach ($lecturers as $lecturer)
                        <option value="{{ $lecturer->id }}"
                            @selected((int) old('lecturer_id', $mataKuliah->lecturer_id) === $lecturer->id)>
                            {{ $lecturer->name }} ({{ $lecturer->nim_nip }})
                        </option>
                    @endforeach
                </select>

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
                    Batal
                </a>
            </div>

        </form>
    </section>

</x-layout>