<?php declare(strict_types=1);
/**
 * Test: FilipSedivy\EET\Dispatcher.
 *
 * @testCase
 */

namespace EETTest\Dispatcher;

use FilipSedivy\EET\Certificate;
use FilipSedivy\EET\Dispatcher;
use FilipSedivy\EET\Receipt;
use Tester\Assert;
use Tester\TestCase;

require_once __DIR__ . '/../bootstrap.php';

class DispatcherTest extends TestCase
{
    public function testSendReceipt(): void
    {
        $certificate = new Certificate(__DIR__ . '/../Data/EET_CA1_Playground-CZ00000019.p12', 'eet');
        $dispatcher = new Dispatcher($certificate, Dispatcher::PLAYGROUND_SERVICE);

        $r = new Receipt();
        $r->uuid_zpravy = \Ramsey\Uuid\Uuid::uuid4()->toString();
        $r->id_provoz = '11';
        $r->id_pokl = 'IP105';
        $r->dic_popl = 'CZ00000019';
        $r->porad_cis = '1';
        $r->dat_trzby = new \DateTime();
        $r->celk_trzba = 500;

        $dispatcher->send($r);

        Assert::type('string', $dispatcher->getFik());
        Assert::type('string', $dispatcher->getBkp());
    }
}

(new DispatcherTest())->run();
