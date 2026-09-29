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

// Sementara TANPA middleware, cuma buat lihat tampilan 3 role
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('mata-kuliah', CourseController::class);
});

Route::prefix('dosen')->name('dosen.')->group(function () {
    Route::resource('mata-kuliah', CourseController::class)->only(['index', 'show', 'edit', 'update']);
});


Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::resource('mata-kuliah', CourseController::class)->only(['index', 'show']);
});