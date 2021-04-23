<?php

namespace App\Services\ReferenceColors;

use App\Models\ReferenceColor;
use Illuminate\Support\Facades\Cache;

/**
 * Получение эталонной RGB-палитры.
 *
 * @package App\Services\ReferenceColor
 */
class ReferenceColorsStorage
{
    const CACHE_TTL = (3600 * 1); // Время жизни кеша.
    const RGB_TYPE = "rgb"; // RGB-представление.
    const HEX_TYPE = "hex"; // Шестнадцатеричное представление.
    const DEC_TYPE = "dec"; // Десятичное представление.

    /**
     * Получить множество цветов в RGB-представлении.
     *
     * @param  bool  $useCache
     *
     * @return array
     */
    public static function getColorsRgb(bool $useCache = true): array
    {
        return static::getColorsByType(static::RGB_TYPE, $useCache);
    }

    /**
     * Получить множество цветов в шестнадцатеричном представлении.
     *
     * @param  bool  $sharp  Если передать "false", то значения вернуться без символа "#".
     * @param  bool  $useCache
     *
     * @return array
     */
//    public static function getColorsHex(bool $sharp = true, bool $useCache = true): array
//    {
//        return static::getColorsByType(static::HEX_TYPE, $useCache);
//    }

    /**
     * Получить множество цветов в десятичном представлении.
     *
     * @param  bool  $useCache
     *
     * @return array
     */
//    public static function getColorsDec(bool $useCache = true): array
//    {
//        return static::getColorsByType(static::DEC_TYPE, $useCache);
//    }

    /**
     * Получить все доступные множества.
     *
     * @param  bool  $useCache
     *
     * @return array
     */
    public static function getColors(bool $useCache = true): array
    {
        $cacheKey = md5('getReferenceColors');
        $fromCache = Cache::get($cacheKey);

        if ($useCache && !empty($fromCache)) {
            return $fromCache;
        }

        $fromDatabase = ReferenceColor::all()
            ->keyBy('id')
            ->all();

        $fromDatabase = array_map(function ($_color) {

            /**
             * @var $_color ReferenceColor
             */
            return [
                'rgb' => $_color->rgb,
                'hex' => $_color->hex,
                'dec' => $_color->dec,
            ];

        }, $fromDatabase);

        Cache::put($cacheKey, $fromDatabase, static::CACHE_TTL);

        return $fromDatabase;
    }

    /**
     * Получить множество цветов в указанном представлении (системы счисления).
     *
     * @param  string  $type
     * @param  bool    $useCache
     *
     * @return array
     */
    private static function getColorsByType(string $type, bool $useCache = true): array
    {
        return array_map(function ($_color) use ($type) {

            return $_color[$type];

        }, static::getColors($useCache));
    }
}