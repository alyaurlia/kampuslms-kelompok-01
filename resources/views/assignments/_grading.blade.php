{{--
    Daftar pengumpulan + form nilai (dosen & admin).
    Variabel dari AssignmentController@show: $assignment, $submissions
    Dosen pengampu : form nilai tampil lewat @can('grade').
    Admin          : hanya melihat nilai (tanpa form).
    Mahasiswa      : TIDAK memakai partial ini (blok "Pengumpulan Saya" di show.blade.php).

    Tampilan: tiap mahasiswa berupa dropdown (<details>) yang bisa dibuka/tutup.
    Dropdown otomatis terbuka bila pengiriman nilai mahasiswa itu gagal validasi,
    supaya pesan errornya langsung terlihat. Gaya ada di css/components/courses.css (blok .gr-*).
--}}
@php
    $max = $assignment->max_score;
    $bag = $errors->getBag('grade');
@endphp

<section class="mk-card gr-panel">
    <div class="mk-body">
        <div class="gr-head">
            <h2 class="gr-title">Pengumpulan Mahasiswa</h2>
            <span class="gr-count">{{ $submissions->count() }}</span>
        </div>

        @if (session('grade_success'))
            <p class="gr-flash"><strong>{{ session('grade_success') }}</strong></p>
        @endif

        @if ($submissions->isEmpty())
            <p class="gr-empty">Belum ada pengumpulan untuk tugas ini.</p>
        @else
            <div class="gr-list">
                @foreach ($submissions as $submission)
                    @php
                        $grade  = $submission->grade;
                        $isThis = (string) old('_submission') === (string) $submission->id;
                        $stale  = $grade && $grade->graded_at && $submission->submitted_at->gt($grade->graded_at);
                        $bukaDropdown = $isThis && $bag->any();
                        $inisial = collect(preg_split('/\s+/', trim($submission->student->name)))
                            ->filter()->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->take(2)->join('');
                    @endphp

                    <details class="gr-item" @if ($bukaDropdown) open @endif>
                        <summary class="gr-summary">
                            <span class="gr-avatar" aria-hidden="true">{{ $inisial }}</span>

                            <span class="gr-who">
                                <span class="gr-name">{{ $submission->student->name }}</span>
                                @if ($submission->student->nim_nip)
                                    <span class="gr-nim">{{ $submission->student->nim_nip }}</span>
                                @endif
                            </span>

                            {{-- penanda status nilai (terlihat saat dropdown tertutup) --}}
                            @if ($stale)
                                <span class="gr-status gr-status--stale">Perlu dinilai ulang</span>
                            @elseif ($grade)
                                <span class="gr-status {{ $grade->is_published ? 'gr-status--done' : 'gr-status--draft' }}">
                                    Nilai {{ $grade->score }}
                                </span>
                            @else
                                <span class="gr-status gr-status--none">Belum dinilai</span>
                            @endif

                            <svg class="gr-chevron" viewBox="0 0 24 24" width="20" height="20" fill="none"
                                 stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
                                 stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"/></svg>
                        </summary>

                        <div class="gr-body">
                            <p class="mk-list-meta gr-meta">
                                Dikumpulkan {{ $submission->submitted_at->translatedFormat('d F Y, H:i') }}
                                @if ($submission->is_late)
                                    <strong>(Terlambat)</strong>
                                @endif
                                &middot;
                                <a href="{{ route('pengumpulan.unduh', $submission) }}">{{ $submission->original_name }}</a>
                                @if ($stale)
                                    &middot; Dikumpulkan ulang setelah dinilai
                                @endif
                            </p>

                            @can('grade', $submission)
                                <form method="POST" action="{{ route('dosen.pengumpulan.nilai', $submission) }}" class="mk-form">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="_submission" value="{{ $submission->id }}">

                                    @if ($isThis && $bag->any())
                                        <div class="mk-form-errors">
                                            <ul>
                                                @foreach ($bag->all() as $message)
                                                    <li>{{ $message }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <div class="mk-field">
                                        <label for="score-{{ $submission->id }}" class="mk-label">Nilai (maks. {{ $max }})</label>
                                        <input type="number" id="score-{{ $submission->id }}" name="score" class="mk-input"
                                               min="0" max="{{ $max }}" step="0.01"
                                               value="{{ $isThis ? old('score') : $grade?->score }}" required>
                                    </div>

                                    <div class="mk-field">
                                        <label for="feedback-{{ $submission->id }}" class="mk-label">Feedback (opsional)</label>
                                        <textarea id="feedback-{{ $submission->id }}" name="feedback" rows="3" maxlength="2000"
                                                  class="mk-input">{{ $isThis ? old('feedback') : $grade?->feedback }}</textarea>
                                    </div>

                                    <div class="mk-field">
                                        <label class="mk-check">
                                            {{-- hidden memastikan nilai 0 terkirim saat checkbox tidak dicentang --}}
                                            <input type="hidden" name="is_published" value="0">
                                            <input type="checkbox" name="is_published" value="1"
                                                   @checked($isThis ? old('is_published') : ($grade?->is_published ?? false))>
                                            Tampilkan nilai ke mahasiswa
                                        </label>
                                    </div>

                                    <div class="mk-form-actions">
                                        <button type="submit" class="mk-btn-primary">
                                            {{ $grade ? 'Perbarui Nilai' : 'Simpan Nilai' }}
                                        </button>
                                    </div>
                                </form>
                            @else
                                {{-- Admin: hanya melihat --}}
                                @if ($grade)
                                    <p>
                                        Nilai: {{ $grade->score }} / {{ $max }}
                                        <span class="mk-chip mk-chip--{{ $grade->is_published ? 'published' : 'draft' }}">
                                            {{ $grade->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </p>
                                    @if ($grade->feedback)
                                        <p>{!! nl2br(e($grade->feedback)) !!}</p>
                                    @endif
                                @else
                                    <p><em>Belum dinilai</em></p>
                                @endif
                            @endcan
                        </div>
                    </details>
                @endforeach
            </div>
        @endif
    </div>
</section>