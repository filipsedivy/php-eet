<?php declare(strict_types=1);

namespace FilipSedivy\EET\Tests\Utils;

use FilipSedivy\EET\Utils\Format;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public function testBkpFormat(): void
    {
        $format = Format::BKB('2fdabd7e8978870662e1d0ec2b51670ac6fedb32');
        $this->assertEquals('2fdabd7e-89788706-62e1d0ec-2b51670a-c6fedb32', $format);
    }

    public function testPriceFormat(): void
    {
        $this->assertEquals('123.00', Format::price(123));
        $this->assertEquals('123.40', Format::price(123.4));
        $this->assertEquals('123.45', Format::price(123.45));
        $this->assertEquals('123.46', Format::price(123.456));
        $this->assertEquals('123.45', Format::price(123.452));
    }
}