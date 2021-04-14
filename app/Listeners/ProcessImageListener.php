<?php

namespace App\Listeners;

use App\Events\ImageUploadedEvent;
use App\Models\Image;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ProcessImageListener implements ShouldQueue
{
    public function __construct()
    {
    }

    /**
     * @param  ImageUploadedEvent  $event
     */
    public function handle(ImageUploadedEvent $event)
    {
        $image = $event->image;
        $image->pending = false;
        $image->save();
    }
}
