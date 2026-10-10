{{-- Variabel dari MaterialController@create: $course, $role --}}
<x-layout title="Tambah Materi">

    <a href="{{ route($role . '.mata-kuliah.materi.index', $course) }}" class="mk-back-link">
        &larr; Kembali ke Daftar Materi
    </a>

    <h1 class="mk-page-title mk-page-title--spaced">Tambah Materi &mdash; {{ $course->name }}</h1>

    @include('materials._form', [
        'action'    => route($role . '.mata-kuliah.materi.store', $course),
        'cancelUrl' => route($role . '.mata-kuliah.materi.index', $course),
    ])

</x-layout>