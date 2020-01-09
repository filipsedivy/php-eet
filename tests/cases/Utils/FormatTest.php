<?php declare(strict_types=1);

namespace Tests\Cases\Utils;

use FilipSedivy\EET;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../../bootstrap.php';

class FormatTest extends TestCase
{
    public function testPriceFormat(): void
    {
        Assert::same(EET\Utils\Format::price(123), '123.00');
        Assert::same(EET\Utils\Format::price(123.4), '123.40');
        Assert::same(EET\Utils\Format::price(123.45), '123.45');
    }

    public function testBkpFormat(): void
    {
        $format = EET\Utils\Format::BKB('2fdabd7e8978870662e1d0ec2b51670ac6fedb32');
        Assert::same($format, '2fdabd7e-89788706-62e1d0ec-2b51670a-c6fedb32');
    }
}

(new FormatTest)->run();
