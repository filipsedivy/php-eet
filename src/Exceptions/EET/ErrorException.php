<?php declare(strict_types=1);

namespace XSuchy09\EET\Exceptions\EET;

use XSuchy09\EET\Exceptions\RuntimeException;

class ErrorException extends RuntimeException implements EETException
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
