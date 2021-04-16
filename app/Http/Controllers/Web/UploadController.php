<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
     * @param  int  $categoryId
     *
     * @return Application|Factory|View
     */
    public function index(int $categoryId = 0)
    {
        $categoriesList = Category::withTrashed()
            ->get()
            ->all();

        return view('pages.upload', [
            'categoriesList' => $categoriesList,
            'categoryId'     => $categoryId,
            'config'         => json_encode(
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
