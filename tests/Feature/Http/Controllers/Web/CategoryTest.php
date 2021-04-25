<?php

namespace Tests\Feature\Http\Controllers\Web;

use App\Http\Requests\Category\CategoryStoreRequest;
use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Tests\Feature\Http\Controllers\ControllersTestTools;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @var User
     */
    private $user;

    /**
     * @var Category
     */
    private $categoryMake;

    private $homeUrl;
    private $loginUrl;
    private $indexUrl;
    private $createUrl;
    private $storeUrl;
    private $showUrl;
    private $editUrl;
    private $updateUrl;
    private $destroyUrl;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->categoryMake = Category::factory()
            ->fullHouse()
            ->make();

        // Куда будем заходить.
        $this->homeUrl = route('home');
        $this->loginUrl = route('login');
        $this->indexUrl = route('categories.index');
        $this->createUrl = route('categories.create');
        $this->storeUrl = route('categories.store');
        $this->showUrl = route('categories.show', [1]);
        $this->editUrl = route('categories.edit', [1]);
        $this->updateUrl = route('categories.update', [1]);
        $this->destroyUrl = route('categories.destroy', [1]);
    }

    /**
     * Страница "галерея" должна быть доступна и для гостя, и для пользователя.
     */
    public function test_gallery_screen_can_be_rendered()
    {
        // Для гостя.
        $this->assertGuest()
            ->get($this->indexUrl)
            ->assertOk();

        // Для пользователя.
        $this->actingAs($this->user)
            ->get($this->indexUrl)
            ->assertOk();
    }

    /**
     * Страница "добавить категорию" должна быть доступна только для пользователя,
     * гость должен быть отправлен на авторизацию.
     */
    public function test_add_category_screen_can_be_rendered()
    {
        // Для гостя.
        $this->assertGuest()
            ->get($this->createUrl)
            ->assertRedirect($this->loginUrl);

        // Для пользователя.
        $this->actingAs($this->user)
            ->get($this->createUrl)
            ->assertOk();
    }

    /**
     * Категория должна быть создана, если заполнены все поля в форме.
     */
    public function test_category_can_be_added()
    {
        $name = $this->categoryMake->name;
        $desc = $this->categoryMake->description;

        Category::truncate();

        $this->actingAs($this->user)
            ->post($this->storeUrl, [
                'name'        => $name,
                'description' => $desc,
            ])
            ->assertRedirect($this->createUrl);

        $this->assertDatabaseCount('categories', 1)
            ->assertDatabaseHas('categories', [
                'name'        => $name,
                'description' => $desc,
            ]);
    }

    /**
     * Категория должна быть создана, если заполнено только поле "название".
     */
    public function test_category_with_empty_description_can_be_created()
    {
        $name = $this->categoryMake->name;

        Category::truncate();

        $this->actingAs($this->user)
            ->post($this->storeUrl, [
                'name'        => $name,
                'description' => '',
            ])
            ->assertRedirect($this->createUrl);

        $this->assertDatabaseCount('categories', 1)
            ->assertDatabaseHas('categories', [
                'name'        => $name,
                'description' => null,
            ]);
    }

    /**
     * Категория не должна быть создана, если поле "название" не заполнено.
     */
    public function test_category_with_empty_name_cannot_be_created()
    {
        $message = __('validation.required', [
            'attribute' => __('validation.attributes.name'),
        ]);

        $error = ControllersTestTools::getValidationError('name', $message);

        Category::truncate();

        $this->actingAs($this->user)
            ->post($this->storeUrl, [
                'name'        => '',
                'description' => '',
            ])
            ->assertSessionHas('errors', $error)
            ->assertRedirect($this->homeUrl);

        $this->assertDatabaseCount('categories', 0);
    }

    /**
     * Категория не должна быть создана, если длина значения в поле "название" превысит лимит.
     */
    public function test_category_with_overlimit_of_name_cannot_be_created()
    {
        $maxLength = config('interface.category.name_max_length');

        $message = __('validation.max.string', [
            'attribute' => __('validation.attributes.name'),
            'max'       => $maxLength,
        ]);

        $error = ControllersTestTools::getValidationError('name', $message);

        Category::truncate();

        $this->actingAs($this->user)
            ->post($this->storeUrl, [
                'name'        => Str::random($maxLength + 1),
                'description' => '',
            ])
            ->assertSessionHas('errors', $error)
            ->assertRedirect($this->homeUrl);

        $this->assertDatabaseCount('categories', 0);
    }

    /**
     * Категория не должна быть создана, если длина значения в поле "описание" превысит лимит.
     */
    public function test_category_with_overlimit_of_description_cannot_be_created()
    {
        $maxLength = config('interface.category.desc_max_length');

        $message = __('validation.max.string', [
            'attribute' => __('validation.attributes.description'),
            'max'       => $maxLength,
        ]);

        $error = ControllersTestTools::getValidationError('description', $message);

        Category::truncate();

        $this->actingAs($this->user)
            ->post($this->storeUrl, [
                'name'        => $this->categoryMake->name,
                'description' => Str::random($maxLength + 1),
            ])
            ->assertSessionHas('errors', $error)
            ->assertRedirect($this->homeUrl);

        $this->assertDatabaseCount('categories', 0);
    }

    /**
     * Категория не должна быть создана, если значение в поле "название" соответствует существующей категории.
     */
    public function test_category_with_duplicate_name_cannot_be_created()
    {
        Category::truncate();

        /**
         * @var $category Category
         */
        $category = Category::factory()
            ->fullHouse()
            ->create();

        $customErrorMessages = (new CategoryStoreRequest())->messages();
        $message = $customErrorMessages['name.unique'];

        $error = ControllersTestTools::getValidationError('name', $message);

        $this->assertDatabaseCount('categories', 1);

        $this->actingAs($this->user)
            ->post($this->storeUrl, [
                'name'        => $category->name,
                'description' => '',
            ])
            ->assertSessionHas('errors', $error)
            ->assertRedirect($this->homeUrl);

        $this->assertDatabaseCount('categories', 1);
    }

    /**
     * Созданная категория должна быть доступна и пользователю, и гостю.
     */
    public function test_category_screen_can_be_rendered()
    {
        Category::truncate();

        Category::factory()
            ->fullHouse()
            ->create();

        Image::factory()
            ->processed()
            ->create();

        $this->assertDatabaseCount('categories', 1);

        // Для гостя.
        $this->assertGuest()
            ->get($this->showUrl)
            ->assertOk();

        // Для пользователя.
        $this->actingAs($this->user)
            ->get($this->showUrl)
            ->assertOk();

        // И снова для гостя, чтобы проверить кешированный вариант.
        Auth::logout();

        $this->assertGuest()
            ->get($this->showUrl)
            ->assertOk();
    }

    /**
     * Созданная категория должна быть доступна и пользователю, и гостю.
     */
    public function test_not_found_screen_must_be_rendered()
    {
        Category::truncate();

        $this->assertDatabaseCount('categories', 0);

        // Для гостя.
        $this->assertGuest()
            ->get($this->showUrl)
            ->assertNotFound();

        // Для пользователя.
        $this->actingAs($this->user)
            ->get($this->showUrl)
            ->assertNotFound();
    }

    /**
     * Страница "редактировать категорию" должна быть доступна только для пользователя,
     * гость должен быть отправлен на авторизацию.
     */
    public function test_update_category_screen_can_be_rendered()
    {
        Category::truncate();

        Category::factory()
            ->fullHouse()
            ->create();

        $this->assertDatabaseCount('categories', 1);

        // Для гостя.
        $this->assertGuest()
            ->get($this->editUrl)
            ->assertRedirect($this->loginUrl);

        // Для пользователя.
        $this->actingAs($this->user)
            ->get($this->editUrl)
            ->assertOk();
    }

    /**
     * Название и описание в категории должны быть обновлены.
     */
    public function test_category_can_be_updated()
    {
        Category::truncate();

        /**
         * @var $category Category
         */
        $category = Category::factory()
            ->fullHouse()
            ->create();

        $this->assertDatabaseCount('categories', 1)
            ->assertDatabaseHas('categories', [
                'name'        => $category->name,
                'description' => $category->description,
            ]);

        $newName = $this->faker->text(
            config('interface.category.name_max_length')
        );

        $newDesc = $this->faker->text(
            config('interface.category.desc_max_length')
        );

        $this->actingAs($this->user)
            ->put($this->updateUrl, [
                'name'        => $newName,
                'description' => $newDesc,
            ])
            ->assertRedirect($this->showUrl);

        $this->assertDatabaseCount('categories', 1)
            ->assertDatabaseHas('categories', [
                'name'        => $newName,
                'description' => $newDesc,
            ]);
    }

    /**
     * Название и описание в категории должны быть обновлены.
     */
    public function test_category_can_be_updated_with_empty_description()
    {
        Category::truncate();

        /**
         * @var $category Category
         */
        $category = Category::factory()
            ->fullHouse()
            ->create();

        $this->assertDatabaseCount('categories', 1)
            ->assertDatabaseHas('categories', [
                'name'        => $category->name,
                'description' => $category->description,
            ]);

        $newName = $this->faker->text(
            config('interface.category.name_max_length')
        );

        $this->actingAs($this->user)
            ->put($this->updateUrl, [
                'name'        => $newName,
                'description' => '',
            ])
            ->assertRedirect($this->showUrl);

        $this->assertDatabaseCount('categories', 1)
            ->assertDatabaseHas('categories', [
                'name'        => $newName,
                'description' => null,
            ]);
    }

    /**
     * Категория не должна быть обновлена, если поле "название" не заполнено.
     */
    public function test_category_with_empty_name_cannot_be_updated()
    {
        $message = __('validation.required', [
            'attribute' => __('validation.attributes.name'),
        ]);

        $error = ControllersTestTools::getValidationError('name', $message);

        Category::truncate();

        $this->actingAs($this->user)
            ->put($this->updateUrl, [
                'name'        => '',
                'description' => '',
            ])
            ->assertSessionHas('errors', $error)
            ->assertRedirect($this->homeUrl);

        $this->assertDatabaseCount('categories', 0);
    }

    /**
     * Категория не должна быть обновлена, если длина значения в поле "название" превысит лимит.
     */
    public function test_category_with_overlimit_of_name_cannot_be_updated()
    {
        $maxLength = config('interface.category.name_max_length');

        $message = __('validation.max.string', [
            'attribute' => __('validation.attributes.name'),
            'max'       => $maxLength,
        ]);

        $error = ControllersTestTools::getValidationError('name', $message);

        Category::truncate();

        $this->actingAs($this->user)
            ->put($this->updateUrl, [
                'name'        => Str::random($maxLength + 1),
                'description' => '',
            ])
            ->assertSessionHas('errors', $error)
            ->assertRedirect($this->homeUrl);

        $this->assertDatabaseCount('categories', 0);
    }

    /**
     * Категория не должна быть обновлена, если длина значения в поле "описание" превысит лимит.
     */
    public function test_category_with_overlimit_of_description_cannot_be_updated()
    {
        $maxLength = config('interface.category.desc_max_length');

        $message = __('validation.max.string', [
            'attribute' => __('validation.attributes.description'),
            'max'       => $maxLength,
        ]);

        $error = ControllersTestTools::getValidationError('description', $message);

        Category::truncate();

        $this->actingAs($this->user)
            ->put($this->updateUrl, [
                'name'        => $this->categoryMake->name,
                'description' => Str::random($maxLength + 1),
            ])
            ->assertSessionHas('errors', $error)
            ->assertRedirect($this->homeUrl);

        $this->assertDatabaseCount('categories', 0);
    }

    /**
     * Категория не должна быть обновлена, если значение в поле "название" соответствует существующей категории.
     */
    public function test_category_with_duplicate_name_cannot_be_updated()
    {
        Category::truncate();

        /**
         * @var $category Category
         */
        $category = Category::factory()
            ->fullHouse()
            ->create();

        $customErrorMessages = (new CategoryStoreRequest())->messages();
        $message = $customErrorMessages['name.unique'];

        $error = ControllersTestTools::getValidationError('name', $message);

        $this->assertDatabaseCount('categories', 1);

        $this->actingAs($this->user)
            ->put($this->updateUrl, [
                'name'        => $category->name,
                'description' => '',
            ])
            ->assertSessionHas('errors', $error)
            ->assertRedirect($this->homeUrl);

        $this->assertDatabaseCount('categories', 1);
    }

    /**
     * Пустая категория должна быть удалена.
     */
    public function test_empty_category_must_be_deleted()
    {
        Category::truncate();
        Image::truncate();

        Category::factory()
            ->fullHouse()
            ->create();

        $this->assertDatabaseCount('categories', 1);

        $this->actingAs($this->user)
            ->delete($this->destroyUrl)
            ->assertRedirect($this->indexUrl);

        $this->assertDatabaseCount('categories', 0);
    }

    /**
     * Не пустая категория должна быть перемещена в архив.
     */
    public function test_not_empty_category_must_be_archived()
    {
        Category::truncate();
        Image::truncate();

        Category::factory()
            ->fullHouse()
            ->create();

        Image::factory()
            ->create();

        $this->assertDatabaseCount('categories', 1)
            ->assertDatabaseHas('categories', [
                'deleted_at' => null,
            ]);

        $this->assertDatabaseCount('images', 1);

        $this->actingAs($this->user)
            ->delete($this->destroyUrl)
            ->assertRedirect($this->indexUrl);

        $this->assertDatabaseCount('categories', 1);

        /**
         * @var $category Category
         */
        $category = Category::withTrashed()->first();

        $this->assertNotNull($category);
        $this->assertNotNull($category->deleted_at);
    }

    /**
     * Архивная категория может быть восстановлена.
     */
    public function test_archived_category_can_be_restored()
    {
        Category::truncate();
        Image::truncate();

        /**
         * @var $category Category
         */
        $category = Category::factory()
            ->fullHouse(true)
            ->create();

        Image::factory()
            ->create();

        $this->assertDatabaseCount('categories', 1)
            ->assertDatabaseHas('categories', [
                'deleted_at' => $category->deleted_at,
            ]);

        $this->assertDatabaseCount('images', 1);

        $this->actingAs($this->user)
            ->delete($this->destroyUrl)
            ->assertRedirect($this->indexUrl);

        $this->assertDatabaseCount('categories', 1)
            ->assertDatabaseHas('categories', [
                'deleted_at' => null,
            ]);
    }

}

