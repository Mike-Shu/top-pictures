<?php

namespace App\Services\Category;

use App\Items\CategoryColorItem;
use App\Models\Category;
use App\Models\Image;

/**
 * Пересчёт основных цветов в категориях.
 *
 * @package App\Services\Category
 */
class CountingColorsService
{
    /**
     * Ставит в категории отметку о том, что в ней требуется пересчёт основных цветов.
     *
     * @param  Category|null  $category
     */
    public static function setUpdateRequired(?Category $category = null): void
    {
        if (!empty($category)) {
            $category->update_required = true;
            $category->save();
        }
    }

    /**
     * Пересчитать основные цвета в категориях.
     */
    public static function recalculate()
    {
        // Список категорий, для которых нужно пересчитать основные цвета.
        $categories = Category::whereUpdateRequired(true)
            ->get()
            ->all();

        foreach ($categories as $_category) {

            /**
             * @var $_category Category
             */
            $images = $_category->images
                ->where('processed', true)
                ->all();

            $actualColors = static::getActualColors($images);

            static::saveColors($_category, $actualColors);

        }
    }

    /**
     * Возвращает массив с актуальными основными цветами.
     *
     * @param  array  $images  Массив с изображениями, из которых нужно вытащить основные цвета.
     *
     * @return array
     */
    protected static function getActualColors(array $images): array
    {
        $actualColors = [];

        foreach ($images as $_image) {

            /**
             * @var $_image Image
             */
            $imageMainColor = $_image->palette->mainColor->color;

            if (array_key_exists($imageMainColor, $actualColors)) {
                $actualColors[$imageMainColor]++;
            } else {
                $actualColors[$imageMainColor] = 1;
            }

        }

        arsort($actualColors); // Наиболее популярные в начале.

        return $actualColors;
    }

    /**
     * Обновляет в указанной категории порядок основных цветов.
     *
     * @param  Category  $category  Категория, для которой нужно выполнить обновление.
     * @param  array     $colors    Массив с актуальными основными цветами.
     */
    protected static function saveColors(Category $category, array $colors): void
    {
        $category->colors = array_map(function ($_amount, $_color) {

            $categoryColorItem = new CategoryColorItem();
            $categoryColorItem->color = $_color;
            $categoryColorItem->amount = $_amount;

            return $categoryColorItem;

        }, $colors, array_keys($colors));

        $category->update_required = false;

        $category->save();
    }
}