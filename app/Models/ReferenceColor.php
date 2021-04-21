<?php

namespace App\Models;

use App\Casts\ReferenceColorRgbCast;
use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Эталонная RGB-палитра.
 *
 * @package App\Models
 */
class ReferenceColor extends Model
{
    protected $casts = [
        'rgb' => ReferenceColorRgbCast::class,
    ];

    /**
     * @param  array  $attributes
     * @param  array  $options
     *
     * @return bool
     * @throws Exception
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        throw new Exception('Reference color updates are not allowed');
    }

    /**
     * @param  array  $options
     *
     * @return bool
     * @throws Exception
     */
    public function save(array $options = []): bool
    {
        throw new Exception('Reference color updates are not allowed');
    }

    /**
     * @return bool|null
     * @throws Exception
     */
    public function delete(): ?bool
    {
        throw new Exception('Removal of the reference color is not allowed');
    }
}