<?php declare(strict_types=1);

namespace FilipSedivy\EET\Tests;

use FilipSedivy\EET\Exceptions;
use FilipSedivy\EET\Receipt;
use PHPUnit\Framework\TestCase;

class ReceiptTest extends TestCase
{
    public function testHeader(): void
    {
        $receipt = new Receipt();
        $receipt->uuid_zpravy = '8f5138bf-49e2-4ee9-9509-d75d01095609';

        $header = [
            'uuid_zpravy' => '8f5138bf-49e2-4ee9-9509-d75d01095609',
            'prvni_zaslani' => true
        ];

        $this->assertEquals($header, $receipt->buildHeader());
    }

    public function testEmptyCodes(): void
    {
        $receipt = new Receipt();
        $exception = new Exceptions\EET\ClientException($receipt, null, null, new \Exception());

        $this->assertNull($exception->getPkp());
        $this->assertNull($exception->getBkp());
    }
}