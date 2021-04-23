<?php

namespace Tests\Feature\Http\Controllers\Web;

use App\Models\Image;
use App\Models\User;
use App\Services\Uploader\UploaderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DownloadTest extends TestCase
{
    use RefreshDatabase;

    private $storageDisk;
    private $storagePath;

    /**
     * @var User
     */
    private $user;

    /**
     * @var File
     */
    private $file;

    /**
     * @var string
     */
    private $filePath;

    protected function setUp(): void
    {
        parent::setUp();

        // Куда будем загружать тестовый файл.
        $this->storageDisk = config('interface.uploading.storage.disk');
        $this->storagePath = config('interface.uploading.storage.path');

        // Пользователь, от лица которого будем загружать.
        $this->user = User::factory()->create();

        // Файл, который будем загружать.
        $this->file = UploadedFile::fake()
            ->image('image.jpg', 320, 240)
            ->mimeType('image/jpeg');

        // Загрузим одно изображение.
        Image::truncate();
        $this->filePath = $this->uploadImage();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Не забудем удалить временный файл.
        Storage::disk($this->storageDisk)->delete($this->filePath);
    }

    /**
     * Доступное изображение может быть скачано.
     */
    public function test_available_image_can_be_downloaded(): void
    {
        $this->assertDatabaseCount('images', 1);

        $image = Image::first();

        $this->assertNotNull($image);

        // Скачаем изображение.
        $this->actingAs($this->user)
            ->get(
                route('download', $image->name)
            )
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertHeader('Content-Length', $image->size)
            ->assertHeader('Content-Disposition', 'attachment; filename=imagejpg.jpg')
            ->assertHeader('Cache-Control', 'no-cache, private');
    }

    /**
     * Доступное изображение с пустым описанием может быть скачано.
     */
    public function test_available_image_with_empty_description_can_be_downloaded(): void
    {
        $this->assertDatabaseCount('images', 1);

        $image = Image::first();

        $this->assertNotNull($image);

        $this->assertDatabaseHas('images', [
            'description' => 'image.jpg',
        ]);

        $image->description = '';
        $image->save();

        $this->assertDatabaseHas('images', [
            'description' => '',
        ]);

        // Скачаем изображение.
        $this->actingAs($this->user)
            ->get(
                route('download', $image->name)
            )
            ->assertOk()
            ->assertHeader('Content-Type', 'image/jpeg')
            ->assertHeader('Content-Length', $image->size)
            ->assertHeader('Content-Disposition', "attachment; filename={$image->name}.jpg")
            ->assertHeader('Cache-Control', 'no-cache, private');
    }

    /**
     * Недоступное изображение не может быть скачано.
     *
     * @throws \Exception
     */
    public function test_inaccessible_image_could_not_be_downloaded(): void
    {
        $this->assertDatabaseCount('images', 1);

        $image = Image::first();

        $this->assertNotNull($image);

        $this->assertNull($image->deleted_at);

        $image->delete();

        $this->assertNotNull($image->deleted_at);

        // Скачаем изображение.
        $this->actingAs($this->user)
            ->get(
                route('download', $image->name)
            )
            ->assertNotFound();
    }

    /**
     * Загружает одно изображение и возвращает относительный путь к файлу (в рамках диска).
     *
     * @return string
     */
    private function uploadImage(): string
    {
        Storage::fake($this->storageDisk);

        $response = $this
            ->actingAs($this->user)
            ->post(
                route('ajax_upload_file'),
                [
                    'file' => $this->file
                ]
            )
            ->assertOk()
            ->assertJson([
                'status' => UploaderService::STATUS_OK
            ]);

        $fileName = $response->json('name');
        $fileExt = $this->file->getClientOriginalExtension();
        $filePath = "{$this->storagePath}/{$fileName}.{$fileExt}";

        // Убедимся, что файл действительно был загружен.
        Storage::disk($this->storageDisk)->assertExists($filePath);

        return $filePath;
    }
}
