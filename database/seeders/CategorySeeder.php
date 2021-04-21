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
            'name' => 'Автоспорт',
        ]);

        Category::create([
            'name' => 'Девушки',
        ]);

        Category::create([
            'name' => 'Природа',
        ]);

        Category::create([
            'name' => 'Прочее',
        ]);
    }
}
