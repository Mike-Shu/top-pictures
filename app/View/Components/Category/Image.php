<?php

namespace App\View\Components\Category;

use App\Items\ImageItem;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Image extends Component
{
    /**
     * @var ImageItem
     */
    public $image;

    /**
     * @param $image
     */
    public function __construct(ImageItem $image)
    {
        $this->image = $image;
    }

    /**
     * @return View|Closure|string
     */
    public function render()
    {
        return view('components.category.image');
    }
}
