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

require_once __DIR__.'/../bootstrap.php';

class UtilsTest extends TestCase
{
    public function testMinimumUUIDLength()
    {
        $uuid = UUID::v4();
        $lengthAssert = strlen($uuid) > 35;
        Assert::true($lengthAssert);
    }
}

(new UtilsTest())->run();