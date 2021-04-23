<?php

namespace App\Events;

use App\Models\Image;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ImageDownloadedEvent
{
    use Dispatchable, SerializesModels;

    /**
     * @var Image
     */
    public $image;

    /**
     * @param  Image  $image
     */
    public function __construct(Image $image)
    {
        $this->image = $image;
    }
}
