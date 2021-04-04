<?php

namespace App\Services\Uploader;

use App\Exceptions\UploaderException;
use App\Models\Image;
use App\Models\User;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Pion\Laravel\ChunkUpload\Exceptions\UploadFailedException;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Storage;

/**
 * Загрузчик файлов в хранилище (на основе "laravel-chunk-upload" и "resumable.js").
 *
 * @see     https://github.com/pionl/laravel-chunk-upload
 * @see     https://github.com/23/resumable.js
 *
 * @package App\Services
 */
class UploaderService
{

    const STATUS_OK = "Ok"; // Загрузка выполнена успешно.
    const STATUS_FAILED = "Failed"; // При выполнении загрузки что-то пошло не так.

    const ERR_CODE_MISSING_FILE = 400; // В запросе отсутствует файл.
    const ERR_CODE_FILE_TYPE = 415; // Недопустимый тип файла.
    const ERR_CODE_DUPLICATE_FILE = 409; // Такой файл уже существует.
    const ERR_CODE_FAILED_PUT_FILE = 500; // Не удалось поместить файл на диск.

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Repository|Application|mixed
     */
    private $storageDisk;

    /**
     * @var Repository|Application|mixed
     */
    private $storagePath;

    /**
     * @var Repository|Application|mixed
     */
    private $chunkStorageDisk;

    /**
     * @var Repository|Application|mixed
     */
    private $chunkStoragePath;

    /**
     * @var User|Authenticatable|null
     */
    private $user;

    /**
     * @param  Request  $request
     */
    public function __construct(Request $request)
    {

        $this->request = $request;

        // От чьего имени будем загружать файл.
        $this->user = auth()->user();

        // Куда будем загружать файл.
        $this->storageDisk = config('uploading.storage.disk');
        $this->storagePath = config('uploading.storage.path');

        // Куда будем загружать фрагмент.
        $this->chunkStorageDisk = config('chunk-upload.storage.disk');
        $this->chunkStoragePath = config('chunk-upload.storage.chunks');

    }

    /**
     * Загружает один файл.
     *
     * @return JsonResponse
     */
    public function uploadFile(): JsonResponse
    {

        try {

            return $this->uploadChunks();

        } catch (Exception $e) {

            $eCode = $e->getCode();
            $eMessage = $e->getMessage();

            // Коды ошибок, которые не должны попасть в лог.
            $ignoredExceptions = [
                static::ERR_CODE_FILE_TYPE,
                static::ERR_CODE_DUPLICATE_FILE,
            ];

            if (!in_array($eCode, $ignoredExceptions)) {
                Log::critical(__METHOD__.': '.$eMessage);
            }

            return response()->json([
                'status'    => static::STATUS_FAILED,
                'error'     => $eCode,
                'error_msg' => $eMessage,
            ]);

        }

    }

    /**
     * Контролирует загрузку частей файла.
     *
     * @return JsonResponse
     * @throws UploadFailedException
     * @throws UploaderException
     */
    protected function uploadChunks(): JsonResponse
    {

        $receiver = new FileReceiver(
            "file",
            $this->request,
            HandlerFactory::classFromRequest($this->request)
        );


        // Если файл не был добавлен в запрос.
        if ($receiver->isUploaded() === false) {

            throw new UploaderException(
                'The request is missing a file',
                static::ERR_CODE_MISSING_FILE
            );
        }

        $received = $receiver->receive();
        $handler = $receiver->getHandler();

        // Загрузка файла завершена (загружены все фрагменты).
        if ($received->isFinished()) {

            $file = $received->getFile();

            // Если тип файла не допущен для загрузки.
            if (!in_array(
                $file->getMimeType(),
                config('uploading.resumable.fileType')
            )) {

                // Удалим временный файл (chunk).
                $fileName = $file->getFilename();
                $filePath = "{$this->chunkStoragePath}/{$fileName}";

                Storage::disk($this->chunkStorageDisk)
                    ->delete($filePath);

                throw new UploaderException(
                    'The file type is not allowed',
                    static::ERR_CODE_FILE_TYPE
                );

            }

            return $this->saveFile($file);

        }

        // Загружен только фрагмент файла.
        return response()->json([
            'status' => static::STATUS_OK,
            "done"   => $handler->getPercentageDone(),
            'chunk'  => $handler->getChunkFileName(), // Требуется для тестирования.
        ]);

    }

    /**
     * Пишет файл в хранилище и кладёт META в БД.
     *
     * @param  UploadedFile  $file
     *
     * @return JsonResponse
     * @throws UploaderException
     */
    protected function saveFile(UploadedFile $file): JsonResponse
    {

        $fileContent = $file->getContent();
        $fileName = md5($fileContent);
        $fileExt = $file->getClientOriginalExtension();
        $fileSize = $file->getSize();
        $fileTmpPath = $file->getPathname(); // Полный путь к временному файлу.

        list($imageWidth, $imageHeight) = getimagesize($fileTmpPath);

        // Не забудем удалить временный файл (chunks).
        unlink($fileTmpPath);

        // Если файл уже имеется в хранилище.
        if (Image::NotUnique($fileName)) {

            throw new UploaderException(
                'The file already exists',
                static::ERR_CODE_DUPLICATE_FILE
            );

        }

        // Пишем файл в хранилище.
        $disk = Storage::disk($this->storageDisk);
        $filePath = "{$this->storagePath}/{$fileName}.{$fileExt}";

        // Если положить файл в хранилище не удалось.
        if ($disk->put($filePath, $fileContent) === false) {

            // @codeCoverageIgnoreStart
            throw new UploaderException(
                'Failed to put file on disk',
                static::ERR_CODE_FAILED_PUT_FILE
            );
            // @codeCoverageIgnoreEnd

        }

        // Кладём META в БД.
        $this->user
            ->images()
            ->create([
                'name'      => $fileName,
                'extension' => $fileExt,
                'size'      => $fileSize,
                'width'     => $imageWidth,
                'height'    => $imageHeight,
            ]);

        return response()->json([
            'status' => static::STATUS_OK,
            'name'   => $fileName, // Требуется для тестирования.
        ]);

    }

}
