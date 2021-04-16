<?php


namespace App\Services\Image;


use App\Exceptions\ProcessImageException;
use App\Models\Image;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Exception\NotFoundException;
use Intervention\Image\ImageManager;

class ProcessImageService
{
    const LARGE_THUMB_TYPE = 'large';
    const MIDDLE_THUMB_TYPE = 'middle';
    const SMALL_THUMB_TYPE = 'small';

    /**
     * Исходное изображение для обработки.
     *
     * @var Image
     */
    private $image;

    /**
     * Полное имя изображения (а-ля "72a74828009be9f66280d06401545b5d.jpg").
     *
     * @var string
     */
    private $imageName;

    /**
     * Собственно, само изображение (двоичные данные).
     *
     * @var string
     */
    private $imageContent;

    /**
     * Менеджер для манипуляции над изображением.
     *
     * @see http://image.intervention.io/getting_started/introduction
     * @var ImageManager
     */
    private $manager;

    /**
     * Диск, на котором лежит загруженный файл.
     *
     * @var FilesystemAdapter
     */
    private $storageDisk;

    /**
     * Где именно на диске лежит загруженный файл.
     *
     * @var string
     */
    private $storagePath;

    /**
     * Диск, на котором будут лежать миниатюры.
     *
     * @var FilesystemAdapter
     */
    private $storageThumbDisk;

    /**
     * Где на диске должны лежать миниатюры.
     *
     * @var string
     */
    private $storageThumbPath;

    /**
     * Доступные типы для миниатюр.
     *
     * @var string[]
     */
    public $validThumbTypes;

    /**
     * @param  ImageManager  $manager
     */
    public function __construct(ImageManager $manager)
    {
        $this->manager = $manager;

        $this->storageDisk = Storage::disk(
            config('interface.uploading.storage.disk')
        );
        $this->storagePath = config('interface.uploading.storage.path');

        $this->storageThumbDisk = Storage::disk(
            config('interface.uploading.thumbs.disk')
        );
        $this->storageThumbPath = config('interface.uploading.thumbs.path');

        $this->validThumbTypes = [
            static::LARGE_THUMB_TYPE,
            static::MIDDLE_THUMB_TYPE,
            static::SMALL_THUMB_TYPE,
        ];
    }

    /**
     * @param  Image  $image
     *
     * @return $this
     */
    public function setImage(Image $image): ProcessImageService
    {
        $this->image = $image;
        $this->setImageName();

        return $this;
    }

    /**
     * Создаёт для исходного изображения все миниатюры.
     *
     * @return $this
     */
    public function makeThumbs(): ProcessImageService
    {

        try {

            $this->makeThumb(static::LARGE_THUMB_TYPE);
            $this->makeThumb(static::MIDDLE_THUMB_TYPE);
            $this->makeThumb(static::SMALL_THUMB_TYPE);

        } catch (ProcessImageException $e) {

            if (App::environment('testing') === false) { // @codeCoverageIgnore
                $eMessage = mb_strtolower($e->getMessage());
                Log::warning(__METHOD__.": {$this->imageName}: {$eMessage}");
            }

        }

        return $this;

    }

    /**
     * Ставит отметку о том, что обработка изображения завершена.
     */
    public function processingComplete(): void
    {
        $this->image->processed = true;
        $this->image->save();
    }

    /**
     * Устанавливает полное имя изображения (а-ля "72a74828009be9f66280d06401545b5d.jpg").
     */
    private function setImageName(): void
    {
        $name = $this->image->name;
        $ext = $this->image->extension;

        $this->imageName = "{$name}.{$ext}";
    }

    /**
     * Создаёт для исходного изображения одну миниатюру указанного типа.
     *
     * @param  string  $type
     *
     * @throws ProcessImageException
     * @see ProcessImageService::MIDDLE_THUMB_TYPE
     * @see ProcessImageService::SMALL_THUMB_TYPE
     * @see ProcessImageService::LARGE_THUMB_TYPE
     */
    private function makeThumb(string $type): void
    {
        try {

            $imageContent = $this->getImageContent();
            $image = $this->manager->make($imageContent);

            $resizeWidthTo = config("interface.uploading.thumbs.{$type}.width");
            $putQuality = config("interface.uploading.thumbs.{$type}.quality");
            $putTo = config("interface.uploading.thumbs.{$type}.path");
            $putTo = "{$this->storageThumbPath}/{$putTo}/{$this->imageName}";

            $image->resize(
                $resizeWidthTo,
                null,
                function ($constraint) {
                    $constraint->aspectRatio();
                }
            );

            $putContent = $image->stream('jpg', $putQuality)
                ->getContents();

            $this->storageThumbDisk->put($putTo, $putContent);

        } catch (Exception $e) {
            throw new ProcessImageException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Возвращает содержимое изображения (двоичные данные).
     *
     * @return mixed|string
     * @throws FileNotFoundException
     */
    private function getImageContent(): string
    {
        if (!empty($this->imageContent)) {
            return $this->imageContent;
        }

        $imagePath = "{$this->storagePath}/{$this->imageName}";

        $this->imageContent = $this->storageDisk->get($imagePath);

        return $this->imageContent;
    }
}