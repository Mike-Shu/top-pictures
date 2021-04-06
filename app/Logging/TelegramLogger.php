<?php

namespace App\Logging;

use App\Services\Logger\TelegramHandlerService;
use Monolog\Logger;

class TelegramLogger
{

    /**
     * @param  array  $config
     *
     * @return Logger
     * @codeCoverageIgnore
     */
    public function __invoke(array $config): Logger
    {
        return new Logger(
            config('app.name'),
            [
                new TelegramHandlerService($config),
            ]
        );
    }
}
