{{--
    Dashboard dosen: ringkasan mata kuliah yang diampu user yang sedang login.
    Memakai komponen .stat-grid / .stat-card yang sama dengan admin/dashboard
    (didefinisikan di resources/css/dashboard.css).
--}}
@php
    $mkDiampu = \App\Models\Course::where('lecturer_id', auth()->id());

    $totalMk     = (clone $mkDiampu)->count();
    $totalAktif  = (clone $mkDiampu)->where('status', 'active')->count();
    $totalPeserta = (clone $mkDiampu)->withCount('students')->get()->sum('students_count');
@endphp

<x-layout title="Dashboard Dosen" role="dosen">
    <h1 class="page-title">Dashboard Dosen</h1>

    <div class="stat-grid">
        <div class="stat-card">
            <p class="stat-label">Mata Kuliah Diampu</p>
            <p class="stat-value">{{ $totalMk }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Mata Kuliah Aktif</p>
            <p class="stat-value">{{ $totalAktif }}</p>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Peserta</p>
            <p class="stat-value">{{ $totalPeserta }}</p>
        </div>
    </div>
</x-layout>