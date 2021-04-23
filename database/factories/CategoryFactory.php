<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CategoryFactory extends Factory
{
    use WithFaker;

    protected $model = Category::class;
    private $now;

    public function __construct(
        $count = null,
        ?Collection $states = null,
        ?Collection $has = null,
        ?Collection $for = null,
        ?Collection $afterMaking = null,
        ?Collection $afterCreating = null,
        $connection = null
    ) {
        parent::__construct($count, $states, $has, $for, $afterMaking, $afterCreating, $connection);
        $this->now = now();
    }

    public function definition(): array
    {

        $desc = $this->faker->boolean(70)
            ? $this->faker->text
            : null;

        $colors = $this->getColors();

        $deleted = $this->faker->boolean(30)
            ? $this->now
            : null;

        return [
            'name'           => $this->faker->words(3, true),
            'description'    => $desc,
            'colors'         => $colors,
            'cover_image_id' => 0,
            'created_at'     => $this->now,
            'updated_at'     => $this->now,
            'deleted_at'     => $deleted,
        ];
    }

    /**
     * Состояние: гарантирует заполнение всех полей в модели.
     *
     * @param  bool  $deleted  Если передать "true", то поле "deleted_at" примет значение "now()".
     * @param  bool  $colors   Если передать "false", то поле "colors" заполнено не будет.
     *
     * @return CategoryFactory
     */
    public function fullHouse(bool $deleted = false, bool $colors = true): CategoryFactory
    {
        $deleted = $deleted
            ? $this->now
            : null;

        $colors = $colors
            ? $this->getColors(true)
            : null;

        return $this->state(function () use ($deleted, $colors) {
            return [
                'description' => $this->faker->text,
                'colors'      => $colors,
                'deleted_at'  => $deleted,
            ];
        });
    }

    /**
     * @param  bool  $guarantee  Если передать "true", то результат гарантированно будет содержать хотя бы один цвет.
     *
     * @return array
     */
    private function getColors(bool $guarantee = false): array
    {
        $result = [];

        if (!$guarantee && $this->faker->boolean(30)) {
            return $result; // Не добавлять цвета вообще.
        }

        for ($x = 0; $x < mt_rand(1, 10); $x++) {

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
