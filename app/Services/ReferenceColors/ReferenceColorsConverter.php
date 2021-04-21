<?php

namespace App\Services\ReferenceColors;

use App\Items\RgbColorItem;

/**
 * Приведение какого-либо цвета к одному из эталонных цветов.
 *
 * @package App\Services\ReferenceColor
 */
class ReferenceColorsConverter
{
    /**
     * Для указанного RGB-цвета возвращает ближайший шестнадцатеричный цвет из эталонной палитры.
     * Например: [255, 128, 0] => #ff8000
     *
     * @param  RgbColorItem  $rgb
     * @param  bool          $sharp
     *
     * @return string
     */
    public static function getReferenceHexColorByRgb(RgbColorItem $rgb, bool $sharp = true): string
    {
        $referenceColor = static::getClosestColor($rgb);

        return rgb2hex($referenceColor, $sharp);
    }

    /**
     * Для указанного RGB-цвета возвращает ближайший RGB-цвет из эталонной палитры.
     * Например: [255, 128, 0] => [255, 128, 0]
     *
     * @param  RgbColorItem  $rgbColor
     *
     * @return RgbColorItem
     */
    private static function getClosestColor(RgbColorItem $rgbColor): RgbColorItem
    {
        $rgbPalette = ReferenceColorsStorage::getColorsRgb();

        $distances = array_map(function ($_rgbPaletteColor) use ($rgbColor) {

            return static::getDistanceFromColor($rgbColor, $_rgbPaletteColor);

        }, $rgbPalette);

        asort($distances);

        $keys = array_keys($distances);

        return $rgbPalette[current($keys)];
    }

    /**
     * Возвращает относительную дистанцию между искомым и эталонным цветом.
     *
     * @param  RgbColorItem  $fromColor  Искомый RGB-цвет.
     * @param  RgbColorItem  $toColor    Эталонный RGB-цвет.
     *
     * @return float
     */
    private static function getDistanceFromColor(RgbColorItem $fromColor, RgbColorItem $toColor): float
    {
        list($r1, $g1, $b1) = array_values($fromColor->toArray());
        list($r2, $g2, $b2) = array_values($toColor->toArray());

        $rPow = pow($r2 - $r1, 2);
        $gPow = pow($g2 - $g1, 2);
        $bPow = pow($b2 - $b1, 2);

        return sqrt($rPow + $gPow + $bPow);
    }

}