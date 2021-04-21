<?php

namespace Tests\Feature\Http\Controllers\Web;

use App\Models\User;
use App\Services\Uploader\UploaderService;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Tests\TestCase;

class UploaderTest extends TestCase
{

    private $storageDisk;
    private $storagePath;
    private $chunkStorageDisk;
    private $chunkStoragePath;

    /**
     * @var User
     */
    private $user;

    /**
     * @var File
     */
    private $file;

    public function setUp(): void
    {
        parent::setUp();

        // Куда будем загружать файл.
        $this->storageDisk = config('interface.uploading.storage.disk');
        $this->storagePath = config('interface.uploading.storage.path');

        // Куда будем загружать фрагмент.
        $this->chunkStorageDisk = config('chunk-upload.storage.disk');
        $this->chunkStoragePath = config('chunk-upload.storage.chunks');

        // Пользователь, от лица которого будем загружать.
        $this->user = User::factory()->create();

        // Файл, который будем загружать.
        $this->file = UploadedFile::fake()
            ->image('image.jpg', 3840, 2160)
            ->mimeType('image/jpeg');
    }

    /**
     * Убедимся, что для доступа к странице загрузки требуется аутентификация.
     */
    public function testAuthenticationRequired()
    {
        $response = $this
            ->get(
                route('upload_form')
            );

        $this->assertGuest();
        $response->assertRedirect(
            route('login')
        );
    }

    /**
     * Убедимся, страница для загрузки на месте.
     */
    public function testUploadScreenCanBeRendered(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->get(
                route('upload_form')
            );

        $response->assertOk();

        /**
         * "RefreshDatabase" не подходит, т.к. для проверки на дубли потребуется сохранить состояние БД.
         *
         * @see UploaderTest::testDuplicateFileException()
         */
        Artisan::call('migrate:fresh');
    }

    /**
     * Пробуем загрузить весь файл.
     */
    public function testFileCanBeUploaded(): void
    {
        Storage::fake($this->storageDisk);

        $response = $this
            ->actingAs($this->user)
            ->post(
                route('ajax_upload_file'),
                [
                    'file' => $this->file
                ]
            );

        $response
            ->assertOk()
            ->assertJson([
                'status' => UploaderService::STATUS_OK
            ]);

        $fileName = $response->json('name');
        $fileExt = $this->file->getClientOriginalExtension();
        $filePath = "{$this->storagePath}/{$fileName}.{$fileExt}";

        Storage::disk($this->storageDisk)
            ->assertExists($filePath)
            ->delete($filePath); // Не забудем удалить временный файл.
    }

    /**
     * Пробуем загрузить фрагмент файла.
     */
    public function testChunkCanBeUploaded(): void
    {
        Storage::fake($this->chunkStorageDisk);

        $postData = [
            'resumableChunkNumber' => 1,
            'resumableTotalChunks' => 2,
            'resumableIdentifier'  => 'file-5669438-1jpg',
            'resumableType'        => 'image/jpeg',
            'resumableFilename'    => 'image.jpg',
//            'resumableChunkSize'        => 1048576,
//            'resumableCurrentChunkSize' => 1048576,
//            'resumableTotalSize'        => 5669438,
        ];

        $response = $this
            ->actingAs($this->user)
            ->post(
                route('ajax_upload_file', $postData),
                [
                    'file' => $this->file
                ]
            );

        $response
            ->assertOk()
            ->assertJson([
                'status' => UploaderService::STATUS_OK
            ]);

        $fileName = $response->json('chunk');
        $filePath = "{$this->chunkStoragePath}/{$fileName}";

        Storage::disk($this->chunkStorageDisk)
            ->assertExists($filePath)
            ->delete($filePath); // Не забудем удалить временный файл.
    }

    /**
     * Проверяем "The request is missing a file".
     *
     * @see UploadMissingFileException
     */
    public function testMissingFileException(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->post(
                route('ajax_upload_file'),
                [
                    'wrong_file' => $this->file
                ]
            );

        $response
            ->assertOk()
            ->assertJson([
                'status' => UploaderService::STATUS_FAILED,
                'error'  => 400,
            ]);
    }

    /**
     * Проверяем "The file already exists".
     */
    public function testDuplicateFileException(): void
    {
        $response = $this
            ->actingAs($this->user)
            ->post(
                route('ajax_upload_file'),
                [
                    'file' => $this->file
                ]
            );

        $response
            ->assertOk()
            ->assertJson([
                'status' => UploaderService::STATUS_FAILED,
                'error'  => 409,
            ]);
    }

    /**
     * Проверяем "The file type is not allowed".
     */
    public function testFileTypeException(): void
    {
        $file = UploadedFile::fake()
            ->image('image.png')
            ->mimeType('image/png');

        $response = $this
            ->actingAs($this->user)
            ->post(
                route('ajax_upload_file'),
                [
                    'file' => $file
                ]
            );

        $response
            ->assertOk()
            ->assertJson([
                'status' => UploaderService::STATUS_FAILED,
                'error'  => 415,
            ]);

        Artisan::call('migrate:fresh');
    }

}
