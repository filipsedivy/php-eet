<?php declare(strict_types=1);

namespace Tests\Cases;

use XSuchy09\EET\Exceptions;
use XSuchy09\EET\Utils;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class UtilsFileSystemTest extends TestCase
{
    public function testNotExistFile(): void
    {
        Assert::exception(static function () {
            Utils\FileSystem::read(__DIR__ . '/not-exist-file');
        }, Exceptions\IOException::class);
    }
}

(new UtilsFileSystemTest)->run();
