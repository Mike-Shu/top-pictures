<?php

namespace App\Items;

/**
 * "Основной цвет" для отображения в категориях.
 *
 * @package App\Items
 */
class CategoryColorItem extends BaseItem implements FromArrayable
{
    /**
     * HEX-кодировка цвета (например: "#E7D62C").
     *
     * @var string
     */
    public $color = '#000';

    /**
     * Количество изображений, которые были сопоставлены с этим цветом.
     *
     * @var int
     */
    public $amount = 0;

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
     * @return $this
     */
    public function fromArray(array $data): CategoryColorItem
    {
        $this->color = trim($data['color']);
        $this->amount = (int)$data['amount'];

        return $this;
    }
}