<?php

namespace Tests\Feature\Services\Logger;

use App\Exceptions\TelegramLoggerException;
use App\Services\Logger\TelegramHandlerService;

/**
 * Заглушка для тестирования ошибок при отправке сообщения.
 *
 * @package Tests\Feature\Services\Logger
 */
class TelegramHandlerServiceFailureStub extends TelegramHandlerService
{

    /**
     * @param  array  $config
     */
    public function __construct(array $config)
    {
        parent::__construct($config);
    }

    /**
     * Для тестирования ошибок переопределим метод так, чтобы он не мог самостоятельно перехватывать исключения.
     *
     * @param  array  $record
     *
     * @throws TelegramLoggerException
     */
    protected function write(array $record): void
    {
        $textChunks = $this->getFormattedChunks($record);
        $this->sendChunks($textChunks);
    }

}
