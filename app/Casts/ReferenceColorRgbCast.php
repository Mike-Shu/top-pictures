<?php

namespace App\Casts;

use App\Items\RgbColorItem;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ReferenceColorRgbCast implements CastsAttributes
{
    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): RgbColorItem
    {
        $rgb = json_decode($value, true);

        return new RgbColorItem($rgb);
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return null; // Оставим пустоту, т.к. саму модель обновлять запрещено.
    }
}