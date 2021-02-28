<?php declare(strict_types=1);

namespace FilipSedivy\EET\Tests\Utils;

use FilipSedivy\EET\Utils\FileSystem;
use FilipSedivy\EET\Exceptions;
use PHPUnit\Framework\TestCase;

class FileSystemTest extends TestCase
{
    public function testOpenNonExistsFile(): void
    {
        $this->expectException(Exceptions\IOException::class);
        FileSystem::read(__DIR__ . '/not-exist-file');
    }
}