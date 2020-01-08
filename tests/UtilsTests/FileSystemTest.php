<?php declare(strict_types=1);

namespace UtilsTests;

use FilipSedivy\EET\Exceptions\IOException;
use FilipSedivy\EET\Utils\FileSystem;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class FileSystemTest extends TestCase
{
    public function testNotExistFile(): void
    {
        Assert::exception(static function () {
            FileSystem::read(__DIR__ . '/not-exist-file');
        }, IOException::class);
    }
}

(new FileSystemTest)->run();
