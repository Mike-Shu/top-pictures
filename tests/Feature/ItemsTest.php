<?php

namespace Tests\Feature;

use App\Items\ImageItem;
use App\Items\RgbColorItem;
use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_image_item()
    {
        User::factory()->create();

        Category::factory()
            ->fullHouse()
            ->create();

        /**
         * @var $image Image
         */
        $image = Image::factory()->create();

        $this->assertEquals(1, $image->id);

        $imageItem = new ImageItem($image);

        $this->assertNull($imageItem->category);
        $imageItem->setCategory();
        $this->assertEquals(1, $imageItem->category->id);

        $this->assertNull($imageItem->user);
        $imageItem->setUser();
        $this->assertEquals(1, $imageItem->user->id);

        // Тестируем кеширование.
        $imageItem->setCategory();
        $this->assertEquals(1, $imageItem->category->id);

        $imageItem->setUser();
        $this->assertEquals(1, $imageItem->user->id);
    }

    public function test_rgb_color_item()
    {
        $item = new RgbColorItem([128, 64, 0]);
        $this->assertEquals(128, $item->red);
        $this->assertEquals(64, $item->green);
        $this->assertEquals(0, $item->blue);

        $this->expectException(\InvalidArgumentException::class);
        new RgbColorItem([128, 64]);
    }
}
