<?php

namespace App\Items;

use Exception;
use InvalidArgumentException;

/**
 * Один RGB-цвет.
 *
 * @package App\Items
 */
class RgbColorItem extends BaseItem implements FromArrayable
{
    /**
     * @var int
     */
    public $red;

    /**
     * @var int
     */
    public $green;

    /**
     * @var int
     */
    public $blue;

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
    public function fromArray(array $data): RgbColorItem
    {
        try {

            list($red, $green, $blue) = $data;

            $this->red = (int)$red;
            $this->green = (int)$green;
            $this->blue = (int)$blue;

        } catch (Exception $e) {
            throw new InvalidArgumentException($e->getMessage(), $e->getCode(), $e);
        }

        return $this;
    }
}