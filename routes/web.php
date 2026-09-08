<?php

use App\Http\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/tentang', function () {
    return view('tentang');
});

Route::middleware('auth')->group(function () {
    Route::get('/mata-kuliah', [CourseController::class, 'index'])
        ->name('mata-kuliah.index');
    Route::get('/mata-kuliah/{mata_kuliah}', [CourseController::class, 'show'])
        ->name('mata-kuliah.show');
});