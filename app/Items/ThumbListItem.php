<?php

namespace App\Items;

use Illuminate\Database\Eloquent\Model;

/**
 * Коллекция миниатюр.
 *
 * @package App\Items
 */
class ThumbListItem extends BaseItem implements FromArrayable
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
    public function fromArray(array $data): ThumbListItem
    {
        $this->large = $data['large'];
        $this->medium = $data['medium'];
        $this->small = $data['small'];

        return $this;
    }
}