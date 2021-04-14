<?php

namespace App\Services\Category\Casts;

use App\Services\Category\Items\ColorItem;

/**
 * Типизация для столбца "colors".
 *
 * @package App\Services\Category\Casts
 */
class ColorsCast implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes
{
    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): array
    {
        $result = [];

        if (!empty($value)) {

            $colors = json_decode($value, true);

            $result = array_map(function($_item){

                return new ColorItem($_item);

            }, $colors);

        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value);
    }
}