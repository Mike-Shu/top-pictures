<?php

namespace App\Items;

/**
 * Палитра цветов для изображения.
 *
 * @package App\Items
 */
class ImagePaletteItem extends BaseItem
{
    /**
     * Основной цвет изображения.
     *
     * @var ImageColorItem
     */
    public $mainColor;

    /**
     * Набор дополнительных цветов.
     *
     * @var ImageColorItem[]
     */
    public $additionalColors;
}