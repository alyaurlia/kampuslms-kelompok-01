{{--
    Form tugas (dipakai create & edit).
    Variabel: $action, $cancelUrl, $assignment (opsional, hanya saat edit)
--}}
@php
    $editing = isset($assignment);
    $dueAt   = old('due_at', isset($assignment) ? $assignment->due_at?->format('Y-m-d\TH:i') : '');
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
        <label for="title" class="mk-label">Judul Tugas</label>
        <input type="text" id="title" name="title" class="mk-input"
               value="{{ old('title', $assignment->title ?? '') }}" required>
    </div>

    <div class="mk-field">
        <label for="instructions" class="mk-label">Instruksi</label>
        <textarea id="instructions" name="instructions" rows="6" class="mk-input" required>{{ old('instructions', $assignment->instructions ?? '') }}</textarea>
    </div>

    <div class="mk-field">
        <label for="attachment" class="mk-label">Lampiran Soal (opsional)</label>
        @if ($editing && $assignment->file_path)
            <p class="mk-hint">
                Lampiran saat ini:
                <a href="{{ $assignment->attachment_url }}" target="_blank" rel="noopener">{{ $assignment->original_name }}</a>.
                Pilih berkas baru hanya jika ingin menggantinya.
            </p>
            <label class="mk-check">
                <input type="checkbox" name="remove_attachment" value="1" @checked(old('remove_attachment'))>
                Hapus lampiran ini
            </label>
        @endif
        <input type="file" id="attachment" name="attachment" class="mk-input">
        <p class="mk-hint">PDF, Word, PowerPoint, Excel, ZIP, TXT, JPG, PNG. Maksimal 10 MB.</p>
    </div>

    <div class="mk-field-row">
        <div class="mk-field">
            <label for="due_at" class="mk-label">Batas Waktu</label>
            <input type="datetime-local" id="due_at" name="due_at" class="mk-input"
                   value="{{ $dueAt }}" required>
        </div>

        <div class="mk-field">
            <label for="max_score" class="mk-label">Nilai Maksimal</label>
            <input type="number" id="max_score" name="max_score" class="mk-input" min="1" max="100"
                   value="{{ old('max_score', $assignment->max_score ?? 100) }}" required>
        </div>

        <div class="mk-field">
            <label for="status" class="mk-label">Status</label>
            <select id="status" name="status" class="mk-input">
                @foreach (['draft' => 'Draft (belum terlihat mahasiswa)', 'published' => 'Published (terlihat mahasiswa)'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('status', $assignment->status ?? 'draft') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mk-field">
        <label class="mk-check">
            {{-- hidden memastikan nilai 0 terkirim saat checkbox tidak dicentang --}}
            <input type="hidden" name="allow_late" value="0">
            <input type="checkbox" name="allow_late" value="1"
                   @checked(old('allow_late', $assignment->allow_late ?? true))>
            Izinkan pengumpulan terlambat
        </label>
    </div>

    <div class="mk-form-actions">
        <button type="submit" class="mk-btn-primary">{{ $editing ? 'Simpan Perubahan' : 'Simpan Tugas' }}</button>
        <a href="{{ $cancelUrl }}" class="mk-btn-ghost">Batal</a>
    </div>
</form>