<?php

namespace App\View\Components\Category;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Метаданные о категории.
 *
 * @package App\View\Components\Category
 */
class MetaInfo extends Component
{
    public $amount;
    public $colors;
    public $deletedAt;

    /**
     * @param  int          $amount
     * @param  array|null   $colors
     * @param  string|null  $deletedAt
     */
    public function __construct(int $amount, ?array $colors, ?string $deletedAt)
    {
        $this->amount = $amount;
        $this->colors = $colors;
        $this->deletedAt = $deletedAt;
    }

    /**
     * @return View|Closure|string
     */
    public function render()
    {
        return view('components.category.meta-info');
    }
}
