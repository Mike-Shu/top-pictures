<?php

namespace App\Exceptions;

use Exception;

class NotFoundException extends Exception
{
    /**
     * @param  string          $message
     * @param  int             $code
     * @param  Exception|null  $previous
     */
    public function __construct(string $message = 'Not found error', int $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
