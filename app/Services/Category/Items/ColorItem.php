<?php

namespace App\Services\Category\Items;

/**
 * Объект "Основной цвет" для отображения в категориях.
 *
 * @package App\Services\Category\Items
 */
class ColorItem
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
     * Заполняет объект данными из переданного массива.
     *
     * @param  array  $data
     */
    public function fromArray(array $data)
    {
        $this->color = trim($data['color']);
        $this->amount = (int)$data['amount'];
    }

}