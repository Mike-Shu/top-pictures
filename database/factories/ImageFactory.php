<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            'category_id' => $this->getRandomTableId('categories'),
            'user_id'     => $this->getRandomTableId('users'),
            'name'        => md5($this->faker->text),
            'extension'   => $this->faker->randomElement(['jpg', 'jpeg', 'jpe']),
            'size'        => $this->faker->randomNumber(7, true),
            'processed'   => $this->faker->boolean,
            'description' => $this->faker->text,
            'width'       => $this->faker->numberBetween(1024, 3840),
            'height'      => $this->faker->numberBetween(1024, 3840),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ];
    }

    /**
     * @param  string  $table
     *
     * @return int
     */
    private function getRandomTableId(string $table): int
    {
        $ids = DB::table($table)
            ->select('id')
            ->get()
            ->all();

        $ids = array_column($ids, 'id');

        return $ids[array_rand($ids)];
    }
}
