<?php

namespace Tests\Feature\Http\Controllers\Web;

use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;

    private $loginUrl;
    private $categoriesListUrl;
    private $changeCategoryUrl;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->loginUrl = route('login');
        $this->categoriesListUrl = route('image-categories-list', [2]);
        $this->changeCategoryUrl = route('image-change-category');

    }

    /**
     * Должен быть получен список актуальных категорий.
     */
    public function test_get_change_categories_list(): void
    {
        Category::truncate();
        Image::truncate();

        // Для гостя.
        $this->assertGuest()
            ->get($this->categoriesListUrl)
            ->assertRedirect($this->loginUrl);

        // Для пользователя.
        Category::factory()
            ->fullHouse()
            ->count(3)
            ->create();

        $this->assertDatabaseCount('categories', 3);

        $this->actingAs($this->user)
            ->get($this->categoriesListUrl)
            ->assertOk()
            ->assertSee('value="1"', false)
            ->assertDontSee('value="2"', false)
            ->assertSee('value="3"', false);
    }

    /**
     * Должно быть выполнено перемещение изображения из одной категории в другую.
     */
    public function test_change_category(): void
    {
        Category::truncate();
        Image::truncate();

        Category::factory()
            ->fullHouse()
            ->create();

        Image::factory()
            ->processed()
            ->create();

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseCount('images', 1);

        $this->assertDatabaseHas('images', [
            'category_id' => 1,
        ]);

        Category::factory()
            ->fullHouse()
            ->create();

        $this->assertDatabaseCount('categories', 2);

        $this->actingAs($this->user)
            ->post($this->changeCategoryUrl, [
                'imageId'    => 1,
                'categoryId' => 2,
            ])
            ->assertOk();

        $this->assertDatabaseHas('images', [
            'category_id' => 2,
        ]);
    }

    /**
     * При ошибочных данных перемещение изображения должно приводить к ошибкам.
     */
    public function test_change_category_failed(): void
    {
        Category::truncate();
        Image::truncate();

        Category::factory()
            ->fullHouse()
            ->create();

        Image::factory()
            ->processed()
            ->create();

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseCount('images', 1);

        $this->assertDatabaseHas('images', [
            'category_id' => 1,
        ]);

        $this->actingAs($this->user)
            ->post($this->changeCategoryUrl, [
                'imageId' => 1,
            ])
            ->assertNotFound();

        $this->actingAs($this->user)
            ->post($this->changeCategoryUrl, [
                'categoryId' => 2,
            ])
            ->assertNotFound();

        $this->actingAs($this->user)
            ->post($this->changeCategoryUrl, [
                'imageId'    => 100,
                'categoryId' => 500,
            ])
            ->assertNotFound();

        $this->assertDatabaseHas('images', [
            'category_id' => 1,
        ]);
    }
}
