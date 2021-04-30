<?php

namespace App\Exceptions;

use Exception;

class BadRequestException extends Exception
{
    /**
     * @param  string          $message
     * @param  int             $code
     * @param  Exception|null  $previous
     */
    public function __construct(string $message = 'Bad request error', int $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
