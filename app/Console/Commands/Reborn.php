<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Storage;

/**
 * @package App\Console\Commands
 * @codeCoverageIgnore
 */
class Reborn extends Command
{
    /**
     * @var string
     */
    protected $signature = 'reborn';

    /**
     * @var string
     */
    protected $description = 'Перерождение: обнуление базы данных и удаление загруженных файлов.';

    /**
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return int
     */
    public function handle(): int
    {
        // Выполняем обнуление базы данных.
        $this->call('migrate:refresh', [
            '--seed' => 'default'
        ]);

        // Удаляем каталог для хранения фрагментов.
        $this->removePath(
            config('chunk-upload.storage.disk'),
            config('chunk-upload.storage.chunks'),
        );

        // Удаляем каталог для хранения загруженных изображений.
        $this->removePath(
            config('interface.uploading.storage.disk'),
            config('interface.uploading.storage.path'),
        );

        // Удаляем каталог для хранения миниатюр.
        $this->removePath(
            config('interface.uploading.thumbs.disk'),
            config('interface.uploading.thumbs.path'),
        );

        $this->info('File deletion completed successfully.');
        $this->info('Ok');

        return 0;
    }

    /**
     * @param  string  $disk
     * @param  string  $path
     */
    private function removePath(string $disk, string $path): void
    {
        try {

            Storage::disk($disk)
                ->deleteDirectory($path);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
