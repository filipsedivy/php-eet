<?php declare(strict_types=1);

namespace FilipSedivy\EET\Tests\Utils;

use FilipSedivy\EET\Utils\UUID;
use PHPUnit\Framework\TestCase;

class UuidTest extends TestCase
{
    public function testFormat(): void
    {
        static $pattern = '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89AB][0-9a-f]{3}-[0-9a-f]{12}$/i';
        $iterator = 0;

        do {
            $this->assertRegExp($pattern, UUID::v4());
            $iterator++;
        } while ($iterator < 10);
    }
}