<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\CategoryStoreRequest;
use App\Http\Requests\Category\CategoryUpdateRequest;
use App\Services\Category\CategoryService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    /**
     * Возвращает страницу со списком категорий.
     *
     * @param  Request          $request
     * @param  CategoryService  $service
     *
     * @return Application|Factory|View
     */
    public function index(Request $request, CategoryService $service)
    {
        return $service->getGalleryIndex($request);
    }

    /**
     * Возвращает страницу с формой для создания новой категории.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('pages.category.category-create');
    }

    /**
     * Сохраняет вновь созданную категорию.
     *
     * @param  CategoryStoreRequest  $request
     * @param  CategoryService       $service
     *
     * @return RedirectResponse
     */
    public function store(CategoryStoreRequest $request, CategoryService $service): RedirectResponse
    {
        return $service->storeCategory($request);
    }

    /**
     * Возвращает страницу с указанной категорией.
     *
     * @param  int              $id
     * @param  CategoryService  $service
     *
     * @return Application|Factory|View
     */
    public function show(int $id, CategoryService $service)
    {
        return $service->showCategory($id);
    }

    /**
     * Возвращает страницу с формой для редактирования категории.
     *
     * @param  int              $id
     * @param  CategoryService  $service
     *
     * @return Application|Factory|View
     */
    public function edit(int $id, CategoryService $service)
    {
        return $service->editCategory($id);
    }

    /**
     * Редактирует указанную категорию.
     *
     * @param  CategoryUpdateRequest  $request
     * @param  int                    $id
     * @param  CategoryService        $service
     *
     * @return RedirectResponse
     */
    public function update(CategoryUpdateRequest $request, int $id, CategoryService $service): RedirectResponse
    {
        return $service->updateCategory($request, $id);
    }

    /**
     * Удаляет указанную категорию.
     *
     * @param  int              $id
     * @param  CategoryService  $service
     *
     * @return RedirectResponse
     */
    public function destroy(int $id, CategoryService $service): RedirectResponse
    {
        return $service->destroyCategory($id);
    }
}
