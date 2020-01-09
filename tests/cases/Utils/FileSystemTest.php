<?php declare(strict_types=1);

namespace Tests\Cases\Utils;

use FilipSedivy\EET\Exceptions;
use FilipSedivy\EET\Utils;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class FileSystemTest extends TestCase
{
    public function testNotExistFile(): void
    {
        Assert::exception(static function () {
            Utils\FileSystem::read(__DIR__ . '/not-exist-file');
        }, Exceptions\IOException::class);
    }
}

(new FileSystemTest)->run();
