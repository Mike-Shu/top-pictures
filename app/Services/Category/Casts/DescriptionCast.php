<?php


namespace App\Services\Category\Casts;

/**
 * Типизация для столбца "description".
 *
 * @package App\Services\Category\Casts
 */
class DescriptionCast implements \Illuminate\Contracts\Database\Eloquent\CastsAttributes
{

    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): string
    {
        return (string)$value;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes): string
    {
        return trim($value);
    }
}