<?php

namespace App\Exceptions;

use Exception;

class TelegramLoggerException extends Exception
{
    /**
     * @param  string          $message
     * @param  int             $code
     * @param  Exception|null  $previous
     */
    public function __construct(string $message = 'Telegram logger error', int $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
