<?php declare(strict_types=1);

namespace FilipSedivy\EET\Exceptions\EET;

use FilipSedivy\EET\Exceptions\RuntimeException;

class ErrorException extends RuntimeException implements EETException
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }
}
