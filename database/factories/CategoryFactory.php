<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;

class CategoryFactory extends Factory
{
    use WithFaker;

    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'description' => $this->faker->text,
            'amount'      => $this->faker->randomNumber(),
            'colors'      => $this->getColors(),
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
        ];
    }

    /**
     * @return array
     */
    private function getColors(): array
    {
        $result = [];

        for ($x = 0; $x < mt_rand(3, 7); $x++) {

            $result[] = [
                'color'  => get_random_color(),
                'amount' => $this->faker->numberBetween(1, 500),
            ];

        }

        usort($result, function (array $a, array $b) {
            return ($a['amount'] < $b['amount']);
        });

        return $result;
    }
}
