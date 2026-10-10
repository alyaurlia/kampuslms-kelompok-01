{{-- Variabel dari MaterialController@edit: $material, $course, $role --}}
<x-layout title="Edit Materi">

    <a href="{{ route($role . '.materi.show', $material) }}" class="mk-back-link">
        &larr; Kembali ke Detail Materi
    </a>

    <h1 class="mk-page-title mk-page-title--spaced">Edit Materi</h1>

    @include('materials._form', [
        'action'    => route($role . '.materi.update', $material),
        'cancelUrl' => route($role . '.materi.show', $material),
        'material'  => $material,
    ])

</x-layout>