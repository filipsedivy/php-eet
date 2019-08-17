<?php declare(strict_types=1);
/**
 * Test: FilipSedivy/EET/Receipt.
 *
 * @testCase
 */

namespace EETTest;

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
}

(new ReceiptTest())->run();
