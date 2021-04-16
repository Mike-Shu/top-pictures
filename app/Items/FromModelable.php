<?php

namespace App\Items;

use Illuminate\Database\Eloquent\Model;

/**
 * @package App\Items
 */
interface FromModelable
{
    /**
     * Заполнить свойства объекта данными из модели.
     *
     * @param  Model  $model
     *
     * @return mixed
     */
    public function fromModel(Model $model);
}