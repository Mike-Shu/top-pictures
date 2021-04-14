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
        $this->now = Carbon::now();
    }

    public function definition(): array
    {

        $desc = $this->faker->boolean(70)
            ? $this->faker->text
            : null;

        $colors = $this->getColors();

        $amount = empty($colors)
            ? 0
            : $this->faker->randomNumber(4);

        $deleted = ($amount && $this->faker->boolean(30))
            ? $this->now
            : null;

        return [
            'name'        => $this->faker->words(3, true),
            'description' => $desc,
            'amount'      => $amount,
            'colors'      => $colors,
            'created_at'  => $this->now,
            'updated_at'  => $this->now,
            'deleted_at'  => $deleted,
        ];
    }

    /**
     * Состояние: гарантирует не удаленную пустую категорию.
     *
     * @return CategoryFactory
     */
    public function empty(): CategoryFactory
    {
        return $this->state(function () {
            return [
                'amount'     => 0,
                'deleted_at' => null,
            ];
        });
    }

    /**
     * Состояние: гарантирует не удаленную и не пустую категорию.
     *
     * @return CategoryFactory
     */
    public function notEmpty(): CategoryFactory
    {
        return $this->state(function () {
            return [
                'amount'     => $this->faker->randomNumber(4),
                'deleted_at' => null,
            ];
        });
    }

    /**
     * Состояние: гарантирует заполнение всех полей в модели.
     *
     * @param  bool  $deleted  Если передать "true", то поле "deleted_at" примет значение "now()".
     *
     * @return CategoryFactory
     */
    public function fullHouse(bool $deleted = false): CategoryFactory
    {

        $deleted = $deleted
            ? $this->now
            : null;

        return $this->state(function () use ($deleted) {
            return [
                'description' => $this->faker->text,
                'amount'      => $this->faker->randomNumber(4),
                'colors'      => $this->getColors(true),
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
