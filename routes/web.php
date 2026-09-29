<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentang', function () {
    return view('tentang');
})->name('tentang');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Route model binding sudah otomatis lewat resource() ini.
Route::resource('mata-kuliah', CourseController::class);
Route::resource('users', UserController::class);

// ADMIN -> /admin/dashboard, /admin/users, /admin/mata-kuliah
Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('mata-kuliah', CourseController::class);
});

// DOSEN -> kelola mata kuliah, materi, dan tugas
Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
    Route::view('/dashboard', 'dosen.dashboard')->name('dashboard');
    Route::resource('mata-kuliah', CourseController::class)
        ->only(['index', 'show', 'edit', 'update']);

    // BUILD 4: route bersarang
    Route::scopeBindings()->group(function () {
        Route::resource('mata-kuliah.materi', MaterialController::class)
            ->parameters(['mata-kuliah' => 'course', 'materi' => 'material'])
            ->shallow();

        Route::resource('mata-kuliah.tugas', AssignmentController::class)
            ->parameters(['mata-kuliah' => 'course', 'tugas' => 'assignment'])
            ->shallow();
    });
});

// MAHASISWA -> hanya melihat
Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::view('/dashboard', 'mahasiswa.dashboard')->name('dashboard');
    Route::resource('mata-kuliah', CourseController::class)->only(['index', 'show']);

    Route::scopeBindings()->group(function () {
        Route::resource('mata-kuliah.materi', MaterialController::class)
            ->only(['index', 'show'])
            ->parameters(['mata-kuliah' => 'course', 'materi' => 'material'])
            ->shallow();

        Route::resource('mata-kuliah.tugas', AssignmentController::class)
            ->only(['index', 'show'])
            ->parameters(['mata-kuliah' => 'course', 'tugas' => 'assignment'])
            ->shallow();
    });
});