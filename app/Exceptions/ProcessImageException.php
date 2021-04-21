<?php

namespace App\Exceptions;

use Exception;

/**
 * @codeCoverageIgnore
 *
 * @package App\Exceptions
 */
class ProcessImageException extends Exception
{
    /**
     * @param  string          $message
     * @param  int             $code
     * @param  Exception|null  $previous
     */
    public function __construct(string $message = 'Process image error', int $code = 500, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
