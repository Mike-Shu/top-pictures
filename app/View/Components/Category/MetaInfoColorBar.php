<?php

namespace App\View\Components\Category;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * "Основной цвет" для отображения в категориях.
 *
 * @package App\View\Components\Category
 */
class MetaInfoColorBar extends Component
{
    public $color;

    /**
     * @param  string  $color
     */
    public function __construct(string $color)
    {
        $this->color = $color;
    }

    /**
     * @return View|Closure|string
     */
    public function render()
    {
        return view('components.category.meta-info-color-bar');
    }
}
