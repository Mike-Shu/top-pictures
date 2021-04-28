<?php

namespace App\Console\Commands;

use App\Models\Image;
use App\Services\Image\ProcessImageService;
use Illuminate\Console\Command;

/**
 * @codeCoverageIgnore
 * @package App\Console\Commands
 */
class ImageRecalculateAdditionalColors extends Command
{
    /**
     * @var string
     */
    protected $signature = 'refresh-colors:image';

    /**
     * @var string
     */
    protected $description = 'Пересчет дополнительных цветов для изображения.';

    private $service;

    /**
     * @param  ProcessImageService  $service
     */
    public function __construct(ProcessImageService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * @return int
     */
    public function handle(): int
    {
        $this->info('Recalculation of additional colors:');

        if ($this->confirm('Do you wish to continue?') === false) {
            return 0;
        }

        $this->withProgressBar(Image::cursor(), function ($_image) {

            $this->service
                ->setImage($_image)
                ->getPalette()
                ->processingComplete();

        });

        $this->newLine();
        $this->info('Recalculation completed successfully.');

        return 0;
    }
}
