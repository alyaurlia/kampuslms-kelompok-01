{{-- Variabel dari AssignmentController@edit: $assignment, $course, $role --}}
<x-layout title="Edit Tugas">

    <a href="{{ route($role . '.tugas.show', $assignment) }}" class="mk-back-link">
        &larr; Kembali ke Detail Tugas
    </a>

    <h1 class="mk-page-title mk-page-title--spaced">Edit Tugas</h1>

    @include('assignments._form', [
        'action'     => route($role . '.tugas.update', $assignment),
        'cancelUrl'  => route($role . '.tugas.show', $assignment),
        'assignment' => $assignment,
    ])

</x-layout>