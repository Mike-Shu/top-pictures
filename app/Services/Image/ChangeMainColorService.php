<?php

namespace App\Services\Image;

use App\Exceptions\BadRequestException;
use App\Items\ImageColorItem;
use App\Items\RgbColorItem;
use App\Services\Category\CountingColorsService;
use App\Services\CommonTools;
use App\Services\RequestService;
use App\Traits\GetImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;

/**
 * Управление изображением: изменение основного цвета для изображения.
 *
 * @package App\Services\Image
 */
class ChangeMainColorService extends RequestService
{
    use GetImage;

    public function changeMainColor(): JsonResponse
    {
        try {

            $imageId = $this->request->get('imageId', 0);
            $colorValue = $this->request->get('colorValue', '');

            $image = $this->getImage($imageId);
            $image->palette->mainColor = $this->getMainColor($colorValue);
            $image->save();

            CountingColorsService::setUpdateRequired($image->category);

            return Response::json([
                'status' => static::STATUS_OK,
            ]);

        } catch (\Exception $e) {

            // @codeCoverageIgnoreStart
            if (App::environment('testing') === false) {
                Log::warning(
                    $e->getMessage()
                );
            }
            // @codeCoverageIgnoreEnd

            return Response::json([
                'status' => static::STATUS_FAILED,
            ], $e->getCode());

        }
    }

    /**
     * @param  string  $value
     *
     * @return ImageColorItem
     * @throws BadRequestException
     */
    protected function getMainColor(string $value): ImageColorItem
    {
        $value = trim($value);

        if (empty($value)) {

            $caller = CommonTools::getCaller(1);

            throw new BadRequestException("The color value is empty or undefined: {$caller}");

        }

        // Вытащим RGB из строки вида "rgb(0, 32, 128)".
        $value = mb_substr($value, 0, (mb_strlen($value) - 1)); // "rgb(0, 32, 128"
        $value = mb_substr($value, 4); // "0, 32, 128"

        $rgb = explode(',', $value);
        $rgb = array_map('intval', $rgb);
        $rgb = new RgbColorItem($rgb);

        $color = new ImageColorItem();
        $color->color = rgb2hex($rgb);
        $color->weight = 100;

        return $color;
    }

}