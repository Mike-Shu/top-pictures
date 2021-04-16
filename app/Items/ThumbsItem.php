<?php

namespace App\Items;

use Illuminate\Database\Eloquent\Model;

/**
 * Коллекция миниатюр.
 *
 * @package App\Items
 */
class ThumbsItem extends BaseItem implements FromArrayable
{
    /**
     * @var string
     */
    public $large;

    /**
     * @var string
     */
    public $medium;

    /**
     * @var string
     */
    public $small;

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
    public function fromArray(array $data): ThumbsItem
    {
        $this->large = $data['large'];
        $this->medium = $data['medium'];
        $this->small = $data['small'];

        return $this;
    }
}