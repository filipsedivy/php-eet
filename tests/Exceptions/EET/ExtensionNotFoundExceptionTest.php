<?php declare(strict_types=1);

namespace Exceptions\EET;

use FilipSedivy\EET\Exceptions\ExtensionNotFound;
use PHPUnit\Framework\TestCase;

class ExtensionNotFoundExceptionTest extends TestCase
{
    public function testMessage(): void
    {
        $exception = new ExtensionNotFound('TestExtension', 99);

        $this->expectException(ExtensionNotFound::class);
        $this->expectExceptionMessage("Extension 'TestExtension' not found");
        $this->expectExceptionCode(99);

        throw $exception;
    }
}