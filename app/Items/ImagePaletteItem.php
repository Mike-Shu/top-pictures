<?php

namespace App\Items;

use Illuminate\Contracts\Support\Arrayable;

/**
 * Палитра цветов для изображения.
 *
 * @package App\Items
 */
class ImagePaletteItem extends BaseItem implements FromArrayable
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

    /**
     * @param  array|null  $data
     */
    public function __construct(?array $data = null)
    {
        if (!is_null($data)) {
            $this->fromArray($data);
        }
    }

    /**
     * @inheritDoc
     */
    public function fromArray(array $data): ImagePaletteItem
    {
        $this->mainColor = $data['baseColor'];
        $this->additionalColors = $data['palette'];

        return $this;
    }
}