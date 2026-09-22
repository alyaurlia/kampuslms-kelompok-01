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

Route::resource('mata-kuliah', CourseController::class);
Route::resource('users', UserController::class);