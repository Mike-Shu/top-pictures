<?php

namespace Database\Factories;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    public function definition(): array
    {
        return [
            'name'        => $this->faker->md5,
            'extension'   => $this->faker->randomElement(['jpg', 'jpeg', 'jpe']),
            'size'        => $this->faker->randomNumber(7, true),
            'pending'     => $this->faker->boolean,
            'description' => $this->faker->text,
            'width'       => $this->faker->numberBetween(1024, 3840),
            'height'      => $this->faker->numberBetween(1024, 3840),
            'visible'     => $this->faker->boolean,
            'created_at'  => Carbon::now(),
            'updated_at'  => Carbon::now(),
            'user_id'     => function () {
                return User::factory()->create()->id;
            },
        ];
    }
}
