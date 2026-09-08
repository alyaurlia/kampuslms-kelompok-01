<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentang', [CourseController::class, 'tentang']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');
