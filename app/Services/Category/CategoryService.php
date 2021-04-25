<?php

namespace App\Services\Category;

use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Items\ImageItem;
use App\Models\Category;
use App\Services\CommonTools;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Основная работа с категориями (а-ля CRUD).
 *
 * @package App\Services\Category
 */
class CategoryService
{
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
     * Возвращает страницу "Галерея".
     *
     * @param  Request  $request
     *
     * @return Application|Factory|View
     */
    public function getGalleryIndex(Request $request)
    {
        $list = $this->getCategoriesListByAuth(
            Auth::check()
        );

        return view('pages.category.categories-list', [
            'items'     => $list->items(),
            'paginator' => $list,
        ]);
    }

    /**
     * Возвращает страницу с указанной категорией.
     *
     * @param  int  $id
     *
     * @return Application|Factory|View
     */
    public function showCategory(int $id)
    {
        $category = $this->getCategory($id);

        list($list, $images) = $this->getCategoryImages($category);

        return view(
            'pages.category.category-show',
            [
                'category'  => $category,
                'images'    => $images,
                'paginator' => $list,
            ]
        );
    }

    /**
     * Возвращает коллекцию изображений для указанной категории.
     *
     * @param  Category  $category
     *
     * @return array
     */
    private function getCategoryImages(Category $category): array
    {
        $page = $this->request->get('page') ?? 1;
        $cacheKey = md5("category{$category->id}{$page}");

        $cached = Cache::get($cacheKey);

        if (Auth::check() === false && !empty($cached)) {
            return $cached;
        }

        $list = $category->images()
            ->where('processed', true)
            ->paginate(
                config('interface.image.per_page')
            );

        $images = array_map(function ($_item) {

            return new ImageItem($_item);

        }, $list->items());

        $result = [
            $list,
            $images,
        ];

        Cache::put($cacheKey, $result, 60);

        return $result;
    }

    /**
     * Добавляет новую категорию в хранилище.
     *
     * @param  CategoryStoreRequest  $request
     *
     * @return RedirectResponse
     */
    public function storeCategory(CategoryStoreRequest $request): RedirectResponse
    {
        $inputData = $request->all();

        Category::create($inputData);

        return redirect()
            ->route('categories.create')
            ->with('message', __('Success'));
    }

    /**
     * Возвращает страницу для редактирования указанной категории.
     *
     * @param  int  $id
     *
     * @return Application|Factory|View
     */
    public function editCategory(int $id)
    {
        $category = $this->getCategory($id);

        return view(
            'pages.category.category-edit',
            [
                'category' => $category
            ]
        );
    }

    /**
     * Редактирует указанную категорию.
     *
     * @param  CategoryUpdateRequest  $request
     * @param  int                    $id
     *
     * @return RedirectResponse
     */
    public function updateCategory(CategoryUpdateRequest $request, int $id): RedirectResponse
    {
        $inputData = $request->all();

        $category = $this->getCategory($id);
        $category->update($inputData);

        return redirect()
            ->route('categories.show', [$id])
            ->with('message', __('Success'));
    }

    /**
     * Удаляет категорию:
     *  - если в категории нет изображений, то категория удаляется из хранилища;
     *  - если в категории есть изображения, то категория отправляется в архив.
     *
     * @param  int  $id
     *
     * @return RedirectResponse
     */
    public function destroyCategory(int $id): RedirectResponse
    {
        $category = $this->getCategory($id);

        if ($category->images()->count()) {

            // Если категория не пустая, то…
            try {

                if ($category->trashed()) {
                    // … вернем ее из архива.
                    $category->restore();
                } else {
                    // … отправим ее в архив.
                    $category->delete();
                }

            } catch (Exception $e) { // @codeCoverageIgnore

                // @codeCoverageIgnoreStart
                $caller = CommonTools::getCaller(2);
                $message = $e->getMessage();
                Log::critical("{$message} | {$caller}");
                // @codeCoverageIgnoreEnd

            }

        } else {

            // Если в категории нет изображений, то снесем ее окончательно.
            $category->forceDelete();

        }

        return redirect()
            ->route('categories.index')
            ->with('message', __('Success'));
    }

    /**
     * Возвращает указанную категорию.
     *
     * @param  int  $id
     *
     * @return Category
     */
    private function getCategory(int $id): Category
    {
        $category = $this->getCategoryByAuth(
            $id,
            Auth::check()
        );

        if (empty($category)) {

            // @codeCoverageIgnoreStart
            if (App::environment('testing') === false) {
                $caller = CommonTools::getCaller(2);
                Log::warning("The category #{$id} was not found: {$caller}");
            }
            // @codeCoverageIgnoreEnd

            throw new NotFoundHttpException();

        }

        return $category;
    }

    /**
     * Возвращает указанную категорию с поправкой на авторизацию.
     *
     * @param  int   $id
     * @param  bool  $auth
     *
     * @return Category|null
     */
    private function getCategoryByAuth(int $id, bool $auth): ?Category
    {
        return $auth
            ? Category::withTrashed()
                ->withCount($this->withCountStatement())
                ->find($id)
            : Category::withCount($this->withCountStatement())
                ->find($id);
    }

    /**
     * Возвращает коллекцию категорий с поправкой на авторизацию пользователя и количество категорий на одной странице.
     *
     * @param  bool  $auth
     *
     * @return LengthAwarePaginator
     */
    private function getCategoriesListByAuth(bool $auth): LengthAwarePaginator
    {
        $perPage = config('interface.category.per_page');

        return $auth
            // Для авторизованных пользователей (все категории, включая архивные и пустые):
            ? Category::withTrashed()
                ->withCount($this->withCountStatement()) // Посчитать кол-во изображений.
                ->orderBy('name')
                ->paginate($perPage)
            // Для гостей (только не архивные и не пустые категории):
            : Category::withCount($this->withCountStatement())
                ->whereHas('images', function (Builder $query) {
                    $query->where('processed', true);
                })
                ->orderBy('name')
                ->paginate($perPage);
    }

    /**
     * @return array []
     */
    private function withCountStatement(): array
    {
        return [
            'images' => function (Builder $query) {
                $query->where('processed', true);
            }
        ];
    }
}