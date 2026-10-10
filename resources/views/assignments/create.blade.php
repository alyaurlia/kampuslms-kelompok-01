{{-- Variabel dari AssignmentController@create: $course, $role --}}
<x-layout title="Tambah Tugas">

    <a href="{{ route($role . '.mata-kuliah.tugas.index', $course) }}" class="mk-back-link">
        &larr; Kembali ke Daftar Tugas
    </a>

    <h1 class="mk-page-title mk-page-title--spaced">Tambah Tugas &mdash; {{ $course->name }}</h1>

    @include('assignments._form', [
        'action'    => route($role . '.mata-kuliah.tugas.store', $course),
        'cancelUrl' => route($role . '.mata-kuliah.tugas.index', $course),
    ])

</x-layout>