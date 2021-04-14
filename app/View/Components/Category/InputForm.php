<?php

namespace App\View\Components\Category;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * Форма для добавления/редактирования категории.
 *
 * @package App\View\Components\Category
 */
class InputForm extends Component
{
    public $action;
    public $name;
    public $description;
    public $nameMaxLength;
    public $descMaxLength;
    public $updateMode;

    /**
     * @param  string  $action
     * @param  string  $name
     * @param  string  $description
     * @param  bool    $updateMode
     */
    public function __construct(string $action, string $name = '', string $description = '', bool $updateMode = false)
    {
        $this->action = $action;
        $this->name = $name;
        $this->description = $description;
        $this->nameMaxLength = config('interface.category.name_max_length');
        $this->descMaxLength = config('interface.category.desc_max_length');
        $this->updateMode = $updateMode;
    }

    /**
     * @return View|Closure|string
     */
    public function render()
    {
        return view('components.category.input-form');
    }
}
