<?php

namespace App\Items;

/**
 * Один цвет в палитре для изображения.
 *
 * @package App\Items
 */
class ImageColorItem extends BaseItem implements FromArrayable
{
    /**
     * HEX-кодировка цвета (например: "#E7D62C").
     *
     * @var string
     */
    public $color = '#000';

    /**
     * Условный вес цвета: чем выше значение, тем наиболее доминирующим является цвет (10-100).
     *
     * @var int
     */
    public $weight = 0;

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
    public function fromArray(array $data): ImageColorItem
    {
        $this->color = $data['color'];
        $this->weight = $data['weight'];

        return $this;
    }
}