{{--
    Form materi (dipakai create & edit).
    Variabel: $action, $course, $material (opsional, hanya saat edit)
--}}
@php
    $editing = isset($material);
    $type    = old('type', $material->type ?? 'file');
@endphp

<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="mk-form">
    @csrf
    @if ($editing)
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="mk-form-errors">
            <strong>Periksa kembali isian Anda:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mk-field">
        <label for="title" class="mk-label">Judul Materi</label>
        <input type="text" id="title" name="title" class="mk-input"
               value="{{ old('title', $material->title ?? '') }}" required>
    </div>

    <div class="mk-field">
        <label for="description" class="mk-label">Deskripsi (opsional)</label>
        <textarea id="description" name="description" rows="4" class="mk-input">{{ old('description', $material->description ?? '') }}</textarea>
    </div>

    <div class="mk-field">
        <label for="type" class="mk-label">Tipe Materi</label>
        <select id="type" name="type" class="mk-input">
            <option value="file" @selected($type === 'file')>Berkas (unggah file)</option>
            <option value="link" @selected($type === 'link')>Tautan luar</option>
        </select>
    </div>

    <div class="mk-field" id="field-file">
        <label for="file" class="mk-label">Berkas</label>
        @if ($editing && $material->file_path)
            <p class="mk-hint">
                Berkas saat ini:
                <a href="{{ $material->url }}" target="_blank" rel="noopener">{{ $material->original_name }}</a>.
                Pilih berkas baru hanya jika ingin menggantinya.
            </p>
        @endif
        <input type="file" id="file" name="file" class="mk-input">
        <p class="mk-hint">PDF, Word, PowerPoint, Excel, ZIP, TXT, JPG, PNG. Maksimal 10 MB.</p>
    </div>

    <div class="mk-field" id="field-link">
        <label for="external_url" class="mk-label">Tautan</label>
        <input type="url" id="external_url" name="external_url" class="mk-input"
               placeholder="https://..."
               value="{{ old('external_url', $material->external_url ?? '') }}">
    </div>

    <div class="mk-form-actions">
        <button type="submit" class="mk-btn-primary">{{ $editing ? 'Simpan Perubahan' : 'Simpan Materi' }}</button>
        <a href="{{ $cancelUrl }}" class="mk-btn-ghost">Batal</a>
    </div>
</form>

<script>
    (function () {
        var type = document.getElementById('type');
        var fileField = document.getElementById('field-file');
        var linkField = document.getElementById('field-link');

        function toggle() {
            var isFile = type.value === 'file';
            fileField.style.display = isFile ? '' : 'none';
            linkField.style.display = isFile ? 'none' : '';
        }

        type.addEventListener('change', toggle);
        toggle();
    })();
</script>