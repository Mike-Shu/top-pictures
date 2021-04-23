<?php

namespace Database\Factories;

use App\Items\ImageColorItem;
use App\Items\ImagePaletteItem;
use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageFactory extends Factory
{
    protected $model = Image::class;

    private $name;
    private $storageThumbDisk;
    private $storageThumbPath;

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

        $this->name = md5($this->faker->text);

        $this->storageThumbDisk = Storage::disk(
            config('interface.uploading.thumbs.disk')
        );

        $this->storageThumbPath = config('interface.uploading.thumbs.path');
    }

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
            'palette'     => $this->getPalette(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ];
    }

    /**
     * Состояние: гарантирует обработанное изображение.
     *
     * @return ImageFactory
     */
    public function processed(): ImageFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'processed' => true,
            ];
        });
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

    private function getPalette(): ImagePaletteItem
    {
        $main = new ImageColorItem();
        $main->color = get_random_reference_color();
        $main->weight = 100;

        $additional = [];
        $additionalCount = mt_rand(1, 7);

        for ($x = 0; $x < $additionalCount; $x++) {

            $color = new ImageColorItem();
            $color->color = get_random_reference_color();
            $color->weight = 90 - ($x * 10);

            $additional[] = $color;

        }

        $imagePalette = new ImagePaletteItem();
        $imagePalette->mainColor = $main;
        $imagePalette->additionalColors = $additional;

        return $imagePalette;
    }
}
