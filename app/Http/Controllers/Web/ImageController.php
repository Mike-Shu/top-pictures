<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Image\ChangeCategoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class ImageController extends Controller
{
    /**
     * @var ChangeCategoryService
     */
    private $changeCategoryService;

    /**
     * @param  ChangeCategoryService  $changeCategoryService
     */
    public function __construct(
        ChangeCategoryService $changeCategoryService
    ) {
        $this->changeCategoryService = $changeCategoryService;
    }

    /**
     * Получить актуальный список категорий для перемещения изображения.
     *
     * @param  int  $excludedId
     *
     * @return View
     */
    public function getCategoriesList(int $excludedId = 0): View
    {
        return $this->changeCategoryService->getCategoriesList($excludedId);
    }

    /**
     * Переместить изображение в другую категорию.
     *
     * @param  ChangeCategoryService  $service
     *
     * @return JsonResponse
     */
    public function changeCategory(ChangeCategoryService $service): JsonResponse
    {
        return $service->changeCategory();
    }

}


