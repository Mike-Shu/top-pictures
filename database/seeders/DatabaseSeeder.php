<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Image;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
        ]);

//        Category::factory()
//            ->count(5)
//            ->create();
//
//        Image::factory()
//            ->count(100)
//            ->create();
    }
}
