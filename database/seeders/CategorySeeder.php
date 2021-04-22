<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * @return void
     */
    public function run()
    {
        Category::create([
            'name'        => 'Автоспорт',
            'description' => 'Гоночные мотоциклы, легковые и грузовые автомобили.',
        ]);

        Category::create([
            'name'        => 'Девушки',
            'description' => 'Милые девушки, изящные модели, гламурные модницы.',
        ]);

        Category::create([
            'name'        => 'Природа',
            'description' => 'Красивые природные пейзажи.',
        ]);

        Category::create([
            'name'        => 'Прочее',
            'description' => '',
        ]);
    }
}
