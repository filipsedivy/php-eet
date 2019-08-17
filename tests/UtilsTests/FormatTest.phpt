<?php declare(strict_types=1);
/**
 * Test: FilipSedivy/Utils/Format.
 *
 * @testCase
 */

namespace UtilsTests;

use FilipSedivy\EET\Utils\Format;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class FormatTest extends TestCase
{
    public function testPriceFormat(): void
    {
        Assert::same(Format::price(123), '123.00');
        Assert::same(Format::price(123.4), '123.40');
        Assert::same(Format::price(123.45), '123.45');
    }
}

(new FormatTest())->run();
