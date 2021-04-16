<?php

namespace App\Items;

/**
 * Миниатюра.
 *
 * @package App\Items
 */
class ThumbItem extends BaseItem implements FromArrayable
{
    /**
     * @var string
     */
    public $url;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var string
     */
    public $html;

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
     * @return ThumbItem
     */
    public function fromArray(array $data): ThumbItem
    {
        $this->url = $data['url'];
        $this->width = $data['width'];
        $this->height = $data['height'];
        $this->html = $data['html'];

        return $this;
    }
}