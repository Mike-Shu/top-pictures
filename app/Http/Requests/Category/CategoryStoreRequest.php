<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Валидация формы для добавления новой категории.
 *
 * @package App\Http\Requests\Category
 */
class CategoryStoreRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $nameMaxLength = config('interface.category.name_max_length');
        $descMaxLength = config('interface.category.desc_max_length');

        return [
            'name'        => "required|string|max:{$nameMaxLength}|unique:categories",
            'description' => "nullable|string|max:{$descMaxLength}",
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.unique' => __('This category already exists.'),
        ];
    }
}
