<?php

namespace App\Services\Image;

use App\Exceptions\ChangeCategoryException;
use App\Models\Category;
use App\Models\Image;
use App\Services\Category\CountingColorsService;
use App\Services\CommonTools;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

/**
 * Управление изображением: перемещение изображения в другую категорию.
 *
 * @package App\Services\Image
 */
class ChangeCategoryService
{
    const STATUS_OK = "Ok"; // Перемещение выполнено успешно.
    const STATUS_FAILED = "Failed"; // При перемещении что-то пошло не так.

    const ERR_NOT_FOUND = 404; // Сущность не найдена.

    /**
     * @var Request
     */
    private $request;

    /**
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

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

        } catch (ChangeCategoryException $e) {

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
     * @param  int  $id
     *
     * @return Image
     * @throws ChangeCategoryException
     */
    private function getImage(int $id): Image
    {
        $image = Image::withTrashed()->find($id);

        if (empty($image)) {

            $caller = CommonTools::getCaller(1);

            throw new ChangeCategoryException(
                "The image #{$id} was not found: {$caller}",
                static::ERR_NOT_FOUND,
            );

        }

        return $image;
    }

    /**
     * @param  int  $id
     *
     * @return Category
     * @throws ChangeCategoryException
     */
    protected function getCategory(int $id): Category
    {
        $category = Category::withTrashed()->find($id);

        if (empty($category)) {

            $caller = CommonTools::getCaller(1);

            throw new ChangeCategoryException(
                "The category #{$id} was not found: {$caller}",
                static::ERR_NOT_FOUND,
            );

        }

        return $category;
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