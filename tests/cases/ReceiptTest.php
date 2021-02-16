<?php declare(strict_types=1);

namespace Tests\Cases;

use Exception;
use FilipSedivy\EET;
use Symfony\Component\Validator;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class ReceiptTest extends TestCase
{
    public function testHeader(): void
    {
        $receipt = new EET\Receipt;
        $receipt->uuid_zpravy = '8f5138bf-49e2-4ee9-9509-d75d01095609';

        $header = [
            'uuid_zpravy' => '8f5138bf-49e2-4ee9-9509-d75d01095609',
            'prvni_zaslani' => true
        ];

        Assert::same($receipt->buildHeader(), $header);
    }

    public function testEmptyCodes(): void
    {
        $receipt = new EET\Receipt;
        $exception = new EET\Exceptions\EET\ClientException($receipt, null, null, new Exception);

        Assert::null($exception->getPkp());
        Assert::null($exception->getBkp());
    }
}

(new ReceiptTest)->run();
