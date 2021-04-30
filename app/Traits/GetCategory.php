<?php

namespace App\Traits;

use App\Exceptions\NotFoundException;
use App\Models\Category;
use App\Services\CommonTools;
use App\Services\Errors;
use Cache;

trait GetCategory
{
    /**
     * @param  int   $id
     * @param  bool  $trashed      Если передать "true", то метод вернет даже архивную категорию.
     * @param  int   $callerDepth  Смотри: CommonTools::getCaller()
     *
     * @return Category
     * @throws NotFoundException
     */
    protected function getCategory(
        int $id,
        bool $trashed = true,
        int $callerDepth = 1
    ): Category {

        $category = $trashed
            ? Category::withTrashed()->find($id)
            : Category::find($id);

        if (empty($category)) {

            $caller = CommonTools::getCaller($callerDepth);

            throw new NotFoundException("The category #{$id} was not found: {$caller}");

        }

        return $category;
    }

    /**
     * @param  int   $id
     * @param  int   $ttl          Время жизни кеша, сек.
     * @param  bool  $trashed      Если передать "true", то метод вернет даже архивную категорию.
     * @param  int   $callerDepth  Смотри: CommonTools::getCaller()
     *
     * @return Category
     * @throws NotFoundException
     */
    protected function getCategoryCached(
        int $id,
        int $ttl = 60,
        bool $trashed = true,
        int $callerDepth = 1
    ): Category {

        $cacheKey = md5(__METHOD__.$id);
        $category = Cache::get($cacheKey);

        if (empty($category)) {

            $category = $this->getCategory($id, $trashed, $callerDepth);
            Cache::put($cacheKey, $category, $ttl);

        }

        return $category;
    }
}
