<?php

namespace App\Services\Uploader;

use Illuminate\Http\Request;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver as Receiver;

class FileReceiver extends Receiver
{

    public function __construct($fileIndexOrFile, Request $request, $handlerClass, $chunkStorage = null, $config = null)
    {
        parent::__construct($fileIndexOrFile, $request, $handlerClass, $chunkStorage, $config);
    }

    /**
     * Требуется для нужд тестирования (чтобы вытащить $chunkFileName).
     * @return AbstractHandler|null
     */
    public function getHandler(): ?AbstractHandler
    {
        return $this->handler;
    }

}
