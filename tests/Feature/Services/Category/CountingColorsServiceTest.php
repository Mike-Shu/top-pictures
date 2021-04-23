<?php

namespace Tests\Feature\Services\Category;

use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use App\Services\Category\CountingColorsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountingColorsServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Проверяем установку отметки о том, что требуется пересчёт основных цветов.
     */
    public function test_set_update_required(): void
    {
        Category::truncate();
        $category = Category::factory()->create();

        $this->assertDatabaseCount('categories', 1);

        $this->assertDatabaseHas('categories', [
            'id'              => 1,
            'update_required' => 0,
        ]);

        CountingColorsService::setUpdateRequired($category);

        $this->assertDatabaseHas('categories', [
            'id'              => 1,
            'update_required' => 1,
        ]);
    }

    /**
     * Проверяем "пересчитыватель" основных цветов для категорий.
     */
    public function test_recalculate(): void
    {
        Category::truncate();
        User::factory()->create();

        $category = Category::factory()
            ->fullHouse(false, false)
            ->create();

        Image::factory()
            ->count(100)
            ->create();

        CountingColorsService::setUpdateRequired($category);

        $this->assertDatabaseHas('categories', [
            'id'     => 1,
            'colors' => null,
        ]);

        CountingColorsService::recalculate();

        $this->assertDatabaseMissing('categories', [
            'id'     => 1,
            'colors' => null,
        ]);
    }
}
