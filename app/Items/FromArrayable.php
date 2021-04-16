<?php

namespace App\Items;

/**
 * @package App\Items
 */
interface FromArrayable
{
    /**
     * Заполнить свойства объекта данными из массива.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function fromArray(array $data);
}