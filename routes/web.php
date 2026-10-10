<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseEnrollmentController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// ---------- Publik / login ----------

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route(Auth::user()->role . '.dashboard');
    }

    return view('welcome');
})->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('login.store');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/tentang', function () {
    return view('tentang');
})->name('tentang');

// ---------- Reset kata sandi (hanya untuk yang belum login) ----------
// Nama route password.request / password.reset dipakai oleh Laravel
// saat membuat tautan di email, jadi jangan diganti.

Route::middleware('guest')->group(function () {

    Route::get('/lupa-password', [PasswordResetController::class, 'requestForm'])
        ->name('password.request');

    Route::post('/lupa-password', [PasswordResetController::class, 'sendLink'])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [PasswordResetController::class, 'resetForm'])
        ->name('password.reset');

    Route::post('/reset-password', [PasswordResetController::class, 'reset'])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

// ---------- Perlu login ----------

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return redirect()->route(Auth::user()->role . '.dashboard');
    })->name('dashboard');

    Route::get('/pengumpulan/{submission}/unduh', [SubmissionController::class, 'download'])
        ->name('pengumpulan.unduh');


    // =========================================================
    // ADMIN
    // =========================================================

    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [AdminDashboardController::class, 'index'])
                ->name('dashboard');

            Route::resource('users', UserController::class);

            Route::resource('mata-kuliah', CourseController::class);

            // Pendaftaran mahasiswa ke mata kuliah (admin).
            // Nama lengkap: admin.mata-kuliah.mahasiswa.store / .destroy
            Route::post('mata-kuliah/{mata_kuliah}/mahasiswa', [CourseEnrollmentController::class, 'store'])
                ->name('mata-kuliah.mahasiswa.store');

            Route::delete('mata-kuliah/{mata_kuliah}/mahasiswa/{student}', [CourseEnrollmentController::class, 'destroy'])
                ->whereNumber('student')
                ->name('mata-kuliah.mahasiswa.destroy');
        });


    // =========================================================
    // DOSEN
    // =========================================================

    Route::middleware('role:dosen')
        ->prefix('dosen')
        ->name('dosen.')
        ->group(function () {

            Route::view('/dashboard', 'dosen.dashboard')
                ->name('dashboard');

            // Dosen hanya melihat MK yang diampu. Mengubah MK = admin saja
            // (CoursePolicy::update), jadi edit/update tidak dibuka di sini.
            Route::resource('mata-kuliah', CourseController::class)
                ->only([
                    'index',
                    'show',
                ]);

            Route::scopeBindings()->group(function () {

                Route::resource('mata-kuliah.materi', MaterialController::class)
                    ->parameters([
                        'mata-kuliah' => 'course',
                        'materi' => 'material',
                    ])
                    ->shallow();

                Route::resource('mata-kuliah.tugas', AssignmentController::class)
                    ->parameters([
                        'mata-kuliah' => 'course',
                        'tugas' => 'assignment',
                    ])
                    ->shallow();
            });
        });


    // =========================================================
    // MAHASISWA
    // =========================================================

    Route::middleware('role:mahasiswa')
        ->prefix('mahasiswa')
        ->name('mahasiswa.')
        ->group(function () {

            Route::view('/dashboard', 'mahasiswa.dashboard')
                ->name('dashboard');

            Route::resource('mata-kuliah', CourseController::class)
                ->only([
                    'index',
                    'show',
                ]);

            Route::scopeBindings()->group(function () {

                Route::resource('mata-kuliah.materi', MaterialController::class)
                    ->only([
                        'index',
                        'show',
                    ])
                    ->parameters([
                        'mata-kuliah' => 'course',
                        'materi' => 'material',
                    ])
                    ->shallow();

                Route::resource('mata-kuliah.tugas', AssignmentController::class)
                    ->only([
                        'index',
                        'show',
                    ])
                    ->parameters([
                        'mata-kuliah' => 'course',
                        'tugas' => 'assignment',
                    ])
                    ->shallow();
            });

             Route::post('/tugas/{assignment}/kumpul', [SubmissionController::class, 'store'])
                ->name('tugas.kumpul');
        });
});

// =========================================================
// HANYA UNTUK PENGEMBANGAN
// Hapus sebelum dikumpulkan.
// =========================================================

if (app()->environment('local')) {

    Route::get('/dev-login/{id}', function ($id) {

        Auth::loginUsingId($id);

        return redirect('/dashboard');
    });
}