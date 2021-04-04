<?php

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

});
