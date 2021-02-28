<?php declare(strict_types=1);

namespace FilipSedivy\EET\Exceptions\EET;

use FilipSedivy\EET\Entity;
use FilipSedivy\EET\Enum;
use FilipSedivy\EET\Exceptions\RuntimeException;

class ErrorException extends RuntimeException implements EETException
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code);
    }

    public static function fromErrorResponse(Entity\Response\Error $error): self
    {
        $code = 'ITEM_';
        if ($error->getCode() < 0) {
            $code .= '_' . ($error->getCode() * -1);
        } else {
            $code .= $error->getCode();
        }

        $enum = Enum\Error::$code();

        if ($enum instanceof Enum\Error) {
            return new self($enum->getValue(), $error->getCode());
        }

        return new self('', $error->getCode());
    }
}
