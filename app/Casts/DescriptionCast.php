<?php

namespace App\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DescriptionCast implements CastsAttributes
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