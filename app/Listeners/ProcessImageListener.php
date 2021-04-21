<?php

namespace App\Listeners;

use App\Events\ImageUploadedEvent;
use App\Services\Image\ProcessImageService;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProcessImageListener implements ShouldQueue
{
    /**
     * @var ProcessImageService
     */
    private $service;

    /**
     * @param  ProcessImageService  $service
     */
    public function __construct(ProcessImageService $service)
    {
        $this->service = $service;
    }

    /**
     * @param  ImageUploadedEvent  $event
     */
    public function handle(ImageUploadedEvent $event)
    {
        $this->service->setImage($event->image)
            ->makeThumbs()
            ->getPalette()
            ->processingComplete();
    }
}
