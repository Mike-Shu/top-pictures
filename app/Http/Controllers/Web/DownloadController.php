<?php

namespace App\Http\Controllers\Web;

use App\Events\ImageDownloadedEvent;
use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Скачать указанное изображение.
     *
     * @param  Request  $request
     * @param  string   $name
     *
     * @return StreamedResponse
     */
    public function __invoke(Request $request, string $name): StreamedResponse
    {
        $image = Image::whereName($name)->first();

        if (empty($image)) {
            abort(404);
        }

        // Кидаем событие о скачанном файле.
        ImageDownloadedEvent::dispatch($image);

        // Возвращаем пользователю файл для скачивания.
        return $this->getImageResponse($image);
    }

    /**
     * Возвращает потоковый HTTP-ответ с файлом для скачивания.
     *
     * @param  Image  $image
     *
     * @return StreamedResponse
     */
    private function getImageResponse(Image $image): StreamedResponse
    {
        $storageDisk = Storage::disk(config('interface.uploading.storage.disk'));
        $storagePath = config('interface.uploading.storage.path');

        // Какой файл отдадим?
        $path = "{$storagePath}/{$image->name}.{$image->extension}";

        // С каким именем будем отдавать?
        $name = $this->getDownloadName($image);

        return $storageDisk->download($path, $name);
    }

    /**
     * Возвращает имя для файла, который будет отдан пользователю.
     *
     * @param  Image  $image
     *
     * @return string
     */
    private function getDownloadName(Image $image): string
    {
        if (!empty($image->description)) {

            $description = Str::slug($image->description, '_');

            $description = Str::limit(
                $description,
                config('interface.image.download_name_length')
            );

            return "{$description}.{$image->extension}";

        }

        return "{$image->name}.{$image->extension}";
    }
}
