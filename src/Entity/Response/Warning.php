<?php declare(strict_types=1);

namespace FilipSedivy\EET\Entity\Response;

use FilipSedivy\EET\Enum;
use BadMethodCallException;

final class Warning
{
    private int $code;

    public function __construct(int $code)
    {
        $this->code = $code;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function findFromEnum(): ?string
    {
        $key = 'ITEM_';

        if ($this->code < 0) {
            $key .= '_' . ($this->code * -1);
        } else {
            $key .= $this->code;
        }

        try {
            $enum = Enum\Warning::$key();

            if ($enum instanceof Enum\Warning) {
                return $enum->getValue();
            }
        } catch (BadMethodCallException $exception) {
            return null;
        }

        return null;
    }
}