<?php

namespace App\View\Components\Gallery;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Объект "категория" для списка на странице "Галерея".
 *
 * @package App\View\Components\Gallery
 */
class ListItem extends Component
{

    public $category;

    /**
     * @param  Category  $category
     */
    public function __construct(Category $category)
    {
        $this->category = $category;
    }

    /**
     * @return View|Closure|string
     */
    public function render()
    {
        return view('components.gallery.list-item');
    }
}
