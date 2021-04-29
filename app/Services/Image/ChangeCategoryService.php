<?php

namespace App\Services\Image;

use App\Exceptions\NotFoundException;
use App\Models\Category;
use App\Services\Category\CountingColorsService;
use App\Services\RequestService;
use App\Traits\GetCategory;
use App\Traits\GetImage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

/**
 * Управление изображением: перемещение изображения в другую категорию.
 *
 * @package App\Services\Image
 */
class ChangeCategoryService extends RequestService
{
    use GetImage, GetCategory;

    /**
     * Возвращает список категорий для перемещения изображения.
     *
     * @param  int  $excludedId  Категория, которой не должно быть в списке.
     *
     * @return View
     */
    public function getCategoriesList(int $excludedId = 0): View
    {
        $categories = $this->getCategories($excludedId);

        return view('components.category.image-control.categories-list', [
            'categories' => $categories,
        ]);
    }

    /**
     * Выполняет перемещение изображения в другую категорию.
     *
     * @return JsonResponse
     */
    public function changeCategory(): JsonResponse
    {
        try {

            $imageId = $this->request->get('imageId', 0);
            $toCategoryId = $this->request->get('categoryId', 0);

            $image = $this->getImage($imageId);
            $fromCategory = $this->getCategory($image->category_id);
            $toCategory = $this->getCategory($toCategoryId);

            $image->category_id = $toCategory->id;
            $image->save();

            CountingColorsService::setUpdateRequired($fromCategory);
            CountingColorsService::setUpdateRequired($toCategory);

            return Response::json([
                'status' => static::STATUS_OK,
            ]);

        } catch (NotFoundException $e) {

            // @codeCoverageIgnoreStart
            if (App::environment('testing') === false) {
                Log::warning(
                    $e->getMessage()
                );
            }
            // @codeCoverageIgnoreEnd

            return Response::json([
                'status' => static::STATUS_FAILED,
            ], $e->getCode());

        }
    }

    /**
     * @param  int  $excludedId
     *
     * @return array
     */
    private function getCategories(int $excludedId): array
    {
        $cacheKey = md5(__METHOD__.$excludedId);
        $categoriesList = Cache::get($cacheKey);

        if (empty($categoriesList)) {

            $categoriesList = Category::withTrashed()
                ->where('id', '!=', $excludedId)
                ->orderBy('name')
                ->get(['id', 'name', 'deleted_at'])
                ->all();

            Cache::put($cacheKey, $categoriesList, 60);

        }

        return $categoriesList;
    }
}