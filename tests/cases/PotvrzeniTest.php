<?php
/******************************************************************************
 * Author: Petr Suchy (xsuchy09) <suchy@wamos.cz> <https://www.wamos.cz>
 * Project: php-eet
 * Date: 13.03.20
 * Time: 12:11
 * Copyright: (c) Petr Suchy (xsuchy09) <suchy@wamos.cz> <http://www.wamos.cz>
 *****************************************************************************/


declare(strict_types=1);

namespace Tests\Cases;

use DateTime;
use Exception;
use FilipSedivy\EET;
use Nette\Schema\Expect;
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
        Expect::string($dispatcher->getPotvrzeni()->fik);
        Expect::string($dispatcher->getPotvrzeni()->dat_prij);
        Assert::type(DateTime::class, $dispatcher->getPotvrzeni()->dat_prij);
        Expect::bool($dispatcher->getPotvrzeni()->test);
        Assert::same(true, $dispatcher->getPotvrzeni()->test);
        Expect::array($dispatcher->getPotvrzeni()->varovani);
    }

    private function getValidReceipt(): EET\Receipt
    {
        $receipt = new EET\Receipt();
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
