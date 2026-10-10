<x-layout title="Detail Pengguna">

    <h1 class="form-title">Detail Pengguna</h1>

    <section class="form-card">

        <dl class="user-detail">
            <div>
                <dt>Nama Lengkap</dt>
                <dd>
                    {{ $user->name }}
                    @if ($user->is(auth()->user()))
                        <span class="users-self">Anda</span>
                    @endif
                </dd>
            </div>

            <div>
                <dt>Email</dt>
                <dd>{{ $user->email }}</dd>
            </div>

            <div>
                <dt>NIM / NIP</dt>
                <dd>{{ $user->nim_nip ?? '—' }}</dd>
            </div>

            <div>
                <dt>Peran</dt>
                <dd><span class="badge badge--{{ $user->role }}">{{ ucfirst($user->role) }}</span></dd>
            </div>

            @if ($user->role === 'dosen')
                <div>
                    <dt>Mata Kuliah Diampu</dt>
                    <dd>{{ $user->taught_courses_count }}</dd>
                </div>
            @elseif ($user->role === 'mahasiswa')
                <div>
                    <dt>Mata Kuliah Diikuti</dt>
                    <dd>{{ $user->courses_count }}</dd>
                </div>
            @endif

            <div>
                <dt>Terdaftar Sejak</dt>
                <dd>{{ $user->created_at?->format('d M Y, H:i') ?? '—' }}</dd>
            </div>
        </dl>

        <div class="form-actions">
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                Edit
            </a>

            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                Kembali
            </a>
        </div>

    </section>

</x-layout>