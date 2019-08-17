<?php declare(strict_types=1);
/**
 * Test: FilipSedivy/Utils/FileSystem.
 *
 * @testCase
 */

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
        Assert::exception(function () {
            FileSystem::read(__DIR__ . '/not-exist-file');
        }, IOException::class);
    }
}

(new FileSystemTest())->run();
