<?php

namespace App\Casts;

use App\Items\ImageColorItem;
use App\Items\ImagePaletteItem;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class ImagePaletteCast implements CastsAttributes
{
    /**
     * @inheritDoc
     */
    public function get($model, string $key, $value, array $attributes): ImagePaletteItem
    {
        $result = null;

        if (!empty($value)) {

            $palette = json_decode($value, true);

            $mainColor = new ImageColorItem($palette['mainColor']);

            $additionalColors = array_map(function ($_color) {

                return new ImageColorItem($_color);

            }, $palette['additionalColors']);

            $result = new ImagePaletteItem();
            $result->mainColor = $mainColor;
            $result->additionalColors = $additionalColors;

        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function set($model, string $key, $value, array $attributes)
    {
        return json_encode($value);
    }
}