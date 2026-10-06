<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ---------- Publik / login ----------
Route::get('/', function () {
    // Kalau sudah login, langsung ke dashboard sesuai peran
    if (Auth::check()) {
        return redirect()->route(Auth::user()->role . '.dashboard');
    }
    return view('welcome');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/tentang', function () {
    return view('tentang');
})->name('tentang');

// ---------- Perlu login ----------
Route::middleware('auth')->group(function () {

    // /dashboard -> arahkan ke dashboard sesuai peran
    Route::get('/dashboard', function () {
        return redirect()->route(Auth::user()->role . '.dashboard');
    })->name('dashboard');

    // ADMIN
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::view('/dashboard', 'admin.dashboard')->name('dashboard');
        Route::resource('users', UserController::class);
        Route::resource('mata-kuliah', CourseController::class);
    });

    // DOSEN
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        Route::view('/dashboard', 'dosen.dashboard')->name('dashboard');
        Route::resource('mata-kuliah', CourseController::class)
            ->only(['index', 'show', 'edit', 'update']);

        Route::scopeBindings()->group(function () {
            Route::resource('mata-kuliah.materi', MaterialController::class)
                ->parameters(['mata-kuliah' => 'course', 'materi' => 'material'])
                ->shallow();

            Route::resource('mata-kuliah.tugas', AssignmentController::class)
                ->parameters(['mata-kuliah' => 'course', 'tugas' => 'assignment'])
                ->shallow();
        });
    });

    // MAHASISWA
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
});