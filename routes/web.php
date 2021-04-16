<?php

use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\UploadController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/test.php';

Route::get('/', [HomeController::class, 'index'])
    ->name('home');

Route::middleware(['auth'])->group(function () {

    Route::resource('categories', CategoryController::class)
        ->only([
            'create',
            'store',
            'edit',
            'update',
            'destroy'
        ]);

    Route::get('/upload/{categoryId?}', [UploadController::class, 'index'])
        ->name('upload_form');

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

});

Route::resource('categories', CategoryController::class)
    ->only([
        'index',
        'show'
    ]);
