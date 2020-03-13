<?php declare(strict_types=1);

namespace Tests\Cases;

use DateTime;
use FilipSedivy\EET;
use Ramsey\Uuid\Uuid;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';


/**
 * Class PotvrzeniTest
 * @package Tests\Cases
 */
class PotvrzeniTest extends TestCase
{
    public function testPotvrzeni(): void
    {
        $receipt = $this->getValidReceipt();
        $certificate = new EET\Certificate(DATA_DIR . '/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new EET\Dispatcher($certificate, EET\Dispatcher::PLAYGROUND_SERVICE);

        $dispatcher->send($receipt);

        Assert::type(EET\Potvrzeni::class, $dispatcher->getPotvrzeni());
        Assert::same($receipt->uuid_zpravy, $dispatcher->getPotvrzeni()->uuid_zpravy);
        Assert::same($dispatcher->getCheckCodes($receipt)['bkp']['_'], $dispatcher->getPotvrzeni()->bkp);
        Assert::type('string', $dispatcher->getPotvrzeni()->fik);
        Assert::type(DateTime::class, $dispatcher->getPotvrzeni()->dat_prij);
        Assert::type('bool', $dispatcher->getPotvrzeni()->test);
        Assert::same(true, $dispatcher->getPotvrzeni()->test);
        Assert::type('array', $dispatcher->getPotvrzeni()->varovani);
    }

    private function getValidReceipt(): EET\Receipt
    {
        $receipt = new EET\Receipt;
        $receipt->uuid_zpravy = Uuid::uuid4()->toString();
        $receipt->id_provoz = '11';
        $receipt->id_pokl = 'IP105';
        $receipt->dic_popl = 'CZ00000019';
        $receipt->porad_cis = '1';
        $receipt->dat_trzby = new DateTime;
        $receipt->celk_trzba = 500;

        return $receipt;
    }
}

(new PotvrzeniTest)->run();
