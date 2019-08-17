<?php declare(strict_types=1);
/**
 * Test: FilipSedivy/EET/Receipt.
 *
 * @testCase
 */

namespace EETTest;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Exceptions\Receipt\ConstraintViolationException;
use FilipSedivy\EET\Receipt;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

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

        Assert::same($receipt->buildHeader(), $header);
    }

    public function testSendEmptyReceipt()
    {
        $certificate = new Certificate(__DIR__ . '/../Data/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new Dispatcher($certificate, Dispatcher::PLAYGROUND_SERVICE);

        Assert::exception(function () use ($dispatcher) {
            $receipt = new Receipt();
            $dispatcher->send($receipt);
        }, ConstraintViolationException::class);
    }
}

(new ReceiptTest())->run();
