<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Uploader\UploaderService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class UploadController extends Controller
{

    /**
     * Возвращает страницу с формой для загрузки изображений.
     *
     * @return Application|Factory|View
     */
    public function index(int $category_id = 0)
    {

        return view('pages.upload', [
            'config' => json_encode(
                config('interface.uploading.resumable')
            ),
        ]);

    }

    /**
     * Загружает один файл или часть файла.
     *
     * @param  UploaderService  $uploader
     *
     * @return JsonResponse
     */
    public function upload(UploaderService $uploader): JsonResponse
    {

        return $uploader->uploadFile();

    }

}
