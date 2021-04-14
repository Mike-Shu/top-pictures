<?php

namespace App\Services\Category;

use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Models\Category;
use App\Services\CommonTools;
use Auth;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CategoryService
 *
 * @package App\Services\Category
 */
class CategoryService
{

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

        return view(
            'pages.category.category-show',
            [
                'category' => $category
            ]
        );
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

        if ($category->amount) {

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

            $caller = CommonTools::getCaller(2);
            Log::warning("The category #{$id} was not found: {$caller}");

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
            ? Category::withTrashed()->find($id)
            : Category::find($id);
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
            // Для авторизованных пользователей.
            ? Category::withTrashed()
                ->orderBy('name')
                ->paginate($perPage)
            // Для гостей.
            : Category::where('amount', '>', 0)
                ->orderBy('name')
                ->paginate($perPage);
    }

}