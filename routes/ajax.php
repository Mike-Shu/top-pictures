<?php

use App\Http\Controllers\Web\ImageController;
use App\Http\Controllers\Web\UploadController;

/**
 * Маршруты для AJAX-запросов.
 */
Route::middleware(['auth'])->group(function () {

    // Возвращает HTML-шаблон с информацией о том, что браузер не поддерживается.
    Route::get('/uploader-not-supported', function () {
        return view('file-uploader.browser-not-supported');
    })->name('ajax_uploader_not_supported');

    // Возвращает HTML-шаблон для добавления файла в список загрузки.
    Route::get('/uploader-list-item', function () {
        return view('file-uploader.uploader-list-item');
    })->name('ajax_uploader_list_item');

    // Маршрут для загрузки файлов.
    Route::post('/upload', [UploadController::class, 'upload'])
        ->name('ajax_upload_file');

    Route::prefix('image')->group(function () {

        Route::get('/categories-list/{excluded_id?}', [ImageController::class, 'getCategoriesList'])
            ->name('image-categories-list');

        Route::post('change-category', [ImageController::class, 'changeCategory'])
            ->name('image-change-category');

        Route::post('change-main-color', [ImageController::class, 'changeMainColor'])
            ->name('image-change-main-color');

    });

});
