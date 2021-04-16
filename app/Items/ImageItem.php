<?php

namespace App\Items;

use App\Models\Category;
use App\Models\Image;
use App\Models\User;
use App\Services\Image\ProcessImageService;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Изображение.
 *
 * @package App\Items
 */
class ImageItem extends BaseItem implements FromModelable
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $extension;

    /**
     * @var int
     */
    public $size;

    /**
     * @var bool
     */
    public $processed;

    /**
     * @var string
     */
    public $description;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @var string
     */
    public $updated_at;

    /**
     * @var string
     */
    public $deleted_at;

    /**
     * @var Category|null
     */
    public $category = null;

    /**
     * @var User|null
     */
    public $user = null;

    /**
     * @var ThumbsItem
     */
    public $thumbs;

    /**
     * @var Image
     */
    private $model;

    /**
     * Диск, на котором лежат миниатюры.
     *
     * @var FilesystemAdapter
     */
    private $storageThumbDisk;

    /**
     * Где именно на диске лежат миниатюры.
     *
     * @var string
     */
    private $storageThumbPath;

    /**
     * @param  Image|null  $model
     */
    public function __construct(?Image $model = null)
    {
        $this->storageThumbDisk = Storage::disk(
            config('interface.uploading.thumbs.disk')
        );

        $this->storageThumbPath = config('interface.uploading.thumbs.path');

        if (!is_null($model)) {
            $this->fromModel($model);
        }
    }

    /**
     * @inheritDoc
     */
    public function fromModel(Model $model): ImageItem
    {
        /**
         * @var $model Image
         */
        $this->model = $model;

        $this->id = $model->id;
        $this->name = $model->name;
        $this->extension = $model->extension;
        $this->size = $model->size;
        $this->processed = $model->processed;
        $this->description = (string)$model->description;
        $this->width = $model->width;
        $this->height = $model->height;
        $this->created_at = $model->created_at;
        $this->updated_at = $model->updated_at;
        $this->deleted_at = $model->deleted_at;
        $this->thumbs = $this->getThumbs();

        return $this;
    }

    /**
     * Добавляет информацию о категории.
     *
     * @return $this
     */
    public function setCategory(): ImageItem
    {
        $cacheKey = md5("category{$this->model->category_id}");
        $cached = runtime_cache($cacheKey);

        if (!empty($cached)) {
            $this->category = $cached;

            return $this;
        }

        $this->category = $this->model->category()->first();

        runtime_cache($cacheKey, $this->category);

        return $this;
    }

    /**
     * Добавляет информацию о пользователе.
     *
     * @return $this
     */
    public function setUser(): ImageItem
    {
        $cacheKey = md5("user{$this->model->user_id}");
        $cached = runtime_cache($cacheKey);

        if (!empty($cached)) {
            $this->user = $cached;

            return $this;
        }

        $this->user = $this->model->user()->first();

        runtime_cache($cacheKey, $this->user);

        return $this;
    }

    /**
     * Возвращает коллекцию с миниатюрами для текущего изображения.
     *
     * @return ThumbsItem
     */
    private function getThumbs(): ThumbsItem
    {
        return new ThumbsItem([
            'large'  => $this->getThumb(ProcessImageService::LARGE_THUMB_TYPE),
            'medium' => $this->getThumb(ProcessImageService::MIDDLE_THUMB_TYPE),
            'small'  => $this->getThumb(ProcessImageService::SMALL_THUMB_TYPE),
        ]);
    }

    /**
     * Возвращает объект с одной миниатюрой указанного типа.
     *
     * @param  string  $type
     *
     * @return ThumbItem
     * @see ProcessImageService::LARGE_THUMB_TYPE
     * @see ProcessImageService::MIDDLE_THUMB_TYPE
     * @see ProcessImageService::SMALL_THUMB_TYPE
     */
    private function getThumb(string $type): ThumbItem
    {
        $imageName = "{$this->name}.{$this->extension}";
        $path = config("interface.uploading.thumbs.{$type}.path");
        $path = "{$this->storageThumbPath}/{$path}/{$imageName}";

        $thumbUrl = $this->storageThumbDisk->url($path);
        $thumbDimension = $this->getImageDimension($path);

        $thumbItemData = array_merge([
            'url' => $thumbUrl,
        ], $thumbDimension);

        return new ThumbItem($thumbItemData);
    }

    /**
     * Возвращает коллекцию с информацией о размере изображения.
     *
     * @param  string  $path
     *
     * @return array
     */
    private function getImageDimension(string $path): array
    {
        $result = [
            'width'  => 0,
            'height' => 0,
            'html'   => '',
        ];

        try {

            $file = $this->storageThumbDisk->get($path);
            list($result['width'], $result['height'], , $result['html']) = getimagesizefromstring($file);

        } catch (FileNotFoundException $e) {

            $eMessage = mb_strtolower($e->getMessage());
            Log::warning(__METHOD__.": {$eMessage}");

        }

        return $result;
    }
}