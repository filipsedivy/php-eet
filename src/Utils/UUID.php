<?php declare(strict_types=1);

namespace FilipSedivy\EET\Utils;

use Ramsey;

class UUID
{
    /**
     * @return string
     * @throws \Exception
     * @deprecated Use Ramsey\Uuid\Uuid::uuid4() instead
     */
    public static function v4(): string
    {
        trigger_error(__METHOD__ . '() is deprecated; use Ramsey\Uuid\Uuid::uuid4() instead.', E_USER_DEPRECATED);
        return Ramsey\Uuid\Uuid::uuid4()->toString();
    }
}