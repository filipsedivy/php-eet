<?php
/**
 * Test: FilipSedivy\EET\Utils.
 *
 * @testCase
 */

namespace EetTest\Utils;

use FilipSedivy\EET\Utils\UUID;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class UtilsTest extends TestCase
{
    public function testMinimumUUIDLength()
    {
        $uuid = UUID::v4();
        Assert::match('#^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$#i', $uuid);
    }
}

(new UtilsTest())->run();