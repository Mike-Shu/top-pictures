<?php

namespace App\Exceptions;

use Exception;

class UploaderException extends Exception
{
    /**
     * @param  string          $message
     * @param  int             $code
     * @param  Exception|null  $previous
     */
    public function __construct(string $message = 'Uploader error', int $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
