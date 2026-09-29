<?php

use App\Http\Controllers\CourseController;
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

// Sementara TANPA middleware, cuma buat lihat tampilan 3 role.
// Nama route di sini harus cocok dengan array $menus di layout.blade.php.

// ADMIN  ->  /admin/dashboard, /admin/users, /admin/mata-kuliah
Route::prefix('admin')->name('admin.')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
    Route::resource('users', UserController::class);
    Route::resource('mata-kuliah', CourseController::class);
});

// DOSEN  ->  /dosen/dashboard, /dosen/mata-kuliah
Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::view('/dashboard', 'dosen.dashboard')->name('dashboard');
    Route::resource('mata-kuliah', CourseController::class)->only(['index', 'show', 'edit', 'update']);
});

// MAHASISWA  ->  /mahasiswa/dashboard, /mahasiswa/mata-kuliah
Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::view('/dashboard', 'mahasiswa.dashboard')->name('dashboard');
    Route::resource('mata-kuliah', CourseController::class)->only(['index', 'show']);
});