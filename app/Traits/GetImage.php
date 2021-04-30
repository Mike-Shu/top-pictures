<?php

namespace App\Traits;

use App\Exceptions\NotFoundException;
use App\Models\Image;
use App\Services\CommonTools;
use Cache;

trait GetImage
{
    /**
     * Возвращает изображение, предварительно проверив его существование.
     *
     * @param  int   $id
     * @param  bool  $trashed      Если передать "true", то метод вернет даже архивное изображение.
     * @param  int   $callerDepth  Смотри: CommonTools::getCaller()
     *
     * @return Image
     * @throws NotFoundException
     * @see CommonTools::getCaller()
     */
    protected function getImage(
        int $id,
        bool $trashed = true,
        int $callerDepth = 1
    ): Image {

        $image = $trashed
            ? Image::withTrashed()->find($id)
            : Image::find($id);

        if (empty($image)) {

            $caller = CommonTools::getCaller($callerDepth);

            throw new NotFoundException("The image #{$id} was not found: {$caller}");

        }

        return $image;
    }

    /**
     * Возвращает кешированное изображение, предварительно проверив его существование.
     *
     * @param  int   $id
     * @param  int   $ttl          Время жизни кеша, сек.
     * @param  bool  $trashed      Если передать "true", то метод вернет даже архивное изображение.
     * @param  int   $callerDepth  Смотри: CommonTools::getCaller()
     *
     * @return Image
     * @throws NotFoundException
     */
    protected function getImageCached(
        int $id,
        int $ttl = 60,
        bool $trashed = true,
        int $callerDepth = 1
    ): Image {

        $cacheKey = md5(__METHOD__.$id);
        $image = Cache::get($cacheKey);

        if (empty($image)) {

            $image = $this->getImage($id, $trashed, $callerDepth);
            Cache::put($cacheKey, $image, $ttl);

        }

        return $image;
    }
}
