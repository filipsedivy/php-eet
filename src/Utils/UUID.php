<?php declare(strict_types=1);

namespace FilipSedivy\EET\Utils;

use Ramsey;

class UUID
{
    /**
     * @throws \Exception
     * @deprecated Use Ramsey\Uuid\Uuid::uuid4() instead
     */
    public static function v4(): string
    {
        return Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}